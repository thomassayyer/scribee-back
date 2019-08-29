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
        $this->middleware('auth', [
            'except' => [
                'find', 'create', 'createToken'
            ]
        ]);
    }

    /**
     * Find a user by pseudo or email.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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
     * Display the current user (the user who's made the request).
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showCurrent(Request $request)
    {
        return $request->user();
    }

    /**
     * Verify the credentials then create and display the token to use.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createToken(Request $request)
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
     * Create a new user.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'pseudo' => 'required|string|unique:users,pseudo|max:15',
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = new User($request->except('password'));
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return response($user, 201);
    }

    /**
     * Destroy the api_token of an authenticated user.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroyToken(Request $request)
    {
        $request->user()->api_token = null;
        $request->user()->save();

        return response('API Token destroyed!');
    }

    /**
     * Update the current user.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateCurrent(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'new_password' => 'required_with:oldPassword|string|min:8',
        ]);

        if ($request->has('old_password')) {
            if (!Hash::check($request->input('old_password'), $request->user()->password)) {
                return response()->json([
                    'old_password' => [ 'Wrong password!' ]
                ], 422);
            }
        }

        $request->user()->name = $request->input('name');
        $request->user()->email = $request->input('email');
        $request->user()->password = Hash::make($request->input('new_password'));
        $request->user()->save();

        return $request->user();
    }
}
