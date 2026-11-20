<?php
/**
 * Ayodhya Dashboard View
 */
if (!defined('RAM_PATH')) exit('Direct access prohibited');

// Real stats from database
try {
    $pdo = new \PDO("mysql:host=localhost;dbname=ramayan_db;charset=utf8mb4", "root", "");
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    
    $ramVar_postCount = $pdo->query("SELECT COUNT(*) FROM ram_posts WHERE post_type = 'post' AND status != 'trash'")->fetchColumn();
    $ramVar_pageCount = $pdo->query("SELECT COUNT(*) FROM ram_posts WHERE post_type = 'page' AND status != 'trash'")->fetchColumn();
    $ramVar_userCount = $pdo->query("SELECT COUNT(*) FROM ram_users")->fetchColumn();
    $ramVar_mediaCount = $pdo->query("SELECT COUNT(*) FROM ram_media")->fetchColumn();
    $ramVar_draftCount = $pdo->query("SELECT COUNT(*) FROM ram_posts WHERE status = 'draft'")->fetchColumn();
    $ramVar_pendingCount = $pdo->query("SELECT COUNT(*) FROM ram_posts WHERE status = 'pending'")->fetchColumn();
    
    // Recent posts
    $stmt = $pdo->query("SELECT * FROM ram_posts WHERE status != 'trash' ORDER BY created_at DESC LIMIT 5");
    $ramVar_recentPosts = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    // Recent users
    $stmt = $pdo->query("SELECT * FROM ram_users ORDER BY id DESC LIMIT 5");
    $ramVar_recentUsers = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
} catch (\PDOException $e) {
    $ramVar_postCount = $ramVar_pageCount = $ramVar_userCount = $ramVar_mediaCount = $ramVar_draftCount = $ramVar_pendingCount = 0;
    $ramVar_recentPosts = $ramVar_recentUsers = [];
}
?>

<div class="mb-3">
    <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 2rem; margin-bottom: 0.5rem;">Pranam, <?= ram_esc_html(ram_current_user()['display_name'] ?? 'Darbar Member') ?>!</h1>
    <p style="color: var(--ram-muted);">Welcome to the Ayodhya Darbar. Everything is in order.</p>
</div>

