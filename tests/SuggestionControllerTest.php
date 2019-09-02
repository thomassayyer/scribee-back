<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class SuggestionControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test the behavior of performing a POST HTTP request to /api/texts/{id}/suggestions.
     *
     * @return void
     */
    public function testCreate()
    {
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
        ]);
        factory(App\Text::class)->create([
            'id' => 1,
        ]);

        $guestFailure = $this->call('POST', 'api/texts/1/suggestions', [
            'suggestions' => [
                [ 'original' => 'lorem', 'suggestion' => 'ipsum' ],
                [ 'original' => 'dolor', 'suggestion' => 'sit' ],
            ],
        ]);

        $this->actingAs($user);
        $wrongSuggestions = $this->call('POST', 'api/texts/2/suggestions', [
            'suggestions' => 'lorem',
        ]);
        $textNotFound = $this->call('POST', 'api/texts/2/suggestions', [
            'suggestions' => [
                [ 'original' => 'lorem', 'suggestion' => 'ipsum' ],
                [ 'original' => 'dolor', 'suggestion' => 'sit' ],
            ],
        ]);
        $success = $this->call('POST', 'api/texts/1/suggestions', [
            'suggestions' => [
                [ 'original' => 'lorem', 'suggestion' => 'ipsum' ],
                [ 'original' => 'dolor', 'suggestion' => 'sit' ],
            ],
        ]);

        $this->assertEquals(401, $guestFailure->status());
        $this->assertEquals(422, $wrongSuggestions->status());
        $this->assertEquals(404, $textNotFound->status());
        $this->assertEquals(201, $success->status());
        $this->seeInDatabase('suggestions', [
            'original' => 'lorem',
            'suggestion' => 'ipsum',
            'text_id' => 1,
            'user_pseudo' => 'johndoe',
        ]);
        $this->seeInDatabase('suggestions', [
            'original' => 'dolor',
            'suggestion' => 'sit',
            'text_id' => 1,
            'user_pseudo' => 'johndoe',
        ]);
    }

    /**
     * Test the behavior of performing a DELETE HTTP request to /api/suggestions/{id}.
     * 
     * @return void
     */
    public function testDestroy()
    {
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
        ]);
        factory(App\Text::class)->create([
            'id' => 1,
            'user_pseudo' => 'johndoe',
        ])->suggestions()->create([
            'id' => 1,
            'original' => 'lorem',
            'suggestion' => 'ipsum',
            'user_pseudo' => 'johndoe',
        ]);
        factory(App\Text::class)->create([
            'id' => 2,
        ])->suggestions()->create([
            'id' => 2,
            'original' => 'dolor',
            'suggestion' => 'sit',
            'user_pseudo' => 'johndoe',
        ]);

        $guestFailure = $this->call('DELETE', 'api/suggestions/1');

        $this->actingAs($user);

        $suggestionNotFound = $this->call('DELETE', 'api/suggestions/3');
        $notOwnText = $this->call('DELETE', 'api/suggestions/2');
        $success = $this->call('DELETE', 'api/suggestions/1');

        $this->assertEquals(401, $guestFailure->status());
        $this->assertEquals(404, $suggestionNotFound->status());
        $this->assertEquals(401, $notOwnText->status());
        $this->assertEquals(200, $success->status());
    }
}
