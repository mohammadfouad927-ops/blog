<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = [
        'user_id'
    ];

    public function post(){
        $this->belongsTo(Post::class);
    }

    public function user(){
        $this->belongsTo(User::class);
    }
}
