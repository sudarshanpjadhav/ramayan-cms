<?php
/**
 * Ramayan CMS — Image Processor (Nandan)
 * finggu.com | Author: sudarshanpjadhav
 */

declare(strict_types=1);

namespace Ram\Media;

/**
 * NandanImageProcessor — Image optimization and resizing.
 */
class NandanImageProcessor
{
    /**
     * Convert an image to WebP format if GD supports it.
     */
    public static function ramFn_convertToWebP(string $ramVar_source): string|false
    {
        if (!function_exists('imagewebp')) {
            return false;
        }

        $ramVar_info = getimagesize($ramVar_source);
        if (!$ramVar_info) return false;

        $ramVar_mime = $ramVar_info['mime'];
        $ramVar_image = null;

        switch ($ramVar_mime) {
            case 'image/jpeg': $ramVar_image = imagecreatefromjpeg($ramVar_source); break;
            case 'image/png':  $ramVar_image = imagecreatefrompng($ramVar_source); break;
            case 'image/gif':  $ramVar_image = imagecreatefromgif($ramVar_source); break;
        }

        if (!$ramVar_image) return false;

        $ramVar_dest = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $ramVar_source);
        if (imagewebp($ramVar_image, $ramVar_dest, 80)) {
            imagedestroy($ramVar_image);
            return $ramVar_dest;
        }

        imagedestroy($ramVar_image);
        return false;
    }
}
