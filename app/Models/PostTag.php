<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostTag extends Model
{
    protected $fillable = ['title', 'slug', 'status'];

    public function post()
    {
        return $this->hasMany('App\Models\Post', 'post_tag_id', 'id')->where('status', 'active');
    }

    public function posts()
    {
        return $this->hasMany('App\Models\Post', 'post_tag_id', 'id')->where('status', 'active');
    }
}
