<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Text extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'text', 'community_pseudo'
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'user', 'community'
    ];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['community'];

    /**
     * The author of the text.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The community of the text.
     */
    public function community()
    {
        return $this->belongsTo(Community::class);
    }
}
