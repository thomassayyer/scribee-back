<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class CommunityControllerTest extends TestCase
{
    use DatabaseMigrations;

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

        $guestFailure = $this->call('GET', 'api/communities/search', ['query' => 'lor']);

        $this->actingAs($user);
        $successPseudo = $this->call('GET', 'api/communities/search', ['query' => 'lor']);
        $successName = $this->call('GET', 'api/communities/search', ['query' => 'Lorem ips']);
        $failure = $this->call('GET', 'api/communities/search', ['query' => 'johndoe']);

        $this->assertEquals(200, $successPseudo->status());
        $this->assertEquals("[{$community->toJson()}]", $successPseudo->content());
        $this->assertEquals(200, $successName->status());
        $this->assertEquals("[{$community->toJson()}]", $successName->content());
        $this->assertEquals(404, $failure->status());
        $this->assertEquals(401, $guestFailure->status());
    }
}