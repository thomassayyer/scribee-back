<?php

namespace App\Http\Controllers;

use App\Text;
use App\Suggestion;
use App\Events\SuggestionsCreated;
use Illuminate\Http\Request;

class SuggestionController extends Controller
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
     * Create and assign new suggestions to text.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  integer  $textId
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $textId)
    {
        $this->validate($request, [
            'suggestions' => 'required|array',
        ]);

        $suggestions = $request->input('suggestions');
        foreach ($suggestions as &$suggestion) {
            $suggestion['user_pseudo'] = $request->user()->pseudo;   
        }

        $created = Text::findOrFail($textId)->suggestions()->createMany($suggestions);

        event(new SuggestionsCreated($created, $request->user()));

        return response($created->load('user'), 201);
    }

    /**
     * Destroy a suggestion.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $suggestion = Suggestion::findOrFail($id);

        if ($suggestion->text->user->pseudo !== $request->user()->pseudo) {
            return response('The text is not yours!', 401);
        }

        $suggestion->delete();

        return response('Suggestion destroyed!');
    }
}
