<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function owner()
    {
        return $this->morphTo('owner');
    }

    public function comments()
    {
        return $this->morphMany('App\Comment', 'owner');
    }

    protected $fillable = [
        'text',
    ];
}
