<?php
/**
 * Ramayan CMS — Settings Page
 * finggu.com | Author: sudarshanpjadhav
 */

declare(strict_types=1);

if (!defined('RAM_PATH')) exit;

$ramVar_action = $_POST['ram_action'] ?? '';
$ramVar_status_msg = '';

if ($ramVar_action === 'save_settings') {
    if (!ram_verify_nonce($_POST['_ram_nonce'] ?? '', 'save_settings')) {
        $ramVar_status_msg = '<div class="alert alert-error">Security check failed.</div>';
    } else {
        ram_update_option('site_name', $_POST['site_name'] ?? '');
        ram_update_option('site_description', $_POST['site_description'] ?? '');
        ram_update_option('admin_email', $_POST['admin_email'] ?? '');
        $ramVar_status_msg = '<div class="alert alert-success">Rajya settings sealed successfully!</div>';
    }
}

?>
<div class="ayodhya-page-header">
    <h1>Rajya Settings</h1>
</div>

<div class="ram-card">
    <?= $ramVar_status_msg ?>
    <form method="POST">
        <?= ram_nonce_field('save_settings') ?>
        <input type="hidden" name="ram_action" value="save_settings">

        <div style="max-width: 600px;">
            <div class="mb-3">
                <label class="ram-label">Kingdom Name</label>
                <input type="text" name="site_name" class="ram-input" value="<?= ram_esc_attr(ram_get_option('site_name', 'Ramayan CMS')) ?>">
            </div>

            <div class="mb-3">
                <label class="ram-label">Kingdom Motto</label>
                <input type="text" name="site_description" class="ram-input" value="<?= ram_esc_attr(ram_get_option('site_description', 'Built for the Future')) ?>">
            </div>

            <div class="mb-3">
                <label class="ram-label">Ayodhya Gatekeeper Email</label>
                <input type="email" name="admin_email" class="ram-input" value="<?= ram_esc_attr(ram_get_option('admin_email', 'admin@example.com')) ?>">
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </form>
</div>
