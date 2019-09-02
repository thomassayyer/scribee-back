<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;

class SuggestionsCreatedTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test the behavior of throwing a App/Events/SuggestionsCreated event.
     * 
     * @return void
     */
    public function testHandle()
    {
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
        ]);
        $suggestions = factory(App\Suggestion::class, 5)->create();
        
        event(new App\Events\SuggestionsCreated($suggestions, $user));

        $this->seeInDatabase('users', [
            'pseudo' => 'johndoe',
            'score' => 5,
        ]);
    }
}
