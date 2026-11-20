<?php
/**
 * Ramayan CMS — Users API Endpoint
 * finggu.com | Author: sudarshanpjadhav
 */

declare(strict_types=1);

namespace Ram\Api\Endpoints;

use Ram\Api\BrahmaandEndpoint;
use Ram\Core\RamRequest;
use Ram\Database\PatalDB;

/**
 * UsersEndpoint — Handles user data via API.
 */
class UsersEndpoint extends BrahmaandEndpoint
{
    public function ramFn_handle(RamRequest $ramVar_request, array $ramVar_pathParts): void
    {
        // Only admins can access user data
        if (!ram_current_user_can('manage_users')) {
            $this->ramFn_sendError('Unauthorized', 403);
        }

        $ramVar_method = $ramVar_request->ramFn_getMethod();
        
        switch ($ramVar_method) {
            case 'GET':
                $this->ramFn_handleGet($ramVar_request, $ramVar_pathParts);
                break;
            default:
                $this->ramFn_sendError('Method not allowed', 405);
        }
    }

    private function ramFn_handleGet(RamRequest $ramVar_request, array $ramVar_pathParts): void
    {
        $ramVar_id = $ramVar_pathParts[1] ?? null;

        if ($ramVar_id !== null) {
            $ramVar_user = PatalDB::ramFn_table('users')->ramFn_find((int) $ramVar_id);
            if ($ramVar_user) {
                unset($ramVar_user['password_hash']);
                $this->ramFn_sendSuccess($ramVar_user);
            } else {
                $this->ramFn_sendError('User not found', 404);
            }
        } else {
            $ramVar_users = PatalDB::ramFn_table('users')->ramFn_get();
            foreach ($ramVar_users as &$ramVar_u) {
                unset($ramVar_u['password_hash']);
            }
            $this->ramFn_sendSuccess($ramVar_users);
        }
    }
}
