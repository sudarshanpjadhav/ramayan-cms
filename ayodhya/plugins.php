<?php
/**
 * Ramayan CMS — Plugins (Vaanar Sena)
 */
declare(strict_types=1);

if (!defined('RAM_PATH')) exit;

$ramVar_pluginDir = RAM_PATH . '/vaanar';
$ramVar_plugins = [];

if (is_dir($ramVar_pluginDir)) {
    foreach (glob($ramVar_pluginDir . '/*', GLOB_ONLYDIR) as $ramVar_dir) {
        $ramVar_slug = basename($ramVar_dir);
        $ramVar_file = $ramVar_dir . '/' . $ramVar_slug . '.php';
        $ramVar_name = $ramVar_slug;
        $ramVar_desc = 'No description';
        $ramVar_ver  = '1.0.0';

        if (file_exists($ramVar_file)) {
            $ramVar_headers = get_plugin_data_headers($ramVar_file);
            $ramVar_name = $ramVar_headers['name'] ?? $ramVar_slug;
            $ramVar_desc = $ramVar_headers['description'] ?? 'No description';
            $ramVar_ver  = $ramVar_headers['version'] ?? '1.0.0';
        }

        $ramVar_plugins[] = [
            'slug'        => $ramVar_slug,
            'name'        => $ramVar_name,
            'description' => $ramVar_desc,
            'version'     => $ramVar_ver,
            'active'      => false,
        ];
    }
}

$ramVar_activePlugins = ram_get_option('active_plugins', []);
foreach ($ramVar_plugins as &$ramVar_p) {
    $ramVar_p['active'] = in_array('vaanar/' . $ramVar_p['slug'] . '/' . $ramVar_p['slug'] . '.php', (array) $ramVar_activePlugins, false)
        || in_array($ramVar_p['slug'] . '/' . $ramVar_p['slug'] . '.php', (array) $ramVar_activePlugins, false);
}
unset($ramVar_p);

function get_plugin_data_headers(string $ramVar_file): array
{
    $ramVar_data = [];
    $ramVar_fp = fopen($ramVar_file, 'r');
    if (!$ramVar_fp) return $ramVar_data;
    $ramVar_contents = fread($ramVar_fp, 8192);
    fclose($ramVar_fp);

    preg_match('/Plugin Name:\s*(.+)/i', $ramVar_contents, $ramVar_m);
    $ramVar_data['name'] = isset($ramVar_m[1]) ? trim($ramVar_m[1]) : null;

    preg_match('/Description:\s*(.+)/i', $ramVar_contents, $ramVar_m);
    $ramVar_data['description'] = isset($ramVar_m[1]) ? trim($ramVar_m[1]) : null;

    preg_match('/Version:\s*(.+)/i', $ramVar_contents, $ramVar_m);
    $ramVar_data['version'] = isset($ramVar_m[1]) ? trim($ramVar_m[1]) : null;

    return $ramVar_data;
}
?>
<div class="ayodhya-page-header">
    <h1>Vaanar Sena Plugins</h1>
</div>

<div class="ram-card">
    <table class="ayodhya-table">
        <thead>
            <tr>
                <th>Plugin</th>
                <th>Description</th>
                <th>Version</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($ramVar_plugins)): ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 3rem; color: var(--ram-muted);">No plugins found.</td>
                </tr>
            <?php else: foreach ($ramVar_plugins as $ramVar_plugin): ?>
                <tr>
                    <td><b style="color: var(--ram-text);"><?= ram_esc_html($ramVar_plugin['name']) ?></b></td>
                    <td style="color: var(--ram-muted); font-size: 0.85rem;"><?= ram_esc_html($ramVar_plugin['description']) ?></td>
                    <td style="font-size: 0.8rem;"><?= ram_esc_html($ramVar_plugin['version']) ?></td>
                    <td>
                        <span style="padding: 2px 8px; border-radius: 4px; background: <?= $ramVar_plugin['active'] ? 'rgba(34, 197, 94, 0.1)' : 'rgba(100,100,100,0.1)' ?>; color: <?= $ramVar_plugin['active'] ? '#4ADE80' : '#94A3B8' ?>; font-size: 0.7rem; text-transform: uppercase; font-weight: 700;">
                            <?= $ramVar_plugin['active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
