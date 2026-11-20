<?php
/**
 * Ramayan CMS — REST API Router (Brahmaand)
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

namespace Ram\Api;

use Ram\Core\RamRequest;
use Ram\Core\RamResponse;
use Ram\Hooks\AkashvaniHooks;

/**
 * BrahmaandAPI — The Universe of Endpoints.
 * 
 * Handles REST API routing, authentication, and response formatting.
 */
class BrahmaandAPI
{
    private static array $ramVar_endpoints = [];

    /**
     * Initialize the API system.
     */
    public static function ramFn_init(): void
    {
        AkashvaniHooks::ramFn_doAction('ram_api_init');
        
        // Register default endpoints
        self::ramFn_registerEndpoint('posts', \Ram\Api\Endpoints\PostsEndpoint::class);
        self::ramFn_registerEndpoint('pages', \Ram\Api\Endpoints\PagesEndpoint::class);
        self::ramFn_registerEndpoint('users', \Ram\Api\Endpoints\UsersEndpoint::class);
        self::ramFn_registerEndpoint('media', \Ram\Api\Endpoints\MediaEndpoint::class);
        
        AkashvaniHooks::ramFn_addAction('ram_init', [self::class, 'ramFn_handleRequest'], 20);
    }

    /**
     * Register a new API endpoint.
     */
    public static function ramFn_registerEndpoint(string $ramVar_route, string $ramVar_className): void
    {
        self::$ramVar_endpoints[$ramVar_route] = $ramVar_className;
    }

    /**
     * Handle incoming API requests.
     */
    public static function ramFn_handleRequest(): void
    {
        $ramVar_request = RamRequest::ramFn_fromGlobals();
        
        // Check for internal admin API param
        $ramVar_internal = $_GET['ram_api'] ?? '';
        if (!empty($ramVar_internal) && isset(self::$ramVar_endpoints[$ramVar_internal])) {
            self::ramFn_dispatch($ramVar_internal, $ramVar_request, [$ramVar_internal]);
            return;
        }

        $ramVar_path    = $ramVar_request->ramFn_getPath();
        
        // Check if this is an API request (starts with /api/v1)
        $ramVar_apiPrefix = '/api/' . (defined('RAM_API_VERSION') ? RAM_API_VERSION : 'v1');
        if (!str_starts_with($ramVar_path, $ramVar_apiPrefix)) {
            return;
        }

        $ramVar_relativeRoute = trim(substr($ramVar_path, strlen($ramVar_apiPrefix)), '/');
        $ramVar_parts         = explode('/', $ramVar_relativeRoute);
        $ramVar_baseRoute     = $ramVar_parts[0] ?? '';

        self::ramFn_dispatch($ramVar_baseRoute, $ramVar_request, $ramVar_parts);
    }

    /**
     * Dispatch to an endpoint.
     */
    private static function ramFn_dispatch(string $ramVar_route, RamRequest $ramVar_request, array $ramVar_parts): void
    {
        if (isset(self::$ramVar_endpoints[$ramVar_route])) {
            $ramVar_endpointClass = self::$ramVar_endpoints[$ramVar_route];
            if (class_exists($ramVar_endpointClass)) {
                $ramVar_instance = new $ramVar_endpointClass();
                $ramVar_instance->ramFn_handle($ramVar_request, $ramVar_parts);
                exit;
            }
        }
        ram_send_json_error('Endpoint not found', 404);
    }

    /**
     * Handle internal admin API calls.
     */
    public function ramFn_handleInternal(string $ramVar_route, RamRequest $ramVar_request): void
    {
        self::ramFn_dispatch($ramVar_route, $ramVar_request, [$ramVar_route]);
    }

    /**
     * Singleton instance.
     */
    public static function ramFn_getInstance(): self
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        return $instance;
    }
}
