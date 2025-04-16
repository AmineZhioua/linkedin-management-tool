<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ScheduledLinkedInPost;
use App\Http\Controllers\LinkedInController;
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
            $post->update(['status' => 'failed', 'error' => 'LinkedIn user or token not found']);
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

            // Uniform response parsing
            Log::info("Raw Response Before Parsing", ['response' => $response]);
            $responseData = is_array($response) ? $response : $response->getData(true);
            Log::info("Parsed Response Data", [
                'responseData' => $responseData,
                'httpCode' => $responseData['status'] ?? 'not_set',
                'errorMsg' => $responseData['error'] ?? 'not_set'
            ]);

            $httpCode = isset($responseData['status']) ? (int) $responseData['status'] : 500;
            $errorMsg = $responseData['error'] ?? 'Unknown error';

            if ($httpCode >= 200 && $httpCode < 300) {
                $post->update(['status' => 'posted']);
                
                if (in_array($post->type, ['image', 'video'])) {
                    Storage::disk('linkedin_media')->delete($content['file_path']);
                }
            } else {
                Log::error("Failed to post", ['code' => $httpCode, 'error' => $errorMsg]);
                $post->update(['status' => 'failed', 'error' => $errorMsg]);
            }
        } catch (\Exception $e) {
            Log::error("Error processing scheduled LinkedIn post {$post->id}: " . $e->getMessage());
            $post->update(['status' => 'failed', 'error' => $e->getMessage()]);
        }
    }
}