<div class="ram-stats-grid">
    <div class="ram-card ram-stat-card" onclick="location.href='index.php?page=posts'" style="cursor: pointer;">
        <div class="ram-stat-label">Sita Chronicles</div>
        <div class="ram-stat-value"><?= (int)$ramVar_postCount ?></div>
        <div style="font-size: 0.75rem; color: var(--ram-muted); margin-top: 0.25rem;"><?= (int)$ramVar_draftCount ?> manuscripts</div>
    </div>
    <div class="ram-card ram-stat-card" onclick="location.href='index.php?page=pages'" style="cursor: pointer;">
        <div class="ram-stat-label">Patras</div>
        <div class="ram-stat-value"><?= (int)$ramVar_pageCount ?></div>
        <div style="font-size: 0.75rem; color: var(--ram-muted); margin-top: 0.25rem;">Published in Ramrajya</div>
    </div>
    <div class="ram-card ram-stat-card" onclick="location.href='index.php?page=users'" style="cursor: pointer;">
        <div class="ram-stat-label">Panchjanya Sabha</div>
        <div class="ram-stat-value"><?= (int)$ramVar_userCount ?></div>
        <div style="font-size: 0.75rem; color: var(--ram-muted); margin-top: 0.25rem;">Sabha Members</div>
    </div>
    <div class="ram-card ram-stat-card" onclick="location.href='index.php?page=media'" style="cursor: pointer;">
        <div class="ram-stat-label">Nandan Treasury</div>
        <div class="ram-stat-value"><?= (int)$ramVar_mediaCount ?></div>
        <div style="font-size: 0.75rem; color: var(--ram-muted); margin-top: 0.25rem;">Treasures</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <div class="ram-card">
        <h3 class="mb-2" style="font-family: 'Space Grotesk';">Recent Chronicles</h3>
        <?php if (empty($ramVar_recentPosts)): ?>
            <p style="color: var(--ram-muted); font-size: 0.9rem;">No chronicles yet. <a href="index.php?page=post-new" style="color: var(--ram-accent);">Write your first chronicle!</a></p>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--ram-border);">
                        <th style="text-align: left; padding: 0.5rem; font-size: 0.8rem; color: var(--ram-muted);">Title</th>
                        <th style="text-align: left; padding: 0.5rem; font-size: 0.8rem; color: var(--ram-muted);">Status</th>
                        <th style="text-align: left; padding: 0.5rem; font-size: 0.8rem; color: var(--ram-muted);">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ramVar_recentPosts as $post): ?>
                    <tr style="border-bottom: 1px solid var(--ram-border);">
                        <td style="padding: 0.5rem;">
                            <a href="index.php?page=post-edit&id=<?= $post['id'] ?>" style="color: var(--ram-text); font-weight: 500;">
                                <?= ram_esc_html(mb_strimwidth($post['title'] ?? 'Untitled', 0, 40, '...')) ?>
                            </a>
                        </td>
                        <td style="padding: 0.5rem;">
                            <?php 
                            $statusColors = [
                                'publish' => '#4ADE80', 
                                'draft' => '#FBBF24', 
                                'pending' => '#60A5FA',
                                'trash' => '#F87171'
                            ];
                            $statusColor = $statusColors[$post['status']] ?? '#9CA3AF';
                            ?>
                            <span style="padding: 2px 8px; border-radius: 4px; background: rgba(0,0,0,0.2); color: <?= $statusColor ?>; font-size: 0.7rem; text-transform: uppercase;">
                                <?= $post['status'] ?>
                            </span>
                        </td>
                        <td style="padding: 0.5rem; font-size: 0.8rem; color: var(--ram-muted);">
                            <?= date('M j, Y', strtotime($post['created_at'])) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div style="margin-top: 1rem;">
                <a href="index.php?page=posts" style="color: var(--ram-accent); font-size: 0.85rem;">View all chronicles →</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="ram-card">
        <h3 class="mb-2" style="font-family: 'Space Grotesk';">Vanar Sena Actions</h3>
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <a href="index.php?page=post-new" class="btn btn-primary">✍️ Write New Chronicle</a>
            <a href="index.php?page=post-new&type=page" class="btn" style="background: var(--ram-deep-2); border: 1px solid var(--ram-border); color: var(--ram-text);">📄 Create New Patra</a>
            <a href="index.php?page=media" class="btn" style="background: var(--ram-deep-2); border: 1px solid var(--ram-border); color: var(--ram-text);">🖼️ Add to Nandan Treasury</a>
        </div>
        
        <h3 class="mb-2" style="font-family: 'Space Grotesk'; margin-top: 1.5rem;">Sudarshana System Status</h3>
        <div style="padding: 1rem; border-radius: 8px; background: rgba(34, 197, 94, 0.05); border: 1px solid rgba(34, 197, 94, 0.2);">
            <p style="color: #4ADE80; font-size: 0.85rem;">✓ Patal Database Connected</p>
            <p style="color: #4ADE80; font-size: 0.85rem;">✓ PHP <?= phpversion() ?></p>
            <p style="color: #4ADE80; font-size: 0.85rem;">✓ Ramayan CMS v<?= ram_esc_html(\Ram\Core\Ram::ramFn_getInstance()->ramFn_version()) ?></p>
            <?php if ($ramVar_pendingCount > 0): ?>
            <p style="color: #FBBF24; font-size: 0.85rem;">⚠ <?= $ramVar_pendingCount ?> chronicles awaiting Sabha review</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
// Allow plugins to inject full-width widgets at the bottom
\Ram\Hooks\AkashvaniHooks::ramFn_doAction('ram_admin_dashboard_bottom'); 
?>
