<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentStoreNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Appointment $appointment)
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
        return ['database','mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title'=>'Appointment is set.',
            'message'=>"An appointment has been updated set by: {$this->appointment->client->name} at date: {$this->appointment->date} and time:{$this->appointment->schedule->slot->start}-{$this->appointment->schedule->slot->end}",
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
        ->subject('New Appointment Scheduled')
        ->greeting("Hello, {$notifiable->name}")
        ->line("A new appointment has been scheduled.")
        ->line("**Client:** {$this->appointment->client->name}")
        ->line("**Date:** {$this->appointment->date}")
        ->line("**Time:** {$this->appointment->schedule->slot->start} - {$this->appointment->schedule->slot->end}")
        ->line('Thank you for using our application!');
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
