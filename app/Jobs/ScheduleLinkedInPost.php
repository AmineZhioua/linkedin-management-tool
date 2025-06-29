<?php

namespace App\Jobs;

use App\Events\PostFailed;
use App\Events\PostPosted;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ScheduledLinkedInPost;
use App\Http\Controllers\LinkedInController;
use App\Models\LinkedinCampaign;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ScheduleLinkedInPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var ScheduledLinkedInPost */
    protected $scheduledPost;

    public function __construct(ScheduledLinkedInPost $scheduledPost)
    {
        $this->scheduledPost = $scheduledPost;
    }

    public function handle()
    {
        $post = $this->scheduledPost;
        $linkedinUser = $post->linkedinUser;

        if (! $linkedinUser || ! $linkedinUser->linkedin_token) {
            Log::error("LinkedIn user or token not found for scheduled post {$post->id}");
            $post->update([
                'status' => 'failed',
                'error'  => 'LinkedIn user or token not found',
                'job_id' => null,
            ]);
            return;
        }

        $controller = new LinkedInController;

        try {
            $content = json_decode($post->content, true);
            $payload = [
                'token'       => $linkedinUser->linkedin_token,
                'linkedin_id' => $linkedinUser->linkedin_id,
            ];

            switch ($post->type) {
                case 'text':
                    $response = $controller->postTextOnly(array_merge($payload, [
                        'caption' => $content['text'],
                    ]));
                    
                    break;

                case 'image':
                case 'video':
                    $stream = Storage::disk('linkedin_media')->readStream($content['file_path']);

                    // register
                    $req = Request::create('/linkedin/registermedia', 'POST', [
                        'token'       => $payload['token'],
                        'linkedin_id' => $payload['linkedin_id'],
                        'type'        => $post->type,
                        'caption'     => $content['caption'] ?? '',
                    ]);
                    $req->files->set('media', new \Illuminate\Http\UploadedFile(
                        stream_get_meta_data($stream)['uri'],
                        $content['original_filename'],
                        null, null, true
                    ));
                    $regResp = $controller->registerMedia($req);
                    $regData = $regResp->getData(true);
                    if (($regData['status'] ?? 0) !== 200) {
                        throw new \Exception("Media registration failed: " . ($regData['error'] ?? 'Unknown'));
                    }

                    // upload
                    $upReq = Request::create('/linkedin/upload-media-binary', 'POST', [
                        'token'      => $payload['token'],
                        'upload_url' => $regData['uploadUrl'],
                    ]);
                    $upReq->files->set('media', new \Illuminate\Http\UploadedFile(
                        stream_get_meta_data($stream)['uri'],
                        $content['original_filename'],
                        null, null, true
                    ));
                    $upResp = $controller->uploadMediaBinary($upReq);
                    $upData = $upResp->getData(true);
                    if (($upData['status'] ?? 0) !== 200) {
                        throw new \Exception("Media upload failed: " . ($upData['error'] ?? 'Unknown'));
                    }

                    // share
                    $response = $controller->shareMedia([
                        'token'       => $payload['token'],
                        'linkedin_id' => $payload['linkedin_id'],
                        'asset'       => $regData['asset'],
                        'caption'     => $content['caption'] ?? null,
                        'media_type'  => strtoupper($post->type),
                    ]);
                    break;

                case 'multiimage':
                    $imageUrns = [];
                
                    $req = Request::create('/linkedin/register-multi-image', 'POST', [
                        'token'       => $payload['token'],
                        'linkedin_id' => $payload['linkedin_id'],
                        'type'        => 'multiimage',
                        'content'     => $content,
                        'caption'     => $content['caption'] ?? null,
                    ]);
                
                    $files = [];
                    foreach ($content['file_paths'] as $index => $filePath) {
                        $stream = Storage::disk('linkedin_media')->readStream($filePath);
                        $originalName = $content['original_filenames'][$index] ?? pathinfo($filePath, PATHINFO_BASENAME);
                
                        $files[$index] = new \Illuminate\Http\UploadedFile(
                            stream_get_meta_data($stream)['uri'],
                            $originalName,
                            null,
                            null,
                            true
                        );
                        
                        $req->request->set("content.original_filenames.{$index}", $originalName);
                    }
                    
                    // Set the files correctly - this is the key fix
                    $req->files->set('content', ['files' => $files]);
                
                    // Register all images at once
                    $regResp = $controller->registerMultiImageMedia($req);
                    $regData = $regResp->getData(true);
                
                    if (($regData['status'] ?? 0) !== 200) {
                        throw new \Exception("Media registration failed: " . ($regData['error'] ?? 'Unknown'));
                    }
                
                    foreach ($regData['data'] as $result) {
                        $uploadUrl = $result['uploadUrl'];
                        $imageUrn = $result['imageUrn'];
                
                        // Find corresponding file path and original name
                        $index = array_search($imageUrn, array_column($regData['data'], 'imageUrn'));
                        $filePath = $content['file_paths'][$index];
                        $originalName = $content['original_filenames'][$index] ?? pathinfo($filePath, PATHINFO_BASENAME);
                        $stream = Storage::disk('linkedin_media')->readStream($filePath);
                
                        // Upload the image
                        $upReq = Request::create('/linkedin/upload-media-binary', 'POST', [
                            'token'      => $payload['token'],
                            'upload_url' => $uploadUrl,
                        ]);
                        $upReq->files->set('media', new \Illuminate\Http\UploadedFile(
                            stream_get_meta_data($stream)['uri'],
                            $originalName,
                            null,
                            null,
                            true
                        ));
                        $upResp = $controller->uploadMediaBinary($upReq);
                        $upData = $upResp->getData(true);
                
                        if (($upData['status'] ?? 0) !== 200) {
                            throw new \Exception("Media upload failed for {$filePath}: " . ($upData['error'] ?? 'Unknown'));
                        }
                
                        $imageUrns[] = $imageUrn;
                    }
                
                    $response = $controller->shareMultiImage([
                        'token'       => $payload['token'],
                        'linkedin_id' => $payload['linkedin_id'],
                        'images_urn'  => $imageUrns,
                        'caption'     => $content['caption'] ?? '',
                    ]);
                    break;

                case 'article':
                    $response = $controller->shareArticle(array_merge($payload, [
                        'caption'             => $content['caption'],
                        'article_url'         => $content['url'],
                        'article_title'       => $content['title'],
                        'article_description' => $content['description'],
                    ]));
                    break;

                default:
                    throw new \Exception("Invalid post type: {$post->type}");
            }

            Log::info("Raw controller response for post {$post->id}", ['response' => $response]);

            if ($response instanceof \Illuminate\Http\JsonResponse) {
                $body = $response->getData(true);
            } elseif (is_array($response)) {
                $body = $response;
            } else {
                $body = json_decode(json_encode($response), true);
            }

            Log::info("Parsed response body", ['body' => $body]);

            // 3. Determine HTTP code & URN:
            $httpCode = isset($body['status']) ? (int)$body['status'] : 500;
            $postUrn = $body['urn'] ?? ($body['data']['response']['data']['stdClass']['id'] ?? null) ?? ($body['data']['id'] ?? null);
            $errorMsg = $body['error'] ?? null;

            // 4. Success vs Failure
            if ($httpCode >= 200 && $httpCode < 300) {
                $post->update([
                    'status'   => 'posted',
                    'post_urn' => $postUrn,
                    'job_id'   => null,
                ]);
                Log::info("Post {$post->id} posted → URN {$postUrn}", [
                    'http_code' => $httpCode,
                ]);

                // $campaign = LinkedinCampaign::find($post->campaign_id)->first();

                event(new PostPosted($post));
                
            } else {
                Log::error("Failed to post {$post->id}", [
                    'http_code' => $httpCode,
                    'error'     => $errorMsg,
                ]);
                $post->update([
                    'status' => 'failed',
                    'error'  => $errorMsg,
                    'job_id' => null,
                ]);

                event(new PostFailed($post));
            }

        } catch (\Exception $e) {
            Log::error("Exception in ScheduleLinkedInPost for {$post->id}", [
                'message' => $e->getMessage(),
                'stack'   => $e->getTraceAsString(),
            ]);
            $post->update([
                'status' => 'failed',
                'error'  => $e->getMessage(),
                'job_id' => null,
            ]);
            
            event(new PostFailed($post));
        }
    }
}
