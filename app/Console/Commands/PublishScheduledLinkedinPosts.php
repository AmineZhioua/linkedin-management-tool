<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ScheduleLinkedinText;
use App\Models\ScheduledLinkedinPost;
use Carbon\Carbon;


class PublishScheduledLinkedinPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:publish-scheduled-linkedin-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish LinkedIn posts that are scheduled for the current time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        
        // Find posts due for publishing
        $posts = ScheduledLinkedinPost::where('status', 'pending')->where('scheduled_time', '>=', $now)->get();
        
        $this->info("Found {$posts->count()} LinkedIn posts to publish");
        
        
        foreach ($posts as $post) {
            // Dispatch the job to publish
            ScheduleLinkedinText::dispatch(
                $post->id,
                $post->user->linkedin_token,
                $post->user_id,
                $post->linkedin_id,
                $post->caption,
                $post->media_url,
                $post->media_title,
                $post->file_type,
                $post->scheduled_time
            )->delay($post->scheduled_time);
            
            // Update status to processing
            $post->update([
                'status' => 'pending',
            ]);
            
            $this->info("Dispatched job for post {$post->id}");
        }
    }
}
