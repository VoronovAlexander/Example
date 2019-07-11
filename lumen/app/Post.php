<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{

    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function comments()
    {
        return $this->morphMany('App\Comment', 'owner');
    }

    protected $fillable = [
        'text',
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];

    protected $hidden = [
        'user_id',
    ];
}
