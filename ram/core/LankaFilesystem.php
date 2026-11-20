<?php
/**
 * Ramayan CMS — Filesystem & Media (Lanka)
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

namespace Ram\Core;

use Ram\Database\PatalDB;

/**
 * LankaFilesystem — Handles file storage, uploads, and media metadata.
 *
 * Implements safe uploads and directory management.
 */
final class LankaFilesystem
{
    /** @var string[] Allowed extensions for media */
    private static array $ramVar_allowedExts = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
        'pdf', 'doc', 'docx', 'zip', 'mp3', 'mp4'
    ];

    /**
     * Upload a file from $_FILES.
     *
     * @param array<string, mixed> $ramVar_file   Item from $_FILES
     * @param int                  $ramVar_userId Owner ID
     * @return int|false Attachment ID or false on failure
     */
    public static function ramFn_upload(array $ramVar_file, int $ramVar_userId): int|bool
    {
        if ($ramVar_file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $ramVar_name = basename($ramVar_file['name']);
        $ramVar_ext  = strtolower(pathinfo($ramVar_name, PATHINFO_EXTENSION));

        if (!in_array($ramVar_ext, self::$ramVar_allowedExts, true)) {
            return false;
        }

        // 1. Create monthly directory structure (Y/m)
        $ramVar_relPath = date('Y/m');
        $ramVar_fullPath = RAM_UPLOAD_PATH . '/' . $ramVar_relPath;

        if (!is_dir($ramVar_fullPath)) {
            mkdir($ramVar_fullPath, 0755, true);
        }

        // 2. Sanitize filename
        $ramVar_safeName = preg_replace('/[^a-z0-9\._-]/i', '-', strtolower($ramVar_name));
        $ramVar_destName = time() . '-' . $ramVar_safeName;
        $ramVar_destPath = $ramVar_fullPath . '/' . $ramVar_destName;

        // 3. Move file
        if (move_uploaded_file($ramVar_file['tmp_name'], $ramVar_destPath)) {
            // 4. Save metadata in DB
            $ramVar_mime = $ramVar_file['type'];
            $ramVar_size = $ramVar_file['size'];
            $ramVar_url  = RAM_UPLOAD_URL . '/' . $ramVar_relPath . '/' . $ramVar_destName;

            try {
                return PatalDB::ramFn_table('media')->ramFn_insert([
                    'author_id' => $ramVar_userId,
                    'filename'  => $ramVar_destName,
                    'path'      => $ramVar_relPath . '/' . $ramVar_destName,
                    'url'       => $ramVar_url,
                    'mime_type' => $ramVar_mime,
                    'file_size' => $ramVar_size,
                    'alt_text'  => $ramVar_name,
                    'uploaded_at' => date('Y-m-d H:i:s')
                ]);
            } catch (\Throwable) {
                return false;
            }
        }

        return false;
    }

    /**
     * Delete a file and its DB record.
     */
    public static function ramFn_delete(int $ramVar_id): bool
    {
        $ramVar_media = PatalDB::ramFn_table('media')->ramFn_find($ramVar_id);
        if (!$ramVar_media) return false;

        $ramVar_filePath = RAM_UPLOAD_PATH . '/' . $ramVar_media['path'];
        if (file_exists($ramVar_filePath)) {
            unlink($ramVar_filePath);
        }

        return (bool) PatalDB::ramFn_table('media')->ramFn_delete($ramVar_id);
    }

    /**
     * Get a media item by ID.
     */
    public static function ramFn_get(int $ramVar_id): ?array
    {
        return PatalDB::ramFn_table('media')->ramFn_find($ramVar_id);
    }
}
