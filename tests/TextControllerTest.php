<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Illuminate\Support\Str;

class TextControllerTest extends TestCase
{
    use DatabaseMigrations;

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
}