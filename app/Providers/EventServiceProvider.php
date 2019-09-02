<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\SuggestionAccepted' => [
            'App\Listeners\DestroySuggestion',
            'App\Listeners\DecrementUserScore',
        ],
        'App\Events\SuggestionsCreated' => [
            'App\Listeners\IncrementUserScore',
        ]
    ];
}
