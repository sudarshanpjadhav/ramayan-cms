<?php
/**
 * Ramayan CMS — Ayodhya Admin Panel Entry
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

// 1. Boot the CMS (we are in /ayodhya/, so RAM_PATH is parent)
define('RAM_PATH', dirname(__DIR__));
require_once RAM_PATH . '/ram/bootstrap/RamLoader.php';
require_once RAM_PATH . '/global-helpers.php';

// Initialize the engine (Brahmastra)
\Ram\Bootstrap\RamBootstrap::ramFn_init(false);

// Ensure core classes are available
if (!class_exists('Ram\Database\PatalDB')) {
    require_once RAM_PATH . '/ram/database/PatalDB.php';
}

$ramVar_cms = \Ram\Core\Ram::ramFn_getInstance();
$ramVar_cms->ramFn_setAdmin(true);

use Ram\User\PanchjanyaSession;
use Ram\Hooks\AkashvaniHooks;

// 2. Authentication Check
if (!PanchjanyaSession::ramFn_isLoggedIn()) {
    // Redirect to login if not authenticated
    header('Location: login.php');
    exit;
}

// Check for admin/editor capabilities
if (!PanchjanyaSession::ramFn_currentUserCan('read')) {
    die('Sudarshana Security: Access Denied. You do not have permission to access Ayodhya.');
}

// 3. Admin Routing
$ramVar_page = $_GET['page'] ?? 'dashboard';

// Allow plugins to register admin pages
AkashvaniHooks::ramFn_doAction('ram_admin_init');

// 4. Render Admin Layout
require_once 'admin-header.php';
require_once 'admin-sidebar.php';

// Ensure meta-boxes class is available before page dispatch
require_once 'meta-boxes.php';

echo '<main class="ayodhya-main">';
echo '<div class="ayodhya-content-wrap">';

// Dispatch the admin page content
switch ($ramVar_page) {
    case 'dashboard':
        require_once 'dashboard.php';
        break;

    case 'posts':
    case 'post-new':
    case 'post-edit':
    case 'categories':
    case 'tags':
        require_once 'posts.php';
        break;

    case 'pages':
        require_once 'pages.php';
        break;

    case 'page-new':
    case 'page-edit':
        require_once 'posts.php';
        break;

    case 'media':
        require_once 'media.php';
        break;

    case 'customizer':
        require_once 'customizer.php';
        break;

    case 'plugins':
        require_once 'plugins.php';
        break;

    case 'themes':
        require_once 'themes.php';
        break;

    case 'users':
        require_once 'users.php';
        break;

    case 'settings':
        require_once 'settings.php';
        break;

    case 'api':
        require_once 'api-handler.php';
        break;

    default:
        // Allow plugins to handle their own pages
        AkashvaniHooks::ramFn_doAction('ram_admin_page_' . $ramVar_page);
        break;
}

echo '</div>'; // ayodhya-content-wrap
echo '</main>';

require_once 'admin-footer.php';
