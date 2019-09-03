<?php

use Carbon\Carbon;
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
        factory(App\Community::class, 5)->create([
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
        factory(App\Community::class)->create([
            'pseudo' => 'lorem',
        ]);

        $guestFailure = $this->call('GET', 'api/communities/lorem');
        $this->actingAs($user);
        $notFound = $this->call('GET', 'api/communities/ipsum');
        $success = $this->call('GET', 'api/communities/lorem');

        $this->assertEquals(401, $guestFailure->status());
        $this->assertEquals(404, $notFound->status());
        $this->assertEquals(200, $success->status());
    }

    /**
     * Test the behavior of performing a GET HTTP request to /api/communities/daily.
     *
     * @return void
     */
    public function testShowDaily()
    {
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
        ]);
        factory(App\Community::class)->create([
            'pseudo' => 'lorem',
        ])->texts()->createMany([
            [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe' ],
            [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe' ],
            [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe' ],
            [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfWeek() ],
            [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfWeek() ],
            [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfMonth() ],
            [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfMonth() ],
        ]);
        factory(App\Community::class, 2)->create()->each(function (App\Community $community) {
            $community->texts()->createMany([
                [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe' ],
                [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfWeek() ],
                [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfWeek() ],
                [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfMonth() ],
                [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfMonth() ],
            ]);
        });

        $guestFailure = $this->call('GET', 'api/communities/daily');
        $this->actingAs($user);
        $success = $this->call('GET', 'api/communities/daily');

        $this->assertEquals(401, $guestFailure->status());
        $this->assertEquals(200, $success->status());
        $this->assertEquals('lorem', json_decode($success->content())->pseudo);
    }

    /**
     * Test the behavior of performing a GET HTTP request to /api/communities/weekly.
     *
     * @return void
     */
    public function testShowWeekly()
    {
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
        ]);
        factory(App\Community::class)->create([
            'pseudo' => 'lorem',
        ])->texts()->createMany([
            [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfWeek() ],
            [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfWeek() ],
            [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfWeek() ],
            [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfMonth() ],
            [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfMonth() ],
        ]);
        factory(App\Community::class, 2)->create()->each(function (App\Community $community) {
            $community->texts()->createMany([
                [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfWeek() ],
                [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfWeek() ],
                [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfMonth() ],
                [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfMonth() ],
            ]);
        });

        $guestFailure = $this->call('GET', 'api/communities/weekly');
        $this->actingAs($user);
        $success = $this->call('GET', 'api/communities/weekly');

        $this->assertEquals(401, $guestFailure->status());
        $this->assertEquals(200, $success->status());
        $this->assertEquals('lorem', json_decode($success->content())->pseudo);
    }

    /**
     * Test the behavior of performing a GET HTTP request to /api/communities/monthly.
     *
     * @return void
     */
    public function testShowMonthly()
    {
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
        ]);
        factory(App\Community::class)->create([
            'pseudo' => 'lorem',
        ])->texts()->createMany([
            [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfWeek() ],
            [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfWeek() ],
            [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfMonth() ],
            [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfMonth() ],
            [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfMonth() ],
        ]);
        factory(App\Community::class, 2)->create()->each(function (App\Community $community) {
            $community->texts()->createMany([
                [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfWeek() ],
                [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfWeek() ],
                [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfMonth() ],
                [ 'text' => Str::random(50), 'user_pseudo' => 'johndoe', 'updated_at' => Carbon::now()->startOfMonth() ],
            ]);
        });

        $guestFailure = $this->call('GET', 'api/communities/monthly');
        $this->actingAs($user);
        $success = $this->call('GET', 'api/communities/monthly');

        $this->assertEquals(401, $guestFailure->status());
        $this->assertEquals(200, $success->status());
        $this->assertEquals('lorem', json_decode($success->content())->pseudo);
    }

    /**
     * Test the behavior of performing a GET HTTP request to /api/communities/latests.
     *
     * @return void
     */
    public function testShowLatests()
    {
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
        ]);
        factory(App\Community::class, 20)->create([
            'name' => 'Lorem',
        ]);
        factory(App\Community::class, 20)->create([
            'name' => 'Ipsum',
            'created_at' => Carbon::now()->startOfYear(),
        ]);

        $guestFailure = $this->call('GET', 'api/communities/latests');
        $this->actingAs($user);
        $success = $this->call('GET', 'api/communities/latests');

        $returnedCollections = json_decode($success->content());

        $this->assertEquals(401, $guestFailure->status());
        $this->assertEquals(200, $success->status());
        $this->assertEquals(10, count($returnedCollections));
        foreach ($returnedCollections as $collection) {
            $this->assertEquals('Lorem', $collection->name);
        }
    }

    /**
     * Test the behavior of performing a GET HTTP request to /api/communities/popular.
     *
     * @return void
     */
    public function testShowPopular()
    {
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
        ]);
        factory(App\Community::class, 20)->create([
            'name' => 'Lorem',
        ])->each(function ($community) {
            $community->texts()->createMany([
                [ 'text' => Str::random(20), 'user_pseudo' => 'johndoe' ],
            ]);
        });
        factory(App\Community::class, 20)->create([
            'name' => 'Ipsum',
        ]);

        $guestFailure = $this->call('GET', 'api/communities/popular');
        $this->actingAs($user);
        $success = $this->call('GET', 'api/communities/popular');

        $returnedCollections = json_decode($success->content());

        $this->assertEquals(401, $guestFailure->status());
        $this->assertEquals(200, $success->status());
        $this->assertEquals(10, count($returnedCollections));
        foreach ($returnedCollections as $collection) {
            $this->assertEquals('Lorem', $collection->name);
        }
    }

    /**
     * Test the behavior of performing a GET HTTP request to /api/communities/search.
     *
     * @return void
     */
    public function testSearch()
    {
        $user = factory(App\User::class)->create();
        factory(App\Community::class)->create([
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

    /**
     * Test the behavior of performing a PATCH HTTP request to /api/communities/{pseudo}.
     * 
     * @return void
     */
    public function testUpdate()
    {
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
        ]);
        factory(App\Community::class)->create([
            'pseudo' => 'lorem',
            'name' => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sit amet.',
            'user_pseudo' => 'johndoe',
        ]);
        factory(App\Community::class)->create([
            'pseudo' => 'ipsum',
        ]);

        $guestFailure = $this->call('PATCH', 'api/communities/lorem');

        $this->actingAs($user);

        $notOwnCommunity = $this->call('PATCH', 'api/communities/ipsum');
        $communityNotFound = $this->call('PATCH', 'api/communities/dolor');
        $wrongDescription = $this->call('PATCH', 'api/communities/lorem', [
            'description' => Str::random(50000),
        ]);
        $success = $this->call('PATCH', 'api/communities/lorem', [
            'name' => 'Ipsum dolor',
            'description' => 'Suspendisse vitae pharetra leo.',
        ]);

        $this->assertEquals(401, $guestFailure->status());
        $this->assertEquals(401, $notOwnCommunity->status());
        $this->assertEquals(404, $communityNotFound->status());
        $this->assertEquals(422, $wrongDescription->status());
        $this->assertEquals(200, $success->status());
        $this->seeInDatabase('communities', [
            'pseudo' => 'lorem',
            'name' => 'Ipsum dolor',
            'description' => 'Suspendisse vitae pharetra leo.',
        ]);
    }

    /**
     * Test the behavior of performing a DELETE HTTP request to /api/communities/{pseudo}.
     * 
     * @return void
     */
    public function testDestroy()
    {
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
        ]);
        factory(App\Community::class)->create([
            'pseudo' => 'lorem',
            'user_pseudo' => 'johndoe',
        ]);
        factory(App\Community::class)->create([
            'pseudo' => 'ipsum',
        ]);

        $guestFailure = $this->call('DELETE', 'api/communities/lorem');

        $this->actingAs($user);

        $communityNotFound = $this->call('DELETE', 'api/communities/dolor');
        $notOwnCommunity = $this->call('DELETE', 'api/communities/ipsum');
        $success = $this->call('DELETE', 'api/communities/lorem');

        $this->assertEquals(401, $guestFailure->status());
        $this->assertEquals(404, $communityNotFound->status());
        $this->assertEquals(401, $notOwnCommunity->status());
        $this->assertEquals(200, $success->status());
    }
}
