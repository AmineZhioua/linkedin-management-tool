<?php

namespace App\Events;

use App\Models\LinkedinCampaign;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CampaignCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    public $campaign;

    public function __construct(LinkedinCampaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('campaign-completed.'.$this->campaign->user_id),
        ];
    }



    public function broadcastAs() {
        return 'CampaignCompleted';
    }


    public function broadcastWith(): array {
        return [
            'campaign_id' => $this->campaign->id,
            'user_id' => $this->campaign->user_id,
            'linkedin_user_id' => $this->campaign->linkedin_user_id,
            'event_name' => 'CampaignCompleted',
            'message' => "Votre campagne '{$this->campaign->name}' a été bien complété à {$this->campaign->end_date}!"
        ];
    }
}
