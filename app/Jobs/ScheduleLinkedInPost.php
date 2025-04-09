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
                    $reflection = new \ReflectionMethod($linkedinController, 'shareMedia');
                    Log::info('shareMedia parameters:', array_map(function ($param) {
                        return $param->getName() . ': ' . ($param->getType() ? $param->getType()->getName() : 'no type');
                    }, $reflection->getParameters()));
                        $response = $linkedinController->shareMedia([
                            'token' => $payload['token'],
                            'linkedin_id' => $payload['linkedin_id'],
                            'asset' => $content['asset'],
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
                    break;
                    throw new \Exception("Invalid post type: {$post->type}");
            }

            // Check the response status using the callable methods on our object
            $httpCode = $response['status'];
            $responseData = $response['data'] ?? [];
            $errorMsg = $response['error'] ?? 'Unknown error';

            if ($httpCode >= 200 && $httpCode < 300) {
                $post->update(['status' => 'posted']);
            } else {
                Log::error("Failed to post", ['code' => $httpCode, 'error' => $errorMsg]);
                $post->update(['status' => 'failed', 'error' => $errorMsg]);
            }

        } catch (\Exception $e) {
            Log::error("Error processing scheduled LinkedIn post {$post->id}: " . $e->getMessage());
            $post->update([
                'status' => 'failed',
                'error' => $e->getMessage()
            ]);
        }
    }
}