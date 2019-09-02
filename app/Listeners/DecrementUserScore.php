<?php

namespace App\Listeners;

use App\Events\SuggestionAccepted;

class DecrementUserScore
{
    /**
     * Handle the event.
     *
     * @param  /App/Events/SuggestionAccepted  $event
     * @return void
     */
    public function handle(SuggestionAccepted $event)
    {
        $event->user->score--;
        $event->user->save();
    }
}
