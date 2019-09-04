<?php

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Illuminate\Support\Str;

class AuthenticationTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test the behavior of the authentication service.
     * 
     * @return void
     */
    public function test()
    {
        $token = Str::random(60);
        $user = factory(App\User::class)->create([
            'pseudo' => 'johndoe',
            'api_token' => $token,
        ]);

        $wrongToken = $this->call('GET', 'test/auth', [], [], [], [
            'HTTP_Authorization' => 'Bearer 123',
        ]);
        $noToken = $this->call('GET', 'test/auth');
        $success = $this->call('GET', 'test/auth', [], [], [], [
            'HTTP_Authorization' => "Bearer $token",
        ]);

        $this->assertEquals(401, $wrongToken->status());
        $this->assertEquals(401, $noToken->status());
        $this->assertEquals(200, $success->status());
        $this->assertEquals('johndoe', json_decode($success->content())->pseudo);
    }
}
