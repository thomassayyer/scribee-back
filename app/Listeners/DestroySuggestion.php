<?php

namespace App\Listeners;

use App\Events\SuggestionAccepted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DestroySuggestion
{
    /**
     * Handle the event.
     *
     * @param  /App/Events/SuggestionAccepted  $event
     * @return void
     */
    public function handle(SuggestionAccepted $event)
    {
        $event->suggestion->delete();
    }
}
