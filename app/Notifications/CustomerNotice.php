<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/** One customer notification, stored in the database. Shown in the bell and on /notifications. */
class CustomerNotice extends Notification
{
    /**
     * @param  string  $url  A path inside this site, such as /orders/5 (never a full external address).
     * @param  string  $category  order | payment | points | buyforme (controls the icon colour)
     */
    public function __construct(
        public readonly string $title,
        public readonly string $message,
        public readonly string $url,
        public readonly string $icon = 'bi-bell',
        public readonly string $category = 'order',
    ) {
    }

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, string> */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'icon' => $this->icon,
            'category' => $this->category,
        ];
    }
}