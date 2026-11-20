<?php
/**
 * Ramayan CMS — Media Uploader (Nandan)
 * finggu.com | Author: sudarshanpjadhav
 */

declare(strict_types=1);

namespace Ram\Media;

use Ram\Database\PatalDB;

/**
 * NandanUploader — Handles safe file uploads.
 */
class NandanUploader
{
    /**
     * Upload a file from $_FILES.
     * 
     * @param array $ramVar_file Single item from $_FILES
     * @return int|false Media ID on success, false on failure
     */
    public static function ramFn_upload(array $ramVar_file): int|false
    {
        if ($ramVar_file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $ramVar_filename = basename($ramVar_file['name']);
        $ramVar_ext      = strtolower(pathinfo($ramVar_filename, PATHINFO_EXTENSION));
        
        // Allowed extensions (should be configurable)
        $ramVar_allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'zip'];
        if (!in_array($ramVar_ext, $ramVar_allowed)) {
            return false;
        }

        // Generate unique filename
        $ramVar_newFilename = time() . '-' . bin2hex(random_bytes(4)) . '.' . $ramVar_ext;
        $ramVar_uploadDir   = RAM_UPLOAD_PATH . '/' . date('Y/m');
        
        if (!is_dir($ramVar_uploadDir)) {
            mkdir($ramVar_uploadDir, 0755, true);
        }

        $ramVar_dest = $ramVar_uploadDir . '/' . $ramVar_newFilename;

        if (move_uploaded_file($ramVar_file['tmp_name'], $ramVar_dest)) {
            // Save to DB
            $ramVar_relativeDir = date('Y/m');
            return PatalDB::ramFn_table('media')->ramFn_insert([
                'filename'   => $ramVar_newFilename,
                'path'       => $ramVar_relativeDir . '/' . $ramVar_newFilename,
                'mime_type'  => $ramVar_file['type'],
                'filesize'   => $ramVar_file['size'],
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return false;
    }
}
