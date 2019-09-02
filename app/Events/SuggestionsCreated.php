<?php

namespace App\Events;

use App\User;
use Illuminate\Queue\SerializesModels;

class SuggestionsCreated extends Event
{
    use SerializesModels;

    /**
     * The suggestions that has been created.
     * 
     * @var /Illuminate/Database/Eloquent/Collection
     */
    public $suggestions;

    /**
     * The user who created the suggestions.
     * 
     * @var /App/User
     */
    public $user;

    /**
     * Create a new event instance.
     * 
     * @param  /Illuminate/Database/Eloquent/Collection  $suggestions
     * @param  /App/User  $user
     * @return void
     */
    public function __construct($suggestions, User $user)
    {
        $this->suggestions = $suggestions;
        $this->user = $user;
    }
}
