<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['find']]);
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
}
