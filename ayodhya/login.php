<?php
/**
 * Ramayan CMS — Admin Login (Ayodhya)
 * finggu.com | Author: sudarshanpjadhav
 */

declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

define('RAM_PATH', dirname(__DIR__));
require_once RAM_PATH . '/ram/bootstrap/RamLoader.php';
require_once RAM_PATH . '/global-helpers.php';

// Initialize
\Ram\Bootstrap\RamBootstrap::ramFn_init(false);

// Ensure core classes are available
if (!class_exists('Ram\Database\PatalDB')) {
    require_once RAM_PATH . '/ram/database/PatalDB.php';
}

use Ram\User\PanchjanyaSession;
use Ram\Security\SudarshanaNonce;

// If already logged in, go to dashboard
if (PanchjanyaSession::ramFn_isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$ramVar_error = '';

// Handle Login POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ramVar_user  = is_string($_POST['user'] ?? '') ? trim($_POST['user']) : '';
    $ramVar_pass  = is_string($_POST['pass'] ?? '') ? $_POST['pass'] : '';
    $ramVar_nonce = is_string($_POST['_ram_nonce'] ?? '') ? $_POST['_ram_nonce'] : '';

    if (SudarshanaNonce::ramFn_check($ramVar_nonce, 'ram_admin_login')) {
        if (PanchjanyaSession::ramFn_login($ramVar_user, $ramVar_pass)) {
            header('Location: index.php');
            exit;
        } else {
            $ramVar_error = 'Invalid credentials. Please try again.';
        }
    } else {
        $ramVar_error = 'Security check failed. Refresh the page.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter — Ayodhya Darbar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ram-saffron: #FF6B2B;
            --ram-gold: #F5A623;
            --ram-deep: #0F172A;
            --ram-text: #F8FAFC;
        }
        html, body {
            scrollbar-width: thin;
            scrollbar-color: #D6A84B #0F172A;
        }
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }
        ::-webkit-scrollbar-track {
            background: #0F172A;
        }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #F7D78A 0%, #D6A84B 48%, #A97822 100%);
            border: 2px solid #0F172A;
            border-radius: 999px;
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body {
            font-family: 'Inter', sans-serif;
            background: var(--ram-deep);
            color: var(--ram-text);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        body::before {
            content: ''; position: fixed; inset: 0;
            background: 
                radial-gradient(circle at 20% 20%, rgba(255, 107, 43, 0.05), transparent 40%),
                radial-gradient(circle at 80% 80%, rgba(108, 63, 196, 0.05), transparent 40%);
            z-index: -1;
        }
        .login-card {
            width: 100%; max-width: 400px;
            padding: 2.5rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .logo-box { text-align: center; margin-bottom: 2rem; }
        .logo-icon { 
            width: 60px; height: 60px; margin: 0 auto 1rem;
            background: linear-gradient(135deg, var(--ram-saffron), var(--ram-gold));
            border-radius: 15px; display: flex; align-items: center; justify-content: center;
            font-size: 2rem; box-shadow: 0 10px 20px rgba(255, 107, 43, 0.2);
        }
        .logo-text { font-family: 'Space Grotesk'; font-size: 1.5rem; font-weight: 700; letter-spacing: -0.02em; }
        
        .form-group { margin-bottom: 1.25rem; }
        label { display: block; font-size: 0.85rem; color: #94A3B8; margin-bottom: 0.5rem; }
        input {
            width: 100%; padding: 0.8rem 1rem; background: rgba(0,0,0,0.2);
            border: 1px solid rgba(255,255,255,0.1); border-radius: 10px;
            color: #fff; font-size: 0.95rem; outline: none; transition: border 0.2s;
        }
        input:focus { border-color: var(--ram-saffron); }
        
        .btn-login {
            width: 100%; padding: 0.9rem; margin-top: 1rem;
            background: linear-gradient(90deg, var(--ram-saffron), var(--ram-gold));
            border: none; border-radius: 10px; color: #1A0A2E;
            font-family: 'Space Grotesk'; font-weight: 700; font-size: 1rem;
            cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255, 107, 43, 0.4); }
        
        .error {
            background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2);
            color: #FCA5A5; padding: 0.75rem; border-radius: 8px; font-size: 0.85rem;
            margin-bottom: 1.5rem; text-align: center;
        }
        .footer-links { margin-top: 2rem; text-align: center; font-size: 0.8rem; color: #64748B; }
        .footer-links a { color: #94A3B8; text-decoration: none; }
        .footer-links a:hover { color: var(--ram-saffron); }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo-box">
            <div class="logo-icon">🏹</div>
            <div class="logo-text">AYODHYA</div>
        </div>

        <?php if ($ramVar_error): ?>
            <div class="error"><?= $ramVar_error ?></div>
        <?php endif; ?>

        <form method="POST">
            <?= SudarshanaNonce::ramFn_field('ram_admin_login') ?>
            <div class="form-group">
                <label>Panchjanya Username</label>
                <input type="text" name="user" required autofocus>
            </div>
            <div class="form-group">
                <label>Secret Phrase</label>
                <input type="password" name="pass" required>
            </div>
            <button type="submit" class="btn-login">Enter Ayodhya</button>
        </form>

        <div class="footer-links">
            <a href="../">← Back to Site</a>
        </div>
    </div>
</body>
</html>
