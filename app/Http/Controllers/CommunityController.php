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
     * Search for one or more communities by pseudo or name.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function search(Request $request)
    {
        $query = '%' . $request->get('query') . '%';

        $communities = Community::where('pseudo', 'like', $query)
                                ->orWhere('name', 'like', $query)
                                ->take(5)
                                ->get();
        
        if ($communities->isEmpty()) {
            return response('No results!', 404);
        }
    
        return $communities;
    }
}
