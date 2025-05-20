<?php

namespace App\Jobs;

use App\Events\CampaignCompleted;
use App\Models\LinkedinCampaign;
use App\Models\ScheduledLinkedinPost;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class CheckCampaignEndStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $campaign;

    public function __construct(LinkedinCampaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function handle(): void
    {
        try {
            if (in_array($this->campaign->status, ['completed', 'failed'])) {
                Log::info('Campaign already finalized, skipping job', [
                    'campaign_id' => $this->campaign->id
                ]);

                return;
            }

            $now = Carbon::now();
            $endDate = Carbon::parse($this->campaign->end_date);

            if ($endDate <= $now) {
                event(new CampaignCompleted($this->campaign));

                $failedPostsCount = ScheduledLinkedinPost::where("campaign_id", $this->campaign->id)
                    ->where('status', 'failed')
                    ->count();

                $this->campaign->status = $failedPostsCount >= 1 ? 'failed' : 'completed';
                $this->campaign->save();
            } else {
                // Re-queue with exact time until end date
                $secondsUntilEnd = $now->diffInSeconds($endDate, false);
                $this->release($secondsUntilEnd);
            }
        } catch (\Exception $e) {
            Log::error('Failed to check campaign end status', [
                'campaign_id' => $this->campaign->id,
                'error' => $e->getMessage(),
            ]);
            $this->fail($e);
        }
    }
}