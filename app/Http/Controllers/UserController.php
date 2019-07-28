<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['show']]);
    }

    /**
     * Find a user by pseudo or email.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \App\User
     */
    public function find(Request $request)
    {
        $user = User::where('pseudo', $request->get('query'))
                    ->orWhere('email', $request->get('query'))
                    ->first();
        
        if (!$user) {
            return response('User not found!', 404);
        }
    
        return $user;
    }

    /**
     * Display the specified user.
     *
     * @param  string  $pseudo
     * @return \App\User|\Illuminate\Http\Response
     */
    public function show($pseudo)
    {
        $user = User::find($pseudo);

        if (!$user) {
            return response('User not found!', 404);
        }
    
        return $user;
    }

    /**
     * Verify the credentials and display the token to use.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $user = User::find($request->input('login'));

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response('Invalid credentials!', 422);
        }

        $token = Str::random(60);
        $user->api_token = $token;
        $user->save();

        return response()->json([ 'api_token' => $token ]);
    }

    /**
     * Register a new user.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'pseudo' => 'required|string|unique:users,pseudo',
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = new User($request->except('password'));
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return $user;
    }

    /**
     * Destroy the api_token of an authenticated user.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->api_token = null;
        $request->user()->save();

        return response('API Token destroyed!');
    }
}
