<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;

class SuggestionAcceptedTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test the behavior of throwing a App/Events/SuggestionAccepted event.
     * 
     * @return void
     */
    public function test()
    {
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
            'score' => 5,
        ]);
        $suggestion = factory(App\Suggestion::class)->create();
        
        event(new App\Events\SuggestionAccepted($suggestion, $user));

        $this->seeInDatabase('users', [
            'pseudo' => 'johndoe',
            'score' => 4,
        ]);
    }
}
