<?php
/**
 * Ramayan CMS — Internal Admin API Handler
 * finggu.com | Author: sudarshanpjadhav
 */

declare(strict_types=1);

if (!defined('RAM_PATH')) exit;

$ramVar_endpoint = $_GET['endpoint'] ?? '';
$ramVar_request  = new \Ram\Core\RamRequest();

// Use Brahmaand logic but for internal admin context
$ramVar_api = \Ram\Api\BrahmaandAPI::ramFn_getInstance();
$ramVar_api->ramFn_handleInternal($ramVar_endpoint, $ramVar_request);
