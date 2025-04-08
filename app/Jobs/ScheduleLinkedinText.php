<?php

namespace App\Jobs;

use App\Models\ScheduledLinkedinPost;
use App\Http\Controllers\LinkedinController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;


use Exception;

class ScheduleLinkedinText implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $scheduled_post_id;
    public $token;
    public $user_id;
    public $linkedin_id;
    public $type;
    public $caption;
    public $media_url;
    public $media_title;
    public $file_type;
    public $scheduled_time;
    public $status;
    public $error;

    /**
     * Create a new job instance.
     */
    public function __construct($scheduled_post_id, $token, $user_id, $linkedin_id, $type, $caption, $media_url, $media_title, $file_type, $scheduled_time)
    {
        $this->scheduled_post_id = $scheduled_post_id;
        $this->token = $token;
        $this->user_id = $user_id;
        $this->linkedin_id = $linkedin_id;
        $this->type = $type;
        $this->caption = $caption;
        $this->media_url = $media_url;
        $this->media_title = $media_title;
        $this->file_type = $file_type;
        $this->scheduled_time = $scheduled_time;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $scheduled_post = ScheduledLinkedinPost::find($this->scheduled_post_id);

        if (!$scheduled_post) {
            Log::error("Scheduled post #{$this->scheduled_post_id} not found");
            return;
        }

        try {
            $linkedin_controller = new LinkedinController();
            $result = $linkedin_controller->postTextOnly($this->token, $this->linkedin_id, $this->caption);

            if ($result['status'] == "201") {
                $scheduled_post->update([
                    'status' => 'success',
                    'post_urn' => $result['response']['id'] ?? null,
                    'error' => null,
                ]);
            } else {
                $scheduled_post->update([
                    'status' => 'failed',
                    'error' => json_encode($result),
                ]);
            }
        } catch(Exception $e) {
            Log::error("Error publishing scheduled LinkedIn post: " . $e->getMessage());
            $scheduled_post->update([
                'errors' => json_encode($e),
                'statut' => "failed",
            ]);
        }
    }
}
