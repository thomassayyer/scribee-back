<?php

namespace App\Http\Controllers;

use App\Text;
use Illuminate\Http\Request;

class TextController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the texts of an authenticated user.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $request->user()->texts;
    }

    /**
     * Create a new text.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'text' => 'required|string|max:40000',
            'community_pseudo' => 'required|string|exists:communities,pseudo',
        ]);

        $text = new Text($request->all());
        $text->user_pseudo = $request->user()->pseudo;
        $text->save();

        return response($text->load([ 'community', 'user' ]), 201);
    }
}
