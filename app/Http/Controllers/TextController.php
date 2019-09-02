<?php

namespace App\Http\Controllers;

use App\Text;
use App\Suggestion;
use App\Events\SuggestionAccepted;
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

        return response($text->load([ 'community', 'user', 'suggestions' ]), 201);
    }

    /**
     * Accept a suggestion on a text.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  integer  $textId
     * @param  integer  $suggestionId
     * @return \Illuminate\Http\Response
     */
    public function acceptSuggestion(Request $request, $textId, $suggestionId)
    {
        if ($request->user()->score === 0) {
            return response('Your score is too low!', 401);
        }
        
        $suggestion = Suggestion::findOrFail($suggestionId);
        $text = Text::findOrFail($textId);

        if ($suggestion->text->id !== $text->id) {
            return response('The suggestion does not belong to the text!', 400);
        }

        if ($text->user->pseudo !== $request->user()->pseudo) {
            return response('The text is not yours!', 401);
        }

        $text->text = str_replace($suggestion->original, $suggestion->suggestion, $text->text);
        $text->save();
        
        event(new SuggestionAccepted($suggestion, $request->user()));

        return response($text->text);
    }

    /**
     * Destroy a text.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $text = Text::findOrFail($id);
        
        if ($text->user_pseudo !== $request->user()->pseudo) {
            return response('The text is not yours!', 401);
        }

        $text->delete();

        return response('Text destroyed!');
    }
}
