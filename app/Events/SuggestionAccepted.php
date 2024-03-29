<?php

namespace App\Events;

use App\User;
use App\Suggestion;
use Illuminate\Queue\SerializesModels;

class SuggestionAccepted extends Event
{
    use SerializesModels;

    /**
     * The suggestion that has been accepted.
     * 
     * @var /App/Suggestion
     */
    public $suggestion;

    /**
     * The user who created the suggestions.
     * 
     * @var /App/User
     */
    public $user;

    /**
     * Create a new event instance.
     * 
     * @param  /App/Suggestion  $suggestion
     * @param  /App/User  $user
     * @return void
     */
    public function __construct(Suggestion $suggestion, User $user)
    {
        $this->suggestion = $suggestion;
        $this->user = $user;
    }
}
