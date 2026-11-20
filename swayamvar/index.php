<?php
/**
 * Ramayan CMS — Installer UI (Swayamvar)
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 *
 * The Swayamvar ceremony — where the setup choices are made.
 * This file shows the 5-field setup form and redirects to install.php.
 */

declare(strict_types=1);

// ── Security: block if already installed ─────────────────────────────────────
$ramVar_configFile = dirname(__DIR__) . '/ramayan-config.php';
if (file_exists($ramVar_configFile)) {
    header('Location: ../index.php');
    exit;
}

// ── Load installer helpers ────────────────────────────────────────────────────
$ramVar_step   = (int) ($_GET['step'] ?? 1);
$ramVar_errors = [];

// Detect base URL for form action
$ramVar_scheme  = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$ramVar_host    = $_SERVER['HTTP_HOST'] ?? 'localhost';
$ramVar_script  = $_SERVER['SCRIPT_NAME'] ?? '/swayamvar/index.php';
$ramVar_baseUrl = $ramVar_scheme . '://' . $ramVar_host . dirname($ramVar_script, 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>Swayamvar — Install Ramayan CMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ram-saffron:    #FF6B2B;
            --ram-gold:       #F5A623;
            --ram-deep:       #1A0A2E;
            --ram-deep-2:     #16213E;
            --ram-purple:     #6C3FC4;
            --ram-violet:     #8B5CF6;
            --ram-muted:      #94A3B8;
            --ram-card-bg:    rgba(255,255,255,0.04);
            --ram-border:     rgba(255,255,255,0.10);
            --ram-input-bg:   rgba(255,255,255,0.06);
            --ram-success:    #22C55E;
            --ram-error:      #EF4444;
            --ram-radius:     14px;
            --ram-radius-sm:  8px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--ram-deep);
            color: #E2E8F0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background */
        body::before {
            content: '';
            position: fixed; inset: 0;
            background:
                radial-gradient(ellipse 80% 50% at 20% 0%, rgba(108,63,196,0.25) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 100%, rgba(255,107,43,0.18) 0%, transparent 60%);
            pointer-events: none;
            z-index: 0;
        }

        /* Floating orbs */
        body::after {
            content: '';
            position: fixed;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(139,92,246,0.15) 0%, transparent 70%);
            top: -100px; right: -100px;
            animation: ramFloat 8s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes ramFloat {
            0%, 100% { transform: translateY(0) scale(1); }
            50%       { transform: translateY(30px) scale(1.05); }
        }

        .ram-installer {
            width: 100%;
            max-width: 560px;
            position: relative;
            z-index: 1;
        }

        /* Logo / Header */
        .ram-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .ram-logo {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
        }

        .ram-logo-icon {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, var(--ram-saffron), var(--ram-gold));
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.75rem;
            box-shadow: 0 8px 24px rgba(255,107,43,0.4);
        }

        .ram-logo-text {
            text-align: left;
        }

        .ram-logo-text strong {
            display: block;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(90deg, #fff 0%, var(--ram-gold) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }

        .ram-logo-text span {
            font-size: 0.75rem;
            color: var(--ram-muted);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .ram-header h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: #F8FAFC;
            margin-bottom: 0.5rem;
        }

        .ram-header p {
            color: var(--ram-muted);
            font-size: 0.9rem;
            line-height: 1.6;
        }

        /* Progress steps */
        .ram-steps {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            margin-bottom: 2rem;
        }

        .ram-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.35rem;
        }

        .ram-step-circle {
            width: 32px; height: 32px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            border: 2px solid var(--ram-border);
            color: var(--ram-muted);
            transition: all 0.3s ease;
        }

        .ram-step.active .ram-step-circle {
            background: linear-gradient(135deg, var(--ram-purple), var(--ram-violet));
            border-color: var(--ram-violet);
            color: #fff;
            box-shadow: 0 0 16px rgba(139,92,246,0.5);
        }

        .ram-step.done .ram-step-circle {
            background: var(--ram-success);
            border-color: var(--ram-success);
            color: #fff;
        }

        .ram-step-label {
            font-size: 0.65rem;
            color: var(--ram-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }

        .ram-step.active .ram-step-label { color: var(--ram-violet); }

        .ram-step-line {
            flex: 1;
            height: 2px;
            background: var(--ram-border);
            margin: 0 8px;
            margin-bottom: 20px;
        }

        /* Card */
        .ram-card {
            background: var(--ram-card-bg);
            border: 1px solid var(--ram-border);
            border-radius: var(--ram-radius);
            padding: 2rem;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow:
                0 0 0 1px rgba(255,255,255,0.05) inset,
                0 24px 64px rgba(0,0,0,0.4);
        }

        .ram-section-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            color: #CBD5E1;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--ram-border);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .ram-section-title::before {
            content: '';
            width: 3px; height: 16px;
            background: linear-gradient(180deg, var(--ram-saffron), var(--ram-gold));
            border-radius: 2px;
        }

        /* Form fields */
        .ram-field {
            margin-bottom: 1.25rem;
        }

        .ram-field label {
            display: block;
            font-size: 0.82rem;
            font-weight: 500;
            color: #CBD5E1;
            margin-bottom: 0.4rem;
        }

        .ram-field label .ram-required {
            color: var(--ram-saffron);
            margin-left: 2px;
        }

        .ram-field label .ram-hint {
            font-weight: 400;
            color: var(--ram-muted);
            font-size: 0.75rem;
            margin-left: 0.4rem;
        }

        .ram-input {
            width: 100%;
            padding: 0.7rem 1rem;
            background: var(--ram-input-bg);
            border: 1px solid var(--ram-border);
            border-radius: var(--ram-radius-sm);
            color: #F1F5F9;
            font-size: 0.9rem;
            font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            outline: none;
        }

        .ram-input:focus {
            border-color: var(--ram-violet);
            background: rgba(139,92,246,0.08);
            box-shadow: 0 0 0 3px rgba(139,92,246,0.15);
        }

        .ram-input::placeholder { color: rgba(148,163,184,0.5); }

        .ram-input-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        /* Divider */
        .ram-divider {
            height: 1px;
            background: var(--ram-border);
            margin: 1.5rem 0;
        }

        /* Error / notice */
        .ram-notice {
            padding: 0.85rem 1rem;
            border-radius: var(--ram-radius-sm);
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            line-height: 1.5;
        }

        .ram-notice.error {
            background: rgba(239,68,68,0.12);
            border: 1px solid rgba(239,68,68,0.3);
            color: #FCA5A5;
        }

        .ram-notice.info {
            background: rgba(139,92,246,0.10);
            border: 1px solid rgba(139,92,246,0.25);
            color: #C4B5FD;
        }

        /* CTA Button */
        .ram-btn-primary {
            width: 100%;
            padding: 0.85rem 1.5rem;
            background: linear-gradient(135deg, var(--ram-saffron) 0%, var(--ram-gold) 100%);
            color: #1A0A2E;
            font-weight: 700;
            font-size: 1rem;
            font-family: 'Space Grotesk', sans-serif;
            border: none;
            border-radius: var(--ram-radius-sm);
            cursor: pointer;
            transition: all 0.25s ease;
            letter-spacing: 0.02em;
            box-shadow: 0 4px 20px rgba(255,107,43,0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }

        .ram-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(255,107,43,0.5);
            background: linear-gradient(135deg, #FF7A40 0%, #F7B53A 100%);
        }

        .ram-btn-primary:active { transform: translateY(0); }

        /* Footer */
        .ram-footer {
            text-align: center;
            margin-top: 1.75rem;
            color: var(--ram-muted);
            font-size: 0.78rem;
        }

        .ram-footer a {
            color: var(--ram-violet);
            text-decoration: none;
        }

        .ram-footer a:hover { text-decoration: underline; }

        /* Requirements list */
        .ram-reqs {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .ram-reqs li {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.85rem;
            color: #94A3B8;
        }

        .ram-reqs li .check { color: var(--ram-success); font-size: 1rem; }
        .ram-reqs li .fail  { color: var(--ram-error);   font-size: 1rem; }

        @media (max-width: 480px) {
            .ram-input-grid { grid-template-columns: 1fr; }
            .ram-card { padding: 1.5rem; }
        }
    </style>
</head>
<body>

<div class="ram-installer">

    <!-- Logo -->
    <div class="ram-header">
        <div class="ram-logo">
            <div class="ram-logo-icon">🏹</div>
            <div class="ram-logo-text">
                <strong>Ramayan CMS</strong>
                <span>finggu.com</span>
            </div>
        </div>
        <h1>Swayamvar Installation Ceremony</h1>
        <p>Establish your Ramayan CMS kingdom.<br>You need a Patal database ready to continue.</p>
    </div>

    <!-- Steps -->
    <div class="ram-steps">
        <div class="ram-step <?= $ramVar_step >= 1 ? 'active' : '' ?>">
            <div class="ram-step-circle">1</div>
            <div class="ram-step-label">Kingdom Requirements</div>
        </div>
        <div class="ram-step-line"></div>
        <div class="ram-step <?= $ramVar_step >= 2 ? 'active' : '' ?>">
            <div class="ram-step-circle">2</div>
            <div class="ram-step-label">Patal Database</div>
        </div>
        <div class="ram-step-line"></div>
        <div class="ram-step <?= $ramVar_step >= 3 ? 'active' : '' ?>">
            <div class="ram-step-circle">3</div>
            <div class="ram-step-label">Kingdom & Gatekeeper</div>
        </div>
    </div>

    <?php
    // ── Step 1: Requirements check ────────────────────────────────────────
    $ramVar_phpOk    = PHP_MAJOR_VERSION >= 8 && PHP_MINOR_VERSION >= 1;
    $ramVar_pdoOk    = extension_loaded('pdo_mysql');
    $ramVar_mbOk     = extension_loaded('mbstring');
    $ramVar_jsonOk   = extension_loaded('json');
    $ramVar_curlOk   = extension_loaded('curl');
    $ramVar_writable = is_writable(dirname(__DIR__));
    $ramVar_allOk    = $ramVar_phpOk && $ramVar_pdoOk && $ramVar_mbOk && $ramVar_jsonOk && $ramVar_writable;

    /**
     * Helper: render a requirement row.
     */
    $ramVar_req = static function (bool $ramVar_pass, string $ramVar_label) use (&$ramVar_errors): string {
        $ramVar_icon = $ramVar_pass ? '<span class="check">✓</span>' : '<span class="fail">✗</span>';
        return '<li>' . $ramVar_icon . ' ' . htmlspecialchars($ramVar_label, ENT_QUOTES, 'UTF-8') . '</li>';
    };
    ?>

    <div class="ram-card">

        <!-- Requirements -->
        <div class="ram-section-title">Kingdom Requirements</div>

        <ul class="ram-reqs">
            <?= $ramVar_req($ramVar_phpOk,    'PHP 8.1+ (You have: ' . PHP_VERSION . ')') ?>
            <?= $ramVar_req($ramVar_pdoOk,    'PDO MySQL extension') ?>
            <?= $ramVar_req($ramVar_mbOk,     'Multibyte String (mbstring)') ?>
            <?= $ramVar_req($ramVar_jsonOk,   'JSON extension') ?>
            <?= $ramVar_req($ramVar_curlOk,   'cURL extension (recommended)') ?>
            <?= $ramVar_req($ramVar_writable, 'Root directory is writable') ?>
        </ul>

        <?php if (!$ramVar_allOk): ?>
        <div class="ram-notice error">
            ⚠️ Some requirements are not met. Please fix the issues above before continuing.
            Contact your hosting provider if you need help enabling PHP extensions.
        </div>
        <?php endif; ?>

        <div class="ram-divider"></div>

        <!-- The 5-Field Setup Form -->
        <form method="POST" action="install.php" id="ramInstallForm">

            <div class="ram-section-title">Patal Database Connection</div>

            <div class="ram-field">
                <label for="db_name">Patal Database Name <span class="ram-required">*</span></label>
                <input id="db_name" name="db_name" type="text" class="ram-input"
                       placeholder="e.g. my_ramayan_db"
                       value="<?= htmlspecialchars($_POST['db_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       required autocomplete="off">
            </div>

            <div class="ram-input-grid">
                <div class="ram-field">
                    <label for="db_user">Patal Database User <span class="ram-required">*</span></label>
                    <input id="db_user" name="db_user" type="text" class="ram-input"
                           placeholder="e.g. db_user"
                           value="<?= htmlspecialchars($_POST['db_user'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           required autocomplete="off">
                </div>
                <div class="ram-field">
                    <label for="db_password">Patal Database Secret <span class="ram-required">*</span></label>
                    <input id="db_password" name="db_password" type="password" class="ram-input"
                           placeholder="••••••••"
                           autocomplete="new-password">
                </div>
            </div>

            <div class="ram-input-grid">
                <div class="ram-field">
                    <label for="db_host">
                        Database Host
                        <span class="ram-hint">usually localhost</span>
                    </label>
                    <input id="db_host" name="db_host" type="text" class="ram-input"
                           placeholder="localhost"
                           value="<?= htmlspecialchars($_POST['db_host'] ?? 'localhost', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="ram-field">
                    <label for="db_prefix">
                        Table Prefix
                        <span class="ram-hint">default: ram_</span>
                    </label>
                    <input id="db_prefix" name="db_prefix" type="text" class="ram-input"
                           placeholder="ram_"
                           value="<?= htmlspecialchars($_POST['db_prefix'] ?? 'ram_', ENT_QUOTES, 'UTF-8') ?>"
                           pattern="[a-zA-Z_][a-zA-Z0-9_]*"
                           title="Only letters, numbers, underscores. Must start with letter or underscore.">
                </div>
            </div>

            <div class="ram-divider"></div>

            <div class="ram-section-title">Kingdom Information</div>

            <div class="ram-field">
                <label for="site_name">Site Name <span class="ram-required">*</span></label>
                <input id="site_name" name="site_name" type="text" class="ram-input"
                       placeholder="My Awesome Website"
                       value="<?= htmlspecialchars($_POST['site_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       required>
            </div>

            <div class="ram-field">
                <label for="site_url">
                    Site URL <span class="ram-required">*</span>
                    <span class="ram-hint">no trailing slash</span>
                </label>
                <input id="site_url" name="site_url" type="url" class="ram-input"
                       placeholder="<?= htmlspecialchars($ramVar_baseUrl, ENT_QUOTES, 'UTF-8') ?>"
                       value="<?= htmlspecialchars($_POST['site_url'] ?? $ramVar_baseUrl, ENT_QUOTES, 'UTF-8') ?>"
                       required>
            </div>

            <div class="ram-divider"></div>

            <div class="ram-section-title">Ayodhya Gatekeeper</div>

            <div class="ram-field">
                <label for="admin_username">Admin Username <span class="ram-required">*</span></label>
                <input id="admin_username" name="admin_username" type="text" class="ram-input"
                       placeholder="admin"
                       value="<?= htmlspecialchars($_POST['admin_username'] ?? 'admin', ENT_QUOTES, 'UTF-8') ?>"
                       required pattern="[a-zA-Z0-9_\-]{3,60}"
                       title="3-60 characters: letters, numbers, underscores, hyphens">
            </div>

            <div class="ram-input-grid">
                <div class="ram-field">
                    <label for="admin_email">Admin Email <span class="ram-required">*</span></label>
                    <input id="admin_email" name="admin_email" type="email" class="ram-input"
                           placeholder="admin@example.com"
                           value="<?= htmlspecialchars($_POST['admin_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           required>
                </div>
                <div class="ram-field">
                    <label for="admin_password">Admin Password <span class="ram-required">*</span></label>
                    <input id="admin_password" name="admin_password" type="password" class="ram-input"
                           placeholder="Min. 8 characters"
                           minlength="8"
                           required autocomplete="new-password">
                </div>
            </div>

            <!-- Hidden token to prevent installer re-submission -->
            <input type="hidden" name="install_token" value="<?= htmlspecialchars(
                bin2hex(random_bytes(16)),
                ENT_QUOTES, 'UTF-8'
            ) ?>">

            <?php if (!$ramVar_allOk): ?>
                <button type="submit" class="ram-btn-primary" disabled style="opacity:0.5;cursor:not-allowed;">
                    ⚠️ Fix Requirements First
                </button>
            <?php else: ?>
                <button type="submit" class="ram-btn-primary" id="ramSubmitBtn">
                    <span>🏹</span> Begin the Swayamvar
                </button>
            <?php endif; ?>

        </form>

    </div><!-- /.ram-card -->

    <div class="ram-footer">
        <p>
            Ramayan CMS v1.0.0-alpha &nbsp;·&nbsp;
            <a href="https://finggu.com" target="_blank" rel="noopener">finggu.com</a>
            &nbsp;·&nbsp;
            <a href="https://github.com/sudarshanpjadhav/ramayan-cms" target="_blank" rel="noopener">GitHub</a>
        </p>
    </div>

</div><!-- /.ram-installer -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ramVar_form   = document.getElementById('ramInstallForm');
    const ramVar_btn    = document.getElementById('ramSubmitBtn');

    if (!ramVar_form || !ramVar_btn) return;

    ramVar_form.addEventListener('submit', function (ramVar_e) {
        // Disable button to prevent double submission
        ramVar_btn.disabled = true;
        ramVar_btn.innerHTML = '<span style="display:inline-block;animation:spin 1s linear infinite">⟳</span> Installing...';
        ramVar_btn.style.opacity = '0.85';
    });
});
</script>
<style>@keyframes spin { from{transform:rotate(0)} to{transform:rotate(360deg)} }</style>

</body>
</html>
