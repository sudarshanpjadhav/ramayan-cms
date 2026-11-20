<?php
/**
 * Ramayan CMS — Themes Manager (Lanka)
 */
declare(strict_types=1);

if (!defined('RAM_PATH')) exit;

// Handle theme activation
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['ram_action'] ?? '') === 'activate_theme') {
    if (!ram_current_user_can('switch_themes')) {
        http_response_code(403);
        exit('Sudarshana Security: You do not have permission to switch themes.');
    }
    if (!ram_verify_nonce((string) ($_POST['_ram_nonce'] ?? ''), 'activate_theme')) {
        http_response_code(403);
        exit('Sudarshana Security: Invalid theme activation request.');
    }
    $ramVar_theme = basename((string) ($_POST['theme'] ?? ''));
    if ($ramVar_theme !== '') {
        ram_update_option('active_theme', $ramVar_theme);
        ram_redirect('index.php?page=themes&activated=1');
    }
}

$ramVar_themeDir = RAM_PATH . '/lanka';
$ramVar_themes = [];
$ramVar_active = ram_get_option('active_theme', 'ram-default');

if (is_dir($ramVar_themeDir)) {
    foreach (glob($ramVar_themeDir . '/*', GLOB_ONLYDIR) as $ramVar_dir) {
        $ramVar_slug = basename($ramVar_dir);
        $ramVar_style = $ramVar_dir . '/style.css';
        $ramVar_name = $ramVar_slug;
        $ramVar_ver = '1.0';
        $ramVar_desc = '';
        $ramVar_author = '';
        
        if (file_exists($ramVar_style)) {
            $ramVar_contents = file_get_contents($ramVar_style);
            preg_match('/Theme Name:\s*(.+)/i', $ramVar_contents, $ramVar_m);
            $ramVar_name = isset($ramVar_m[1]) ? trim($ramVar_m[1]) : $ramVar_slug;
            preg_match('/Version:\s*(.+)/i', $ramVar_contents, $ramVar_m);
            $ramVar_ver = isset($ramVar_m[1]) ? trim($ramVar_m[1]) : '1.0';
            preg_match('/Description:\s*(.+)/i', $ramVar_contents, $ramVar_m);
            $ramVar_desc = isset($ramVar_m[1]) ? trim($ramVar_m[1]) : '';
            preg_match('/Author:\s*(.+)/i', $ramVar_contents, $ramVar_m);
            $ramVar_author = isset($ramVar_m[1]) ? trim($ramVar_m[1]) : '';
            preg_match('/Tags:\s*(.+)/i', $ramVar_contents, $ramVar_m);
            $ramVar_tags = isset($ramVar_m[1]) ? trim($ramVar_m[1]) : '';
        }

        $ramVar_screenshot = '';
        foreach (['screenshot.png', 'screenshot.jpg', 'screenshot.webp'] as $ss) {
            if (file_exists($ramVar_dir . '/' . $ss)) {
                $ramVar_screenshot = '/lanka/' . $ramVar_slug . '/' . $ss;
                break;
            }
        }

        $ramVar_themes[] = [
            'slug' => $ramVar_slug,
            'name' => $ramVar_name,
            'version' => $ramVar_ver,
            'description' => $ramVar_desc,
            'author' => $ramVar_author,
            'tags' => $ramVar_tags,
            'screenshot' => $ramVar_screenshot,
            'active' => $ramVar_slug === $ramVar_active,
        ];
    }
}
?>

