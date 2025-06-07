<?php

namespace App\Listeners;

use App\Events\AddUserRequestEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\EventService;

class AddUserRequestListener implements ShouldQueue
{

    private EventService $eventService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->eventService = new EventService();
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\AddUserRequestEvent  $event
     * @return void
     */
    public function handle(AddUserRequestEvent $event)
    {
        $this->eventService->addEvent($event->number);
    }
}
