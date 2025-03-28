<?php

namespace App\Events;

use App\Models\Appointment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AppointmentEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Appointment $appointment)
    {
        Log::info('Appointment Event Fired: ', ['appointment' => $appointment]);

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn():Channel|PrivateChannel
    {
        Log::info('Broadcasting' . $this->appointment->schedule->admin->id);

        return new PrivateChannel('appointments.admin.'.$this->appointment->schedule->admin->id);
    }

    public function broadcastAs():string
    {
        return 'appointment.update';
    }
}
