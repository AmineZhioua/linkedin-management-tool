<?php

namespace App\Events;

use App\Models\LinkedinCampaign;
use App\Models\ScheduledLinkedinPost;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostFailed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $post;

    /**
     * Create a new event instance.
     */
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
            new PrivateChannel('post-failed.' . $this->post->user_id),
        ];
    }

    /**
     * Get the event name for broadcasting.
     */
    public function broadcastAs()
    {
        return "PostFailed";
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        $campaign = $this->post->campaign_id ? LinkedinCampaign::find($this->post->campaign_id) : null;

        if ($campaign) {
            $message = "Campagne {$campaign->name}: Post {$this->post->id} de type {$this->post->type} a échoué lors de la publication !";
            $campaignData = [
                'campaign_id' => $campaign->id,
            ];
        } else {
            $message = "Post {$this->post->id} de type {$this->post->type} a échoué lors de la publication !";
            $campaignData = [
                'campaign_id' => null,
            ];
        }

        return [
            'user_id' => $this->post->user_id,
            'campaign_id' => $campaignData["campaign_id"],
            'linkedin_user_id' => $this->post->linkedin_user_id,
            'event_name' => 'PostFailed',
            'message' => $message,
        ];
    }
}