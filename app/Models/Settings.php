<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\CloudinaryService;

class Settings extends Model
{
    protected $fillable = ['short_des', 'description', 'photo', 'address', 'phone', 'email', 'logo'];

    /**
     * Get photo URL (Cloudinary or local)
     */
    public function getPhotoUrlAttribute()
    {
        return CloudinaryService::getImageUrl($this->photo);
    }

    /**
     * Get logo URL (Cloudinary or local)
     */
    public function getLogoUrlAttribute()
    {
        return CloudinaryService::getImageUrl($this->logo);
    }
}
