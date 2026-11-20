<?php
/**
 * Ramayan CMS — Media API Endpoint
 * finggu.com | Author: sudarshanpjadhav
 */

declare(strict_types=1);

namespace Ram\Api\Endpoints;

use Ram\Api\BrahmaandEndpoint;
use Ram\Core\RamRequest;
use Ram\Database\PatalDB;

class MediaEndpoint extends BrahmaandEndpoint
{
    public function ramFn_handle(RamRequest $ramVar_request, array $ramVar_pathParts): void
    {
        if (!ram_is_logged_in()) {
            $this->ramFn_sendError('Unauthorized', 401);
        }

        $ramVar_method = $ramVar_request->ramFn_getMethod();
        
        switch ($ramVar_method) {
            case 'GET':
                $this->ramFn_handleGet();
                break;
            default:
                $this->ramFn_sendError('Method not allowed', 405);
        }
    }

    private function ramFn_handleGet(): void
    {
        // Try using NandanLibrary first, fallback to direct query
        try {
            if (class_exists('Ram\Media\NandanLibrary')) {
                $ramVar_media = \Ram\Media\NandanLibrary::ramFn_listMedia();
                if (!empty($ramVar_media)) {
                    $this->ramFn_sendSuccess($this->ramFn_normalizeUrls($ramVar_media));
                    return;
                }
            }
        } catch (\Throwable $e) {
            // Fall through to direct query
        }
        
        // Direct query fallback
        try {
            $pdo = new \PDO("mysql:host=localhost;dbname=ramayan_db;charset=utf8mb4", "root", "");
            $stmt = $pdo->query("SELECT * FROM ram_media ORDER BY uploaded_at DESC LIMIT 50");
            $ramVar_media = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $this->ramFn_sendSuccess($this->ramFn_normalizeUrls($ramVar_media));
        } catch (\PDOException $e) {
            $this->ramFn_sendError('Error: ' . $e->getMessage());
        }
    }

    /**
     * Convert stored root-relative media URLs into browsable absolute URLs.
     *
     * @param array<int, array<string, mixed>> $ramVar_items
     * @return array<int, array<string, mixed>>
     */
    private function ramFn_normalizeUrls(array $ramVar_items): array
    {
        foreach ($ramVar_items as &$ramVar_item) {
            if (isset($ramVar_item['url'])) {
                $ramVar_item['url'] = \ram_media_url((string) $ramVar_item['url']);
            }
        }
        unset($ramVar_item);

        return $ramVar_items;
    }
}
