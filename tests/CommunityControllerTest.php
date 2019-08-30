<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Illuminate\Support\Str;

class CommunityControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test the behavior of performing a GET HTTP request to /api/communities.
     *
     * @return void
     */
    public function testIndex()
    {
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
        ]);
        $texts = factory(App\Community::class, 5)->create([
            'user_pseudo' => 'johndoe',
        ]);
        factory(App\Community::class, 5)->create();

        $guestFailure = $this->call('GET', 'api/communities');
        $success = $this->actingAs($user)->call('GET', 'api/communities');

        $this->assertEquals(401, $guestFailure->status());
        $this->assertEquals(200, $success->status());
        $this->assertEquals(5, count(json_decode($success->content())));
    }

    /**
     * Test the behavior of performing a GET HTTP request to /api/communities/{pseudo}.
     *
     * @return void
     */
    public function testShow()
    {
        $user = factory(App\User::class)->create();
        $community = factory(App\Community::class)->create([
            'pseudo' => 'lorem',
        ]);

        $guestFailure = $this->call('GET', 'api/communities/lorem');
        $this->actingAs($user);
        $success = $this->call('GET', 'api/communities/lorem');
        $failure = $this->call('GET', 'api/communities/ipsum');

        $this->assertEquals(401, $guestFailure->status());
        $this->assertEquals(200, $success->status());
        $this->assertEquals(404, $failure->status());
    }

    /**
     * Test the behavior of performing a GET HTTP request to /api/communities/search.
     *
     * @return void
     */
    public function testSearch()
    {
        $user = factory(App\User::class)->create();
        $community = factory(App\Community::class)->create([
            'pseudo' => 'lorem',
            'name' => 'Lorem ipsum',
        ]);
        factory(App\Community::class, 10)->create([
            'name' => 'Doro',
        ])->each(function ($c) {
            $c->pseudo = 'i'.$c->pseudo;
            $c->save();
        });

        $guestFailure = $this->call('GET', 'api/communities/search', ['query' => 'lor']);
        $this->actingAs($user);
        $successPseudo = $this->call('GET', 'api/communities/search', ['query' => 'lor']);
        $successName = $this->call('GET', 'api/communities/search', ['query' => 'Lorem ips']);
        $successMultiple = $this->call('GET', 'api/communities/search', ['query' => 'Doro']);
        $failure = $this->call('GET', 'api/communities/search', ['query' => 'johndoe']);

        $this->assertEquals(200, $successPseudo->status());
        $this->assertEquals(200, $successName->status());
        $this->assertEquals(200, $successMultiple->status());
        $this->assertEquals(5, count(json_decode($successMultiple->content())));
        $this->assertEquals(404, $failure->status());
        $this->assertEquals(401, $guestFailure->status());
    }

    /**
     * Test the behavior of performing a POST HTTP request to /api/communities.
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

        $guestFailure = $this->call('POST', 'api/communities', [
            'pseudo' => 'ipsum',
            'name' => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet.',
        ]);

        $this->actingAs($user);

        $wrongPseudo = $this->call('POST', 'api/communities', [
            'pseudo' => Str::random(20),
            'name' => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet.',
        ]);

        $existingPseudo = $this->call('POST', 'api/communities', [
            'pseudo' => 'lorem',
            'name' => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet.',
        ]);

        $wrongDescription = $this->call('POST', 'api/communities', [
            'pseudo' => 'ipsum',
            'name' => 'Lorem ipsum',
            'description' => Str::random(50000),
        ]);

        $success = $this->call('POST', 'api/communities', [
            'pseudo' => 'ipsum',
            'name' => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet.',
        ]);
        
        $this->assertEquals(401, $guestFailure->status());
        $this->assertEquals(422, $wrongPseudo->status());
        $this->assertEquals(422, $existingPseudo->status());
        $this->assertEquals(422, $wrongDescription->status());
        $this->assertEquals(201, $success->status());
        $this->seeInDatabase('communities', [
            'pseudo' => 'ipsum',
            'name' => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet.',
        ]);
    }
}
