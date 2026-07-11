<?php

namespace App\Notifications;

use App\Models\SocialAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScrapeCompletedNotification extends Notification
{
    use Queueable;

    public $account;
    public $postsFound;
    public $status; // 'success' or 'error'

    /**
     * Create a new notification instance.
     */
    public function __construct(SocialAccount $account, int $postsFound = 0, string $status = 'success')
    {
        $this->account = $account;
        $this->postsFound = $postsFound;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database']; // We only want UI notifications
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'social_account_id' => $this->account->id,
            'account_name' => $this->account->account_name,
            'status' => $this->status,
            'posts_found' => $this->postsFound,
            'message' => $this->status === 'success' 
                ? "Successfully scraped {$this->postsFound} new posts for {$this->account->account_name}."
                : "Failed to scrape {$this->account->account_name}."
        ];
    }
}

