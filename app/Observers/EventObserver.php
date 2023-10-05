<?php

namespace App\Observers;

use App\Models\Event;

class EventObserver
{
    /**
     * Handle the Event "deleting" event.
     *
     * @param  \App\Models\Event  $event
     * @return void
     */
    public function deleting(Event $event)
    {
        $event->users()->detach();
    }
}
