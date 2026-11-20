<?php
/**
 * Ramayan CMS — Admin Logout
 */
declare(strict_types=1);

define('RAM_PATH', dirname(__DIR__));
require_once RAM_PATH . '/ram/bootstrap/RamLoader.php';
require_once RAM_PATH . '/global-helpers.php';

\Ram\Bootstrap\RamBootstrap::ramFn_init(false);
\Ram\User\PanchjanyaSession::ramFn_logout();

header('Location: login.php');
exit;
