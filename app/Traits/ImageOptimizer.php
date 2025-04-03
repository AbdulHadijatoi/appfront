<?php

namespace App\Traits;

use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\JpegEncoder;
use Illuminate\Support\Facades\Storage;

trait ImageOptimizer
{
    /**
     * Optimize and store the image if it's larger than 1MB.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path
     * @return string
     */
    public function optimizeImage($file, $path)
    {
        // Get file size in MB
        $fileSizeMB = $file->getSize() / (1024 * 1024);

        // If file is already ≤ 1MB, save it directly and return full path
        if ($fileSizeMB <= 1) {
            return $file->store($path, 'public'); // Returns "uploads/image_name.jpg"
        }

        // Load image using Intervention v3
        $image = Image::read($file);

        // Start with high quality
        $quality = 90;

        // Reduce quality until the image is ≤ 1MB
        while ($fileSizeMB > 1 && $quality > 10) {
            // Encode using JpegEncoder (Intervention v3 syntax)
            $optimizedImage = $image->encode(new JpegEncoder($quality));

            // Check new size
            $fileSizeMB = strlen($optimizedImage) / (1024 * 1024);

            // Reduce quality gradually
            $quality -= 10;
        }

        // Generate unique filename
        $filename = time() . '.jpg';

        // Full relative path
        $fullPath = $path . '/' . $filename;

        // Save optimized image in storage
        Storage::disk('public')->put($fullPath, $optimizedImage);

        // Return the full relative path (e.g., "uploads/image_name.jpg")
        return $fullPath;
    }
}