<style>
.themes-container { max-width: 1400px; margin: 0 auto; }
.themes-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.themes-header h1 { font-family: 'Space Grotesk'; font-size: 1.75rem; }
.themes-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 2rem; }
.theme-card { background: var(--ram-card); border-radius: 16px; overflow: hidden; border: 1px solid var(--ram-border); transition: all 0.3s ease; position: relative; }
.theme-card:hover { transform: translateY(-8px); box-shadow: 0 20px 60px rgba(0,0,0,0.3); border-color: var(--ram-accent); }
.theme-card.active { border-color: var(--ram-saffron); box-shadow: 0 0 0 2px rgba(255,107,43,0.2); }
.theme-preview { aspect-ratio: 16/10; background: linear-gradient(135deg, var(--ram-deep) 0%, var(--ram-deep-2) 100%); position: relative; overflow: hidden; }
.theme-preview img { width: 100%; height: 100%; object-fit: cover; }
.theme-preview .placeholder { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 4rem; opacity: 0.2; }
.theme-badge { position: absolute; top: 1rem; right: 1rem; background: var(--ram-saffron); color: white; padding: 0.35rem 1rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
.theme-info { padding: 1.5rem; }
.theme-name { font-family: 'Space Grotesk'; font-size: 1.2rem; font-weight: 700; margin-bottom: 0.25rem; display: flex; align-items: center; justify-content: space-between; }
.theme-version { font-size: 0.8rem; color: var(--ram-muted); font-weight: 400; }
.theme-desc { font-size: 0.9rem; color: var(--ram-muted); margin: 0.75rem 0; line-height: 1.5; min-height: 2.7em; }
.theme-meta { display: flex; gap: 1rem; font-size: 0.8rem; color: var(--ram-muted); margin-bottom: 1.25rem; }
.theme-actions { display: flex; gap: 0.75rem; }
.theme-actions .btn { flex: 1; text-align: center; }
.theme-tags { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1rem; }
.theme-tag { background: var(--ram-deep); padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.7rem; color: var(--ram-muted); }
.live-preview-btn { position: absolute; top: 1rem; left: 1rem; background: rgba(0,0,0,0.7); color: white; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.8rem; opacity: 0; transition: opacity 0.3s; }
.theme-preview:hover .live-preview-btn { opacity: 1; }
</style>

<?php if (isset($_GET['activated'])): ?>
<div class="alert alert-success" style="margin-bottom: 1.5rem;">✅ Theme activated successfully! <a href="/" target="_blank" style="color: var(--ram-accent);">View your site →</a></div>
<?php endif; ?>

<div class="themes-container">
    <div class="themes-header">
        <div>
            <h1>🎨 Lanka Themes</h1>
            <p style="color: var(--ram-muted); margin-top: 0.5rem;">Transform your website's appearance with Lanka's beautiful attire.</p>
        </div>
        <a href="https://ramayan-cms.com/themes" target="_blank" class="btn">➕ Add New Theme</a>
    </div>

    <div class="themes-grid">
        <?php foreach ($ramVar_themes as $theme): ?>
        <div class="theme-card <?= $theme['active'] ? 'active' : '' ?>">
            <div class="theme-preview">
                <?php if ($theme['screenshot']): ?>
                <img src="<?= $theme['screenshot'] ?>" alt="<?= $theme['name'] ?>">
                <?php else: ?>
                <div class="placeholder">🎨</div>
                <?php endif; ?>
                
                <a href="/" target="_blank" class="live-preview-btn">👁 Live Preview</a>
                
                <?php if ($theme['active']): ?>
                <span class="theme-badge">Active</span>
                <?php endif; ?>
            </div>
            
            <div class="theme-info">
                <div class="theme-name">
                    <?= htmlspecialchars($theme['name']) ?>
                    <span class="theme-version">v<?= htmlspecialchars($theme['version']) ?></span>
                </div>
                
                <?php if ($theme['author']): ?>
                <div style="font-size: 0.85rem; color: var(--ram-muted);">by <?= htmlspecialchars($theme['author']) ?></div>
                <?php endif; ?>
                
                <?php if ($theme['description']): ?>
                <p class="theme-desc"><?= htmlspecialchars($theme['description']) ?></p>
                <?php endif; ?>
                
                <?php if ($theme['tags']): ?>
                <div class="theme-tags">
                    <?php foreach (explode(',', $theme['tags']) as $tag): ?>
                    <span class="theme-tag"><?= trim($tag) ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <div class="theme-actions">
                    <?php if ($theme['active']): ?>
                    <a href="index.php?page=customizer" class="btn">🎨 Customize</a>
                    <a href="/" target="_blank" class="btn">👁 View Site</a>
                    <?php else: ?>
                    <form method="POST" action="index.php?page=themes" style="flex:1;" onsubmit="return confirm('Activate <?= htmlspecialchars($theme['name']) ?>?')">
                        <?= ram_nonce_field('activate_theme') ?>
                        <input type="hidden" name="ram_action" value="activate_theme">
                        <input type="hidden" name="theme" value="<?= ram_esc_attr($theme['slug']) ?>">
                        <button type="submit" class="btn btn-primary" style="width:100%;">⚡ Activate</button>
                    </form>
                    <a href="#" class="btn">ℹ️ Details</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($ramVar_themes)): ?>
    <div class="ram-card" style="text-align: center; padding: 4rem;">
        <div style="font-size: 4rem; margin-bottom: 1rem;">🎨</div>
        <h3 style="margin-bottom: 0.5rem;">No themes found</h3>
        <p style="color: var(--ram-muted);">Install themes in /lanka/ folder to see them here.</p>
    </div>
    <?php endif; ?>
    
    <div style="margin-top: 3rem; padding: 2rem; background: var(--ram-card); border-radius: 12px; border: 1px dashed var(--ram-border);">
        <h3 style="margin-bottom: 1rem;">💡 Want more themes?</h3>
        <p style="color: var(--ram-muted); margin-bottom: 1rem;">Themes are stored in the <code>/lanka/</code> folder. Each theme needs:</p>
        <ul style="color: var(--ram-muted); margin-left: 1.5rem; line-height: 2;">
            <li><code>style.css</code> - Theme metadata (name, description, author)</li>
            <li><code>index.html.php</code> - Main template file</li>
            <li><code>screenshot.png</code> - Theme preview image (optional)</li>
        </ul>
    </div>
</div>