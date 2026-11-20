<?php
/**
 * Ramayan CMS — Media Library (Nandan)
 * finggu.com | Author: sudarshanpjadhav
 */

declare(strict_types=1);

namespace Ram\Media;

use Ram\Database\PatalDB;

/**
 * NandanLibrary — Core media management logic.
 */
class NandanLibrary
{
    /**
     * Get a media item by ID.
     */
    public static function ramFn_getMedia(int $ramVar_id): ?array
    {
        return PatalDB::ramFn_table('media')->ramFn_find($ramVar_id);
    }

    /**
     * List media items.
     */
    public static function ramFn_listMedia(array $ramVar_args = []): array
    {
        $ramVar_args = array_merge([
            'limit'  => 50,
            'offset' => 0,
            'type'   => null,
        ], $ramVar_args);

        $ramVar_query = PatalDB::ramFn_table('media')
            ->ramFn_orderBy('uploaded_at', 'DESC')
            ->ramFn_limit($ramVar_args['limit'])
            ->ramFn_offset($ramVar_args['offset']);

        if ($ramVar_args['type']) {
            $ramVar_query->ramFn_where('mime_type', 'LIKE', $ramVar_args['type'] . '/%');
        }

        return $ramVar_query->ramFn_get();
    }
}
