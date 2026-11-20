<?php
/**
 * Ramayan CMS — Pages API Endpoint
 * finggu.com | Author: sudarshanpjadhav
 */

declare(strict_types=1);

namespace Ram\Api\Endpoints;

use Ram\Api\BrahmaandEndpoint;
use Ram\Core\RamRequest;
use Ram\Database\PatalDB;

/**
 * PagesEndpoint — Handles CRUD for pages via API.
 */
class PagesEndpoint extends BrahmaandEndpoint
{
    public function ramFn_handle(RamRequest $ramVar_request, array $ramVar_pathParts): void
    {
        $ramVar_method = $ramVar_request->ramFn_getMethod();
        
        switch ($ramVar_method) {
            case 'GET':
                $this->ramFn_handleGet($ramVar_request, $ramVar_pathParts);
                break;
            case 'POST':
                $this->ramFn_handlePost($ramVar_request);
                break;
            default:
                $this->ramFn_sendError('Method not allowed', 405);
        }
    }

    private function ramFn_handleGet(RamRequest $ramVar_request, array $ramVar_pathParts): void
    {
        $ramVar_id = $ramVar_pathParts[1] ?? null;

        if ($ramVar_id !== null) {
            $ramVar_page = PatalDB::ramFn_table('posts')
                ->ramFn_where('id', '=', (int) $ramVar_id)
                ->ramFn_where('post_type', '=', 'page')
                ->ramFn_where('status', '=', 'publish')
                ->ramFn_first();
                
            if ($ramVar_page) {
                $this->ramFn_sendSuccess($ramVar_page);
            } else {
                $this->ramFn_sendError('Page not found', 404);
            }
        } else {
            $ramVar_limit = (int) ($ramVar_request->ramFn_get('limit') ?? 10);
            $ramVar_limit = max(1, min(100, $ramVar_limit));
            $ramVar_pages = PatalDB::ramFn_table('posts')
                ->ramFn_where('post_type', '=', 'page')
                ->ramFn_where('status', '=', 'publish')
                ->ramFn_limit($ramVar_limit)
                ->ramFn_get();
                
            $this->ramFn_sendSuccess($ramVar_pages);
        }
    }

    private function ramFn_handlePost(RamRequest $ramVar_request): void
    {
        if (!$this->ramFn_authorize($ramVar_request)) {
            $this->ramFn_sendError('Unauthorized', 401);
        }

        $ramVar_data = $ramVar_request->ramFn_getJsonBody();
        if (empty($ramVar_data['title'])) {
            $this->ramFn_sendError('Title is required');
        }

        // Placeholder for insert logic
        $this->ramFn_sendSuccess(['message' => 'Page created (Mock)'], 201);
    }
}
