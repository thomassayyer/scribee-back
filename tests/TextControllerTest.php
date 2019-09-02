<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Illuminate\Support\Str;

class TextControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test the behavior of performing a GET HTTP request to /api/texts.
     *
     * @return void
     */
    public function testIndex()
    {
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
        ]);
        $texts = factory(App\Text::class, 5)->create([
            'user_pseudo' => 'johndoe',
        ]);
        factory(App\Text::class, 5)->create();

        $guestFailure = $this->call('GET', 'api/texts');
        $success = $this->actingAs($user)->call('GET', 'api/texts');

        $this->assertEquals(401, $guestFailure->status());
        $this->assertEquals(200, $success->status());
        $this->assertEquals(5, count(json_decode($success->content())));
    }

    /**
     * Test the behavior of performing a POST HTTP request to /api/texts.
     *
     * @return void
     */
    public function testCreate()
    {
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
        ]);
        factory(App\Community::class)->create([
            'pseudo' => 'lorem',
        ]);

        $guestFailure = $this->call('POST', 'api/texts', [
            'text' => 'Lorem',
            'community_pseudo' => 'lorem',
        ]);

        $this->actingAs($user);
        $wrongText = $this->call('POST', 'api/texts', [
            'text' => Str::random(50000),
            'community_pseudo' => 'lorem'
        ]);
        $wrongCommunity = $this->call('POST', 'api/texts', [
            'text' => 'Lorem',
            'community_pseudo' => 'lorem12',
        ]);
        $success = $this->call('POST', 'api/texts', [
            'text' => 'Lorem',
            'community_pseudo' => 'lorem',
        ]);

        $this->assertEquals(401, $guestFailure->status());
        $this->assertEquals(422, $wrongText->status());
        $this->assertEquals(422, $wrongCommunity->status());
        $this->assertEquals(201, $success->status());
        $this->seeInDatabase('texts', [
            'text' => 'Lorem',
            'community_pseudo' => 'lorem',
            'user_pseudo' => 'johndoe',
        ]);
    }

    /**
     * Test the behavior of performing a POST HTTP request to /api/texts/{id}/suggestions.
     *
     * @return void
     */
    public function testCreateSuggestions()
    {
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
        ]);
        $text = factory(App\Text::class)->create([
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
     * Test the behavior of performing a DELETE HTTP request to /api/texts/{id}.
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
        ]);
        factory(App\Text::class)->create([
            'id' => 2,
        ]);

        $guestFailure = $this->call('DELETE', 'api/texts/1');

        $this->actingAs($user);

        $textNotFound = $this->call('DELETE', 'api/texts/3');
        $notOwnText = $this->call('DELETE', 'api/texts/2');
        $success = $this->call('DELETE', 'api/texts/1');

        $this->assertEquals(401, $guestFailure->status());
        $this->assertEquals(404, $textNotFound->status());
        $this->assertEquals(401, $notOwnText->status());
        $this->assertEquals(200, $success->status());
    }
}
