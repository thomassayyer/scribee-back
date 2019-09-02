<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'original', 'suggestion', 'text_id', 'user_pseudo'
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'user'
    ];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['text'];

    /**
     * The user who made the suggestion.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The text that the suggestion has been made on.
     */
    public function text()
    {
        return $this->belongsTo(Text::class);
    }
}
