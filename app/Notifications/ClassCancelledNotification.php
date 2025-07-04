<?php

namespace App\Notifications;

use App\Models\ScheduledClass;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ClassCancelledNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public ScheduledClass $scheduledClass)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Sorry, your class was cancelled')
            ->greeting('Dear '.$notifiable->name.',')
            ->line(new HtmlString(sprintf(
                'We regret to inform you that your <em>%s</em> class on <em>%s</em> has been cancelled. We apologize for any inconvenience this may cause.',
                e($this->scheduledClass->classType->name),
                e($this->scheduledClass->scheduled_at->format('jS F @ h:i'))
            )))
            ->action('Book a Class', route('member.booking.create'))
            ->line('Thank you for your understanding.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
