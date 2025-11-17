<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\CloudinaryService;

class Banner extends Model
{
    protected $fillable = ['title', 'slug', 'description', 'photo', 'status'];

    /**
     * Get photo URL (Cloudinary or local)
     */
    public function getPhotoUrlAttribute()
    {
        return CloudinaryService::getImageUrl($this->photo);
    }
}
