<?php

namespace App\Events;

use App\Models\LinkedinCampaign;
use App\Models\ScheduledLinkedinPost;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostPosted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    public $post;

    public function __construct(ScheduledLinkedinPost $post)
    {
        $this->post = $post;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('post-posted.'.$this->post->user_id),
        ];
    }

    public function broadcastAs() {
        return "PostPosted";
    }


    public function broadcastWith(): array
    {
        $campaign = $this->post->campaign_id ? LinkedinCampaign::find($this->post->campaign_id) : null;

        if ($campaign) {
            $message = "Campagne {$campaign->name}: Post {$this->post->id} de type {$this->post->type} a été publié avec succès !";
            $campaignData = [
                'campaign_id' => $campaign->id,
            ];
        } else {
            $message = "Post {$this->post->id} de type {$this->post->type} a été publié avec succès !";
            $campaignData = [
                'campaign_id' => null,
            ];
        }

        return [
            'user_id' => $this->post->user_id,
            'campaign_id' => $campaignData["campaign_id"],
            'linkedin_user_id' => $this->post->linkedin_user_id,
            'event_name' => 'PostPosted',
            'message' => $message
        ];
    }
}
