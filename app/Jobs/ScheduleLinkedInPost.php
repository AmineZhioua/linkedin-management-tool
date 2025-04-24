<?php
namespace App\Jobs;

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

    protected $scheduledPost;

    public function __construct(ScheduledLinkedInPost $scheduledPost)
    {
        $this->scheduledPost = $scheduledPost;
    }

    public function handle()
    {
        $post = $this->scheduledPost;
        $linkedinUser = $post->linkedinUser;

        if (!$linkedinUser || !$linkedinUser->linkedin_token) {
            Log::error("LinkedIn user or token not found for scheduled post {$post->id}");
            $post->update(['status' => 'failed', 'error' => 'LinkedIn user or token not found', 'job_id' => null]);
            return;
        }

        $linkedinController = new LinkedInController();

        try {
            $content = json_decode($post->content, true);
            $payload = [
                'token' => $linkedinUser->linkedin_token,
                'linkedin_id' => $linkedinUser->linkedin_id,
            ];

            switch ($post->type) {
                case 'text':
                    $response = $linkedinController->postTextOnly(array_merge($payload, [
                        'caption' => $content['text'],
                    ]));
                    break;

                case 'image':
                case 'video':
                    $fileStream = Storage::disk('linkedin_media')->readStream($content['file_path']);

                    // Register media
                    $registerRequest = Request::create('/linkedin/registermedia', 'POST', [
                        'token' => $linkedinUser->linkedin_token,
                        'linkedin_id' => $linkedinUser->linkedin_id,
                        'type' => $post->type,
                        'caption' => $content['caption'] ?? '',
                    ]);
                    $registerRequest->files->add(['media' => new \Illuminate\Http\UploadedFile(
                        stream_get_meta_data($fileStream)['uri'], 
                        $content['original_filename'],
                        null,
                        null,
                        true
                    )]);
                    $registerResponse = $linkedinController->registerMedia($registerRequest);
                    $registerData = json_decode($registerResponse->getContent(), true);

                    if ($registerData['status'] !== 200) {
                        throw new \Exception("Media registration failed: " . ($registerData['error'] ?? 'Unknown error'));
                    }

                    // Upload media
                    $uploadRequest = Request::create('/linkedin/upload-media-binary', 'POST', [
                        'token' => $linkedinUser->linkedin_token,
                        'upload_url' => $registerData['uploadUrl'],
                    ]);
                    $uploadRequest->files->add(['media' => new \Illuminate\Http\UploadedFile(
                        stream_get_meta_data($fileStream)['uri'], 
                        $content['original_filename'],
                        null,
                        null,
                        true
                    )]);
                    $uploadResponse = $linkedinController->uploadMediaBinary($uploadRequest);
                    $uploadData = json_decode($uploadResponse->getContent(), true);

                    if ($uploadData['status'] !== 200) {
                        throw new \Exception("Media upload failed: " . ($uploadData['error'] ?? 'Unknown error'));
                    }

                    // Share media
                    $response = $linkedinController->shareMedia([
                        'token' => $payload['token'],
                        'linkedin_id' => $payload['linkedin_id'],
                        'asset' => $registerData['asset'],
                        'caption' => $content['caption'] ?? null,
                        'media_type' => strtoupper($post->type),
                    ]);
                    break;

                case 'article':
                    $response = $linkedinController->shareArticle(array_merge($payload, [
                        'caption' => $content['caption'],
                        'article_url' => $content['url'],
                        'article_title' => $content['title'],
                        'article_description' => $content['description'],
                    ]));
                    Log::info("ShareArticle Raw Response", ['response' => $response]);
                    break;

                default:
                    throw new \Exception("Invalid post type: {$post->type}");
            }

            Log::info("Raw Response Before Parsing", ['response' => $response]);

            if (is_array($response)) {
                $responseData = $response;
            } elseif (method_exists($response, 'getData')) {
                $responseData = $response->getData(true);
            } else {
                $responseData = json_decode(json_encode($response), true);
            }

            Log::info("Parsed Response Data", [
                'responseData' => $responseData,
            ]);

            $httpCode = isset($responseData['http_code']) ? (int) $responseData['http_code'] : 500;
            $errorMsg = $responseData['error'] ?? 'Unknown error';

            $postUrn = null;
            if ($post->type === 'text' && isset($responseData['data']) && isset($responseData['data']['stdClass'])) {
                $postUrn = $responseData['data']['stdClass']->id ?? null;
            } else {
                $postUrn = $responseData['data']['id'] ?? null;
            }

            if ($httpCode >= 200 && $httpCode < 300) {
                $post->update([
                    'status' => 'posted',
                    'post_urn' => $postUrn,
                    'job_id' => null
                ]);
                
                Log::info("Scheduled LinkedIn post {$post->id} successfully posted", [
                    'post_type' => $post->type,
                    'post_urn' => $postUrn,
                    'http_code' => $httpCode,
                ]);
            } else {
                Log::error("Failed to post", ['response' => $responseData, 'error' => $errorMsg]);
                $post->update(['status' => 'failed', 'error' => $errorMsg, 'job_id' => null]);
            }
        } catch (\Exception $e) {
            Log::error("Error processing scheduled LinkedIn post {$post->id}: " . $e->getMessage());
            $post->update(['status' => 'failed', 'error' => $e->getMessage(), 'job_id' => null]);
        }
    }
}