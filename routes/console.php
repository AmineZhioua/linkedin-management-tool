<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\ScheduleLinkedInPost;
use App\Models\ScheduledLinkedinPost;

// Existing artisan command
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule LinkedIn posts every minute
Schedule::everyMinute()->call(function () {
    $posts = ScheduledLinkedinPost::where('status', 'queued')
        ->where('scheduled_time', '<=', now())
        ->get();

    foreach ($posts as $post) {
        ScheduleLinkedInPost::dispatch($post);
    }
});
