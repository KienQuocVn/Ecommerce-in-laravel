<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    /**
     * Upload image to Cloudinary
     *
     * @param UploadedFile|string $file File to upload or local file path
     * @param string $folder Folder name in Cloudinary
     * @param array $options Additional upload options
     * @return array|string Returns Cloudinary URL or array with public_id and secure_url
     */
    public static function upload($file, $folder = 'ecommerce', $options = [])
    {
        try {
            $defaultOptions = [
                'folder' => $folder,
                'resource_type' => 'image',
                'overwrite' => true,
                'invalidate' => true,
            ];

            $uploadOptions = array_merge($defaultOptions, $options);

            // If it's a string (local path), read the file
            if (is_string($file)) {
                if (!file_exists($file)) {
                    throw new \Exception("File not found: {$file}");
                }
                $result = Cloudinary::upload($file, $uploadOptions);
            } else {
                // It's an UploadedFile
                $result = Cloudinary::upload($file->getRealPath(), $uploadOptions);
            }

            return [
                'public_id' => $result->getPublicId(),
                'secure_url' => $result->getSecurePath(),
                'url' => $result->getPath(),
                'format' => $result->getExtension(),
                'width' => $result->getWidth(),
                'height' => $result->getHeight(),
            ];
        } catch (\Exception $e) {
            Log::error('Cloudinary upload error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Upload image from local storage path
     *
     * @param string $localPath Local file path (e.g., /storage/photos/1/Banner/banner-01.jpg)
     * @param string $folder Folder name in Cloudinary
     * @return string Cloudinary secure URL
     */
    public static function uploadFromLocalPath($localPath, $folder = 'ecommerce')
    {
        try {
            // Convert relative path to absolute path
            $absolutePath = public_path($localPath);

            if (!file_exists($absolutePath)) {
                Log::warning("File not found for Cloudinary upload: {$absolutePath}");
                return $localPath; // Return original path if file doesn't exist
            }

            $result = self::upload($absolutePath, $folder);
            return $result['secure_url'];
        } catch (\Exception $e) {
            Log::error('Cloudinary upload from local path error: ' . $e->getMessage());
            return $localPath; // Fallback to original path
        }
    }

    /**
     * Delete image from Cloudinary
     *
     * @param string $publicId Cloudinary public ID
     * @return bool
     */
    public static function delete($publicId)
    {
        try {
            Cloudinary::destroy($publicId);
            return true;
        } catch (\Exception $e) {
            Log::error('Cloudinary delete error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get Cloudinary URL with transformations
     *
     * @param string $publicId Cloudinary public ID
     * @param array $transformations Transformation options
     * @return string
     */
    public static function url($publicId, $transformations = [])
    {
        try {
            return Cloudinary::show($publicId, $transformations);
        } catch (\Exception $e) {
            Log::error('Cloudinary URL generation error: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Check if URL is a Cloudinary URL
     *
     * @param string $url
     * @return bool
     */
    public static function isCloudinaryUrl($url)
    {
        return strpos($url, 'res.cloudinary.com') !== false ||
            strpos($url, 'cloudinary.com') !== false;
    }

    /**
     * Get image URL (Cloudinary or local)
     *
     * @param string|null $path Image path (can be Cloudinary URL or local path)
     * @param string $default Default image if path is null
     * @return string
     */
    public static function getImageUrl($path, $default = '/images/default.jpg')
    {
        if (empty($path)) {
            return $default;
        }

        // If it's already a Cloudinary URL or full URL, return as is
        if (self::isCloudinaryUrl($path) || filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // If it's a local path starting with /storage, return as is (will be served by Laravel)
        if (strpos($path, '/storage') === 0 || strpos($path, '/') === 0) {
            return $path;
        }

        return $default;
    }
}
