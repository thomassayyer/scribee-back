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
        'suggestion', 'row', 'column', 'text_id'
    ];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['text'];

    /**
     * The text that the suggestion has been made on.
     */
    public function text()
    {
        return $this->belongsTo(Text::class);
    }
}
