<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;

class UserControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test the behavior of performing a GET HTTP request to /api/users/find.
     *
     * @return void
     */
    public function testFind()
    {
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
            'email' => 'john.doe@example.com',
        ]);

        $successPseudo = $this->call('GET', 'api/users/find', ['query' => 'johndoe']);
        $successEmail = $this->call('GET', 'api/users/find', ['query' => 'john.doe@example.com']);
        $failure = $this->call('GET', 'api/users/find', ['query' => 'janedoe']);

        $this->assertEquals(200, $successPseudo->status());
        $this->assertEquals($user->toJson(), $successPseudo->content());
        $this->assertEquals(200, $successEmail->status());
        $this->assertEquals($user->toJson(), $successEmail->content());
        $this->assertEquals(404, $failure->status());
    }

    /**
     * Test the behavior of performing a GET HTTP request to /api/users/{user}.
     *
     * @return void
     */
    public function testShow()
    {
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe'
        ]);

        $response = $this->call('GET', 'api/users/johndoe');

        $this->assertEquals(200, $response->status());
        $this->assertEquals($user->toJson(), $response->content());
    }

    /**
     * Test the behavior of performing a POST HTTP request to /api/users/login.
     *
     * @return void
     */
    public function testLogin()
    {
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
            'password' => Hash::make('password'),
        ]);

        $wrongPseudo = $this->call('POST', 'api/users/login', [
            'login' => 'janedoe',
            'password' => 'password',
        ]);
        $wrongPassword = $this->call('POST', 'api/users/login', [
            'login' => 'janedoe',
            'password' => 'secret',
        ]);
        $success = $this->call('POST', 'api/users/login', [
            'login' => 'johndoe',
            'password' => 'password',
        ]);

        $this->assertEquals(422, $wrongPseudo->status());
        $this->assertEquals(422, $wrongPassword->status());
        $this->assertEquals(200, $success->status());
        $this->seeInDatabase('users', ['api_token' => json_decode($success->content())->api_token]);
    }
}
