<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class UserControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test the behavior of performing a GET HTTP request to /api/users/search.
     *
     * @return void
     */
    public function testSearch()
    {
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
            'email' => 'john.doe@example.com',
        ]);

        $this->json('GET', 'api/users/find', ['query' => 'johndoe'])
             ->seeJson($user->toArray());

        $this->json('GET', 'api/users/find', ['query' => 'john.doe@example.com'])
             ->seeJson($user->toArray());

        $this->assertEquals(
            404,
            $this->call('GET', 'api/users/find', ['query' => 'janedoe'])->status()
        );
    }
}
