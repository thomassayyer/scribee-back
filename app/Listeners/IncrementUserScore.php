<?php

namespace App\Listeners;

use App\Events\SuggestionsCreated;

class IncrementUserScore
{
    /**
     * Handle the event.
     *
     * @param  /App/Events/SuggestionsCreated  $event
     * @return void
     */
    public function handle(SuggestionsCreated $event)
    {
        $event->user->score += $event->suggestions->count();
        $event->user->save();
    }
}
