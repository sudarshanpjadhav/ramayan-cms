<?php
/**
 * Ramayan CMS — Posts API Endpoint
 * finggu.com | Author: sudarshanpjadhav
 */

declare(strict_types=1);

namespace Ram\Api\Endpoints;

use Ram\Api\BrahmaandEndpoint;
use Ram\Core\RamRequest;

/**
 * PostsEndpoint — Handles CRUD for posts via API.
 */
class PostsEndpoint extends BrahmaandEndpoint
{
    public function ramFn_handle(RamRequest $ramVar_request, array $ramVar_pathParts): void
    {
        $ramVar_method = $ramVar_request->ramFn_getMethod();
        
        // Route by method
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
            $ramVar_post = ram_get_post((int) $ramVar_id);
            if ($ramVar_post) {
                $this->ramFn_sendSuccess($ramVar_post);
            } else {
                $this->ramFn_sendError('Post not found', 404);
            }
        } else {
            // List posts
            $ramVar_limit  = (int) ($ramVar_request->ramFn_get('limit') ?? 10);
            $ramVar_status = $ramVar_request->ramFn_get('status') ?? 'publish';
            
            $ramVar_posts = ram_get_posts([
                'limit'  => $ramVar_limit,
                'status' => $ramVar_status
            ]);
            
            $this->ramFn_sendSuccess($ramVar_posts);
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

        // Logic to insert post would go here...
        // For now, just a mock success
        $this->ramFn_sendSuccess(['message' => 'Post created (Mock)'], 201);
    }
}
