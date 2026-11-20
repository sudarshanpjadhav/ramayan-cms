<?php
/**
 * Ramayan CMS — Entry Point
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

// ─── Core Path Constants ─────────────────────────────────────────────────────
define('RAM_PATH',    __DIR__);
define('RAM_VERSION', '1.0.0-alpha');

// ─── Detect if installed ──────────────────────────────────────────────────────
$ramVar_configFile = RAM_PATH . '/ramayan-config.php';

if (!file_exists($ramVar_configFile)) {
    // Not installed — redirect to Swayamvar installer
    if (!headers_sent()) {
        header('Location: swayamvar/index.php');
        exit;
    }
    echo '<meta http-equiv="refresh" content="0;url=swayamvar/index.php">';
    exit;
}

// ─── Load config ─────────────────────────────────────────────────────────────
require_once $ramVar_configFile;

// ─── Load autoloader ─────────────────────────────────────────────────────────
require_once RAM_PATH . '/ram/bootstrap/RamLoader.php';

// ─── Load global helpers (must be before bootstrap) ──────────────────────────
require_once RAM_PATH . '/global-helpers.php';

// ─── Boot the CMS ─────────────────────────────────────────────────────────────
use Ram\Bootstrap\RamBootstrap;

RamBootstrap::ramFn_init();
