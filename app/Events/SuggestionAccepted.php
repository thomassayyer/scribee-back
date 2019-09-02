<?php

namespace App\Events;

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
     * Create a new event instance.
     * 
     * @param  /App/Suggestion  $suggestion
     * @return void
     */
    public function __construct(Suggestion $suggestion)
    {
        $this->suggestion = $suggestion;
    }
}
