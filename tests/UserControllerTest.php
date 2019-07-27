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

        $successPseudo = $this->call('GET', 'api/users/find', ['query' => 'johndoe']);
        $successEmail = $this->call('GET', 'api/users/find', ['query' => 'john.doe@example.com']);
        $failure = $this->call('GET', 'api/users/find', ['query' => 'janedoe']);

        $this->assertEquals(200, $successPseudo->status());
        $this->assertEquals((string) $user, $successPseudo->content());
        $this->assertEquals(200, $successEmail->status());
        $this->assertEquals((string) $user, $successEmail->content());
        $this->assertEquals(404, $failure->status());
    }
}
