<?php

use Illuminate\Support\Str;

if (!function_exists('generateUniqueSlug')) {
    /**
     * Generate a unique slug for a given title and model.
     */
    function generateUniqueSlug(string $title, string $modelClass): string
    {
        $slug = Str::slug($title);
        $count = $modelClass::where('slug', $slug)->count();

        if ($count > 0) {
            $slug .= '-' . date('ymdis') . '-' . rand(0, 999);
        }

        return $slug;
    }
}
