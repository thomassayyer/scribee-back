<?php

namespace App\Http\Controllers;

use App\Community;
use Illuminate\Http\Request;

class CommunityController extends Controller
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
     * Display the communities of an authenticated user.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $request->user()->communities;
    }

    /**
     * Display a community.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $pseudo
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $pseudo)
    {
        return Community::with('texts')->findOrFail($pseudo);
    }

    /**
     * Search for one or more communities by pseudo or name.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $query = $request->get('query') . '%';

        $communities = Community::where('pseudo', 'like', $query)
                                ->orWhere('name', 'like', $query)
                                ->take(5)
                                ->get();
        
        if ($communities->isEmpty()) {
            return response('No results!', 404);
        }
    
        return $communities;
    }

    /**
     * Create a new community.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'pseudo' => 'required|string|unique:communities,pseudo|max:15',
            'name' => 'required|string',
            'description' => 'required|string|max:40000',
        ]);

        $community = new Community($request->all());
        $community->user_pseudo = $request->user()->pseudo;
        $community->save();

        return response($community, 201);
    }

    /**
     * Update a community.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $pseudo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $pseudo)
    {
        $this->validate($request, [
            'name' => 'string',
            'description' => 'string|max:40000',
        ]);

        $community = Community::findOrFail($pseudo);

        if ($community->user_pseudo !== $request->user()->pseudo) {
            return response('The community is not yours!', 401);
        }

        if ($request->has('name')) {
            $community->name = $request->input('name');
        }

        if ($request->has('description')) {
            $community->description = $request->input('description');
        }

        $community->save();

        return $community;
    }

    /**
     * Destroy a community.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $pseudo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $pseudo)
    {
        $community = Community::findOrFail($pseudo);
        
        if ($community->user_pseudo !== $request->user()->pseudo) {
            return response('The community is not yours!', 401);
        }

        $community->delete();

        return response('Community destroyed!');
    }
}
