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
     * Test the behavior of performing a POST HTTP request to /api/users/token.
     *
     * @return void
     */
    public function testCreateToken()
    {
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
            'password' => Hash::make('password'),
        ]);

        $wrongPseudo = $this->call('POST', 'api/users/token', [
            'login' => 'janedoe',
            'password' => 'password',
        ]);
        $wrongPassword = $this->call('POST', 'api/users/token', [
            'login' => 'janedoe',
            'password' => 'secret',
        ]);
        $success = $this->call('POST', 'api/users/token', [
            'login' => 'johndoe',
            'password' => 'password',
        ]);

        $this->assertEquals(422, $wrongPseudo->status());
        $this->assertEquals(422, $wrongPassword->status());
        $this->assertEquals(200, $success->status());
        $this->seeInDatabase('users', ['api_token' => json_decode($success->content())->api_token]);
    }

    /**
     * Test the behavior of performing a POST HTTP request to /api/users.
     *
     * @return void
     */
    public function testCreate()
    {
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ]);

        $existingPseudo = $this->call('POST', 'api/users', [
            'pseudo' => 'johndoe',
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'Password33',
        ]);

        $wrongPseudo = $this->call('POST', 'api/users', [
            'pseudo' => 'johndoe78928123',
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'Password33',
        ]);

        $wrongEmail = $this->call('POST', 'api/users', [
            'pseudo' => 'janedoe',
            'name' => 'Jane Doe',
            'email' => 'jane.doe',
            'password' => 'Password33',
        ]);

        $existingEmail = $this->call('POST', 'api/users', [
            'pseudo' => 'janedoe',
            'name' => 'Jane Doe',
            'email' => 'john.doe@example.com',
            'password' => 'Password33',
        ]);

        $wrongPassword = $this->call('POST', 'api/users', [
            'pseudo' => 'janedoe',
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'password' => 'passwd',
        ]);

        $success = $this->call('POST', 'api/users', [
            'pseudo' => 'janedoe',
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'password' => 'Password33',
        ]);

        
        $this->assertEquals(422, $existingPseudo->status());
        $this->assertEquals(422, $wrongPseudo->status());
        $this->assertEquals(422, $wrongEmail->status());
        $this->assertEquals(422, $existingEmail->status());
        $this->assertEquals(422, $wrongPassword->status());
        $this->assertEquals(201, $success->status());
        $this->seeInDatabase('users', [
            'pseudo' => 'janedoe',
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
        ]);
    }

    /**
     * Test the behavior of performing a DELETE HTTP request to /api/users/token.
     *
     * @return void
     */
    public function testDestroyToken()
    {
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
            'api_token' => 'token',
        ]);

        $response = $this->actingAs($user)->call('DELETE', 'api/users/token');

        $this->assertEquals(200, $response->status());
        $this->seeInDatabase('users', [
            'pseudo' => 'johndoe',
            'api_token' => null,
        ]);
    }

    /**
     * Test the behavior of performing a GET HTTP request to /api/users/current.
     *
     * @return void
     */
    public function testShowCurrent()
    {
        $user = factory(App\User::class)->create();

        $response = $this->actingAs($user)->call('GET', 'api/users/current');

        $this->assertEquals(200, $response->status());
        $this->assertEquals($user->toJson(), $response->content());
    }

    /**
     * Test the behavior of performing a PATCH HTTP request to /api/users/current.
     * 
     * @return void
     */
    public function testUpdateCurrent()
    {
        factory(App\User::class)->create([
            'email' => 'jane.doe@example.com',
            'password' => Hash::make('password'),
        ]);
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('secret'),
        ]);

        $user->makeVisible('password');

        $guestFailure = $this->call('PATCH', 'api/users/current');

        $this->actingAs($user);

        $wrongEmail = $this->call('PATCH', 'api/users/current', [
            'email' => 'john.doe',
        ]);

        $existingEmail = $this->call('PATCH', 'api/users/current', [
            'email' => 'jane.doe@example.com',
        ]);

        $wrongOldPassword = $this->call('PATCH', 'api/users/current', [
            'old_password' => 'password',
            'new_password' => 'Password33',
        ]);

        $wrongNewPassword = $this->call('PATCH', 'api/users/current', [
            'old_password' => 'secret',
            'new_password' => 'passwd',
        ]);

        $success = $this->call('PATCH', 'api/users/current', [
            'name' => 'David Doe',
            'email' => 'david.doe@example.com',
            'old_password' => 'secret',
            'new_password' => 'Password33',
        ]);

        $this->assertEquals(401, $guestFailure->status());
        $this->assertEquals(422, $wrongEmail->status());
        $this->assertEquals(422, $existingEmail->status());
        $this->assertEquals(422, $wrongOldPassword->status());
        $this->assertEquals(422, $wrongNewPassword->status());
        $this->assertEquals(200, $success->status());
        $this->seeInDatabase('users', [
            'pseudo' => 'johndoe',
            'name' => 'David Doe',
            'email' => 'david.doe@example.com',
        ]);
        $this->assertTrue(Hash::check('Password33', json_decode($success->content())->password));
    }

    /**
     * Test the behavior of performing a DELETE HTTP request to /api/users/current.
     * 
     * @return void
     */
    public function testDestroyCurrent()
    {
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
            'password' => Hash::make('secret'),
        ]);

        $guestFailure = $this->call('DELETE', 'api/users/current', [
            'password' => 'secret'
        ]);

        $this->actingAs($user);

        $wrongPassword = $this->call('DELETE', 'api/users/current', [
            'password' => 'password'
        ]);

        $success = $this->call('DELETE', 'api/users/current', [
            'password' => 'secret'
        ]);

        $this->assertEquals(401, $guestFailure->status());
        $this->assertEquals(400, $wrongPassword->status());
        $this->assertEquals(200, $success->status());
    }
}
