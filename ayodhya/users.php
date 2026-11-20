<?php
/**
 * Ramayan CMS — Users Management
 */
declare(strict_types=1);

if (!defined('RAM_PATH')) exit;

$ramVar_users = [];

try {
    $pdo = new \PDO("mysql:host=localhost;dbname=ramayan_db;charset=utf8mb4", "root", "");
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SELECT * FROM ram_users ORDER BY id DESC LIMIT 50");
    $ramVar_users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
} catch (\PDOException $e) {
    $ramVar_users = [];
}
?>
<div class="ayodhya-page-header">
    <h1>Panchjanya Sabha</h1>
</div>

<div class="ram-card">
    <table class="ayodhya-table">
        <thead>
            <tr>
                <th style="width: 40px;"><input type="checkbox"></th>
                <th>User</th>
                <th>Email</th>
                <th>Role</th>
                <th>Chronicles</th>
                <th>Joined</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($ramVar_users)): ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 3rem; color: var(--ram-muted);">
                    <div style="font-size: 2rem; margin-bottom: 1rem;">👤</div>
                    No users found.
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($ramVar_users as $ramVar_user): 
                $postCount = 0;
                try {
                    $postCount = (int)$pdo->query("SELECT COUNT(*) FROM ram_posts WHERE author_id = " . (int)$ramVar_user['id'])->fetchColumn();
                } catch (\PDOException $e) {}
                $avatarLetter = strtoupper(substr($ramVar_user['display_name'] ?? $ramVar_user['user_login'] ?? 'U', 0, 1));
                $displayName = ram_esc_html($ramVar_user['display_name'] ?? $ramVar_user['user_login'] ?? 'Unknown');
                $userEmail = ram_esc_html($ramVar_user['user_email'] ?? '-');
                $role = $ramVar_user['role'] ?? 'subscriber';
                $created = !empty($ramVar_user['created_at']) ? date('M j, Y', strtotime($ramVar_user['created_at'])) : '-';
            ?>
            <tr>
                <td><input type="checkbox"></td>
                <td>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--ram-saffron); display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--ram-deep);">
                            <?= $avatarLetter ?>
                        </div>
                        <div>
                            <div style="font-weight: 600; color: var(--ram-text);"><?= $displayName ?></div>
                            <div style="font-size: 0.75rem; margin-top: 0.25rem;">
                                <a href="#" style="color: var(--ram-accent);">Edit</a>
                            </div>
                        </div>
                    </div>
                </td>
                <td style="color: var(--ram-muted);"><?= $userEmail ?></td>
                <td><span style="padding: 2px 8px; border-radius: 4px; background: var(--ram-deep-2); color: var(--ram-accent); font-size: 0.7rem; text-transform: capitalize;"><?= $role ?></span></td>
                <td style="color: var(--ram-muted);"><?= $postCount ?></td>
                <td style="font-size: 0.8rem; color: var(--ram-muted);"><?= $created ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>