<?php
/**
 * Ramayan CMS — Base API Endpoint
 * finggu.com | Author: sudarshanpjadhav
 */

declare(strict_types=1);

namespace Ram\Api;

use Ram\Core\RamRequest;

/**
 * BrahmaandEndpoint — Abstract base for all API endpoints.
 */
abstract class BrahmaandEndpoint
{
    /**
     * Handle the incoming request.
     * 
     * @param RamRequest $ramVar_request
     * @param string[]   $ramVar_pathParts
     */
    abstract public function ramFn_handle(RamRequest $ramVar_request, array $ramVar_pathParts): void;

    /**
     * Check if the request is authorized.
     */
    protected function ramFn_authorize(RamRequest $ramVar_request): bool
    {
        // Simple authentication check for now
        // In production, this would check Bearer tokens or API keys
        return ram_is_logged_in();
    }

    /**
     * Send a successful JSON response.
     */
    protected function ramFn_sendSuccess(mixed $ramVar_data = null, int $ramVar_status = 200): never
    {
        ram_send_json_success($ramVar_data, $ramVar_status);
    }

    /**
     * Send an error JSON response.
     */
    protected function ramFn_sendError(string $ramVar_message, int $ramVar_status = 400): never
    {
        ram_send_json_error($ramVar_message, $ramVar_status);
    }
}
