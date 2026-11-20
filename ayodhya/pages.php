<?php
/**
 * Ramayan CMS — Pages Management
 */
declare(strict_types=1);

use Ram\Database\PatalDB;
use Ram\Hooks\AkashvaniHooks;

if (!defined('RAM_PATH')) exit;

$ramVar_pageAction = $_POST['ram_action'] ?? ($_GET['ram_action'] ?? '');

// Handle page save/update - reuse posts logic
require_once __DIR__ . '/posts.php';

$ramVar_status = $_GET['status'] ?? 'publish';
$ramVar_pages = ram_get_posts(['post_type' => 'page', 'limit' => 50, 'status' => $ramVar_status === 'any' ? 'publish' : $ramVar_status]);
?>
<div class="ayodhya-page-header">
    <h1>Ayodhya Patras</h1>
    <a href="index.php?page=post-new&type=page" class="btn btn-primary"><span>+</span> Create Patra</a>
</div>

<div class="ram-card mb-3">
    <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; align-items: center;">
        <?php 
        try {
            $pdo = new \PDO("mysql:host=localhost;dbname=ramayan_db;charset=utf8mb4", "root", "");
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $allCount = (int)$pdo->query("SELECT COUNT(*) FROM ram_posts WHERE post_type = 'page' AND status != 'trash'")->fetchColumn();
            $pubCount = (int)$pdo->query("SELECT COUNT(*) FROM ram_posts WHERE post_type = 'page' AND status = 'publish'")->fetchColumn();
            $draftCount = (int)$pdo->query("SELECT COUNT(*) FROM ram_posts WHERE post_type = 'page' AND status = 'draft'")->fetchColumn();
            $trashCount = (int)$pdo->query("SELECT COUNT(*) FROM ram_posts WHERE post_type = 'page' AND status = 'trash'")->fetchColumn();
        } catch (\PDOException $e) {
            $allCount = $pubCount = $draftCount = $trashCount = 0;
        }
        ?>
        <a href="index.php?page=pages" style="color: var(--ram-saffron); font-weight: 600; font-size: 0.9rem; text-decoration: none;">All (<?= $allCount ?>)</a>
        <span style="color: var(--ram-border);">|</span>
        <a href="index.php?page=pages&status=publish" style="color: var(--ram-muted); font-size: 0.9rem; text-decoration: none;">Published (<?= $pubCount ?>)</a>
        <span style="color: var(--ram-border);">|</span>
        <a href="index.php?page=pages&status=draft" style="color: var(--ram-muted); font-size: 0.9rem; text-decoration: none;">Drafts (<?= $draftCount ?>)</a>
        <span style="color: var(--ram-border);">|</span>
        <a href="index.php?page=pages&status=trash" style="color: var(--ram-muted); font-size: 0.9rem; text-decoration: none;">Trash (<?= $trashCount ?>)</a>
    </div>

    <table class="ayodhya-table">
        <thead>
            <tr>
                <th style="width: 40px;"><input type="checkbox"></th>
                <th>Title</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($ramVar_pages)): ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 3rem; color: var(--ram-muted);">
                        <div style="font-size: 2rem; margin-bottom: 1rem;">📄</div>
                        No pages found.
                    </td>
                </tr>
            <?php else: foreach ($ramVar_pages as $ramVar_page): ?>
                <tr>
                    <td><input type="checkbox"></td>
                    <td>
                        <div style="font-weight: 600; color: var(--ram-text);"><?= ram_esc_html($ramVar_page['title']) ?></div>
                        <div style="font-size: 0.75rem; margin-top: 0.25rem;">
                            <a href="index.php?page=post-edit&id=<?= $ramVar_page['id'] ?>" style="color: var(--ram-accent);">Edit</a>
                            <span style="color: var(--ram-border);">|</span>
                            <a href="index.php?page=pages&action=trash&id=<?= $ramVar_page['id'] ?>" style="color: #F87171;" onclick="return confirm('Move to trash?')">Trash</a>
                            <span style="color: var(--ram-border);">|</span>
                            <a href="../?p=<?= $ramVar_page['id'] ?>" target="_blank" style="color: var(--ram-muted);">View</a>
                        </div>
                    </td>
                    <td><span style="padding: 2px 8px; border-radius: 4px; background: <?= $ramVar_page['status'] === 'publish' ? 'rgba(34, 197, 94, 0.1)' : 'rgba(245, 158, 11, 0.1)' ?>; color: <?= $ramVar_page['status'] === 'publish' ? '#4ADE80' : '#FBBF24' ?>; font-size: 0.7rem; text-transform: uppercase; font-weight: 700;"><?= $ramVar_page['status'] ?></span></td>
                    <td style="font-size: 0.8rem; color: var(--ram-muted);"><?= date('Y/m/d', strtotime($ramVar_page['created_at'])) ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
