<?php

namespace App\Helper;

use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ImageUpload
{
    public static function fileUploadWithResize($image, $path, $width = null, $height = null)
    {
        try {
            $imageUrl = $path . Str::slug(explode('.', time() . $image->getClientOriginalName())[0]) . '.' . $image->getClientOriginalExtension();
            $img = Image::make($image);
            if ($width && $height) {
                $img->resize($width, $height);
            }
            
            // Convert image to string to save it to S3
            $imageContent = (string) $img->encode();

            Storage::disk('s3')->put($imageUrl, $imageContent, 'public');
            $url = Storage::disk('s3')->url($imageUrl);

            return $url;
        } catch (\Exception $e) {
            \Log::error('Error uploading file with resize: ' . $e->getMessage());
            return null;
        }
    }

    public static function fileUploadWithCrop($image, $path, $width = null, $height = null)
    {
        try {
            $imageUrl = $path . Str::slug(explode('.', time() . $image->getClientOriginalName())[0]) . '.' . $image->getClientOriginalExtension();
            $img = Image::make($image);
            if ($width && $height) {
                $img->crop($width, $height);
            }

            // Convert image to string to save it to S3
            $imageContent = (string) $img->encode();

            Storage::disk('s3')->put($imageUrl, $imageContent, 'public');
            $url = Storage::disk('s3')->url($imageUrl);

            return $url;
        } catch (\Exception $e) {
            \Log::error('Error uploading file with crop: ' . $e->getMessage());
            return null;
        }
    }

    public static function fileUpload($file, $path)
    {
        try {
            $fileUrl = 'uploads/' . $path . Str::slug(explode('.', time() . $file->getClientOriginalName())[0]) . '.' . $file->getClientOriginalExtension();
            Storage::disk('s3')->put($fileUrl, file_get_contents($file), 'public');

            $url = Storage::disk('s3')->url($fileUrl);
            return $url;
        } catch (\Exception $e) {
            \Log::error('Error uploading file: ' . $e->getMessage());
            return null;
        }
    }
}
