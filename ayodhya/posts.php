<?php
/**
 * Ramayan CMS — Ayodhya Posts Management
 * finggu.com | Author: sudarshanpjadhav
 */

declare(strict_types=1);

use Ram\Database\PatalDB;
use Ram\Hooks\AkashvaniHooks;

if (!defined('RAM_PATH')) exit;

$ramVar_page   = $_GET['page'] ?? 'posts';
$ramVar_action = $_POST['ram_action'] ?? ($_GET['ram_action'] ?? '');
$ramVar_status_msg = '';

// ─── Handle Post Saving ───
if ($ramVar_action === 'save_post') {
    $ramVar_nonce = $_POST['_ram_nonce'] ?? '';
    if (!ram_verify_nonce($ramVar_nonce, 'save_post')) {
        $ramVar_status_msg = '<div class="alert alert-error">Security check failed. Nonce: ' . substr($ramVar_nonce, 0, 10) . '</div>';
    } else {
        $ramVar_postId   = (int) ($_POST['post_id'] ?? 0);
        $ramVar_title   = $_POST['post_title'] ?? '';
        $ramVar_content = $_POST['post_content'] ?? '';
        $ramVar_status  = $_POST['post_status'] ?? 'draft';
        $ramVar_type    = $_POST['post_type'] ?? 'post';
        $ramVar_slug    = ram_sanitize_slug($ramVar_title ?: ($ramVar_type . '-' . time()));
        $ramVar_featuredImage = $_POST['featured_image'] ?? '';
        $ramVar_categories = $_POST['categories'] ?? [];
        $ramVar_tags = $_POST['tags'] ?? [];
        $ramVar_newCategory = $_POST['new_category'] ?? '';
        $ramVar_newTags = $_POST['new_tags'] ?? '';

        // Create new category if provided
        if (!empty($ramVar_newCategory)) {
            $ramVar_newCatId = ram_create_category($ramVar_newCategory);
            if ($ramVar_newCatId) {
                $ramVar_categories[] = $ramVar_newCatId;
            }
        }

        // Create new tags if provided
        if (!empty($ramVar_newTags)) {
            $ramVar_tagNames = array_filter(array_map('trim', explode(',', $ramVar_newTags)));
            foreach ($ramVar_tagNames as $ramVar_tagName) {
                $ramVar_newTagId = ram_create_tag($ramVar_tagName);
                if ($ramVar_newTagId) {
                    $ramVar_tags[] = $ramVar_newTagId;
                }
            }
        }

        // Debug log
        error_log("Saving post: $ramVar_title | $ramVar_content | $ramVar_status");

        try {
            $ramVar_postData = [
                'title'         => $ramVar_title,
                'content'      => $ramVar_content,
                'slug'         => $ramVar_slug,
                'status'       => $ramVar_status,
                'post_type'    => $ramVar_type,
                'featured_image' => $ramVar_featuredImage,
            ];

            if ($ramVar_postId > 0) {
                $ramVar_result = ram_update_post($ramVar_postId, $ramVar_postData);
                $ramVar_label = $ramVar_type === 'page' ? 'Patra' : 'Chronicle';
                $ramVar_status_msg = '<div class="alert alert-success">' . $ramVar_label . ' updated successfully!</div>';
            } else {
                $ramVar_result = ram_insert_post($ramVar_postData);
                error_log("Insert result: " . var_export($ramVar_result, true));
                $ramVar_label = $ramVar_type === 'page' ? 'Patra' : 'Chronicle';
                $ramVar_status_msg = '<div class="alert alert-success">' . $ramVar_label . ' saved successfully! Result: ' . var_export($ramVar_result, true) . '</div>';
            }

            if ($ramVar_result && is_int($ramVar_result)) {
                ram_set_post_terms($ramVar_result, $ramVar_categories, 'category');
                ram_set_post_terms($ramVar_result, $ramVar_tags, 'post_tag');
                AkashvaniHooks::ramFn_doAction('ram_save_post', $ramVar_result);

                $ramVar_redirectPage = $ramVar_type === 'page' ? 'pages' : 'posts';
                ram_redirect('index.php?page=' . $ramVar_redirectPage . '&saved=1');
            }
        } catch (\Throwable $e) {
            $ramVar_status_msg = '<div class="alert alert-error">Error: ' . $e->getMessage() . '</div>';
        }
    }
}

// ─── Handle Trash/Delete Actions ───
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['ram_action'] ?? '') === 'post_action') {
    $ramVar_nonce = (string) ($_POST['_ram_nonce'] ?? '');
    $ramVar_action = (string) ($_POST['action'] ?? '');
    $ramVar_postId = (int) ($_POST['id'] ?? 0);

    if (!ram_verify_nonce($ramVar_nonce, 'post_action_' . $ramVar_postId)) {
        http_response_code(403);
        exit('Sudarshana Security: Invalid post action.');
    }
    if (!ram_current_user_can('edit_posts')) {
        http_response_code(403);
        exit('Sudarshana Security: You do not have permission to manage posts.');
    }

    if ($ramVar_action === 'trash' && $ramVar_postId > 0) {
        ram_trash_post($ramVar_postId);
        ram_redirect('index.php?page=posts&trashed=1');
    } elseif ($ramVar_action === 'delete' && $ramVar_postId > 0) {
        ram_delete_post($ramVar_postId, true);
        ram_redirect('index.php?page=posts&deleted=1');
    } elseif ($ramVar_action === 'restore' && $ramVar_postId > 0) {
        ram_update_post($ramVar_postId, ['status' => 'draft']);
        ram_redirect('index.php?page=posts&restored=1');
    }
}

echo '<div class="ayodhya-posts-container">';
echo $ramVar_status_msg;

if (isset($_GET['saved'])) {
    echo '<div class="alert alert-success">Chronicle published in Ramrajya successfully!</div>';
}
if (isset($_GET['trashed'])) {
    echo '<div class="alert alert-success">Chronicle moved to the archive!</div>';
}
if (isset($_GET['deleted'])) {
    echo '<div class="alert alert-success">Chronicle permanently removed!</div>';
}
if (isset($_GET['restored'])) {
    echo '<div class="alert alert-success">Chronicle restored from the archive!</div>';
}

switch ($ramVar_page) {
    case 'post-new':
    case 'post-edit':
        ramFn_renderPostEditor();
        break;

    case 'categories':
        ramFn_renderTaxonomy('category');
        break;
    
    case 'tags':
        ramFn_renderTaxonomy('post_tag');
        break;
    
    case 'posts':
    default:
        ramFn_renderPostList();
        break;
}

echo '</div>';

/**
 * Render the Post List table
 */
function ramFn_renderPostList(): void {
    try {
        $ramVar_publishCount = \Ram\Database\PatalDB::ramFn_table('posts')->ramFn_where('status', '=', 'publish')->ramFn_count();
        $ramVar_draftCount = \Ram\Database\PatalDB::ramFn_table('posts')->ramFn_where('status', '=', 'draft')->ramFn_count();
        $ramVar_pendingCount = \Ram\Database\PatalDB::ramFn_table('posts')->ramFn_where('status', '=', 'pending')->ramFn_count();
        $ramVar_trashCount = \Ram\Database\PatalDB::ramFn_table('posts')->ramFn_where('status', '=', 'trash')->ramFn_count();
        $ramVar_allCount = $ramVar_publishCount + $ramVar_draftCount + $ramVar_pendingCount;
    } catch (\Throwable) {
        $ramVar_allCount = $ramVar_publishCount = $ramVar_draftCount = $ramVar_pendingCount = $ramVar_trashCount = 0;
    }
    ?>
    <div class="ayodhya-page-header">
        <h1>Sita Chronicles</h1>
        <a href="index.php?page=post-new" class="btn btn-primary"><span>➕</span> Create Chronicle</a>
    </div>

    <div class="ram-card mb-3">
        <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; align-items: center;">
            <a href="index.php?page=posts" style="color: var(--ram-saffron); font-weight: 600; font-size: 0.9rem; text-decoration: none;">All (<?= $ramVar_allCount ?>)</a>
            <span style="color: var(--ram-border);">|</span>
            <a href="index.php?page=posts&status=publish" style="color: var(--ram-muted); font-size: 0.9rem; text-decoration: none;">Published (<?= $ramVar_publishCount ?>)</a>
            <span style="color: var(--ram-border);">|</span>
            <a href="index.php?page=posts&status=pending" style="color: var(--ram-muted); font-size: 0.9rem; text-decoration: none;">Pending (<?= $ramVar_pendingCount ?>)</a>
            <span style="color: var(--ram-border);">|</span>
            <a href="index.php?page=posts&status=draft" style="color: var(--ram-muted); font-size: 0.9rem; text-decoration: none;">Drafts (<?= $ramVar_draftCount ?>)</a>
            <span style="color: var(--ram-border);">|</span>
            <a href="index.php?page=posts&status=trash" style="color: var(--ram-muted); font-size: 0.9rem; text-decoration: none;">Trash (<?= $ramVar_trashCount ?>)</a>
        </div>

        <table class="ayodhya-table">
            <thead>
                <tr>
                    <th style="width: 40px;"><input type="checkbox"></th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $ramVar_status = $_GET['status'] ?? 'publish';
                if ($ramVar_status === 'any') {
                    $ramVar_status = 'publish';
                }
                if ($ramVar_status === 'trash') {
                    $ramVar_posts = ram_get_posts(['limit' => 50, 'status' => 'trash']);
                } else {
                    $ramVar_posts = ram_get_posts(['limit' => 50, 'status' => $ramVar_status]);
                }
                if (empty($ramVar_posts)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 3rem; color: var(--ram-muted);">
                            <div style="font-size: 2rem; margin-bottom: 1rem;">📭</div>
                            No posts found. Start writing your first epic!
                        </td>
                    </tr>
                <?php else: foreach ($ramVar_posts as $ramVar_post): 
                    $ramVar_statusClass = match($ramVar_post['status']) {
                        'publish' => '#4ADE80',
                        'pending' => '#60A5FA',
                        'draft' => '#FBBF24',
                        'trash' => '#F87171',
                        default => '#9CA3AF'
                    };
                    $ramVar_statusBg = match($ramVar_post['status']) {
                        'publish' => 'rgba(34, 197, 94, 0.1)',
                        'pending' => 'rgba(96, 165, 250, 0.1)',
                        'draft' => 'rgba(251, 191, 36, 0.1)',
                        'trash' => 'rgba(248, 113, 113, 0.1)',
                        default => 'rgba(156, 163, 175, 0.1)'
                    };
                ?>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td>
                            <div style="font-weight: 600; color: var(--ram-text);"><?= ram_esc_html($ramVar_post['title']) ?></div>
                            <div style="font-size: 0.75rem; margin-top: 0.25rem;">
                                <?php if ($ramVar_post['status'] !== 'trash'): ?>
                                    <a href="index.php?page=post-edit&id=<?= $ramVar_post['id'] ?>" style="color: var(--ram-accent);">Edit</a>
                                    <span style="color: var(--ram-border);">|</span>
                                    <form method="POST" action="index.php?page=posts" style="display:inline;" onsubmit="return confirm('Move to trash?');">
                                        <?= ram_nonce_field('post_action_' . (int) $ramVar_post['id']) ?>
                                        <input type="hidden" name="ram_action" value="post_action">
                                        <input type="hidden" name="action" value="trash">
                                        <input type="hidden" name="id" value="<?= (int) $ramVar_post['id'] ?>">
                                        <button type="submit" style="border:0;background:none;color:#F87171;cursor:pointer;padding:0;">Trash</button>
                                    </form>
                                    <span style="color: var(--ram-border);">|</span>
                                    <a href="../?p=<?= $ramVar_post['id'] ?>" target="_blank" style="color: var(--ram-muted);">View</a>
                                <?php else: ?>
                                    <form method="POST" action="index.php?page=posts" style="display:inline;">
                                        <?= ram_nonce_field('post_action_' . (int) $ramVar_post['id']) ?>
                                        <input type="hidden" name="ram_action" value="post_action">
                                        <input type="hidden" name="action" value="restore">
                                        <input type="hidden" name="id" value="<?= (int) $ramVar_post['id'] ?>">
                                        <button type="submit" style="border:0;background:none;color:var(--ram-accent);cursor:pointer;padding:0;">Restore</button>
                                    </form>
                                    <span style="color: var(--ram-border);">|</span>
                                    <form method="POST" action="index.php?page=posts" style="display:inline;" onsubmit="return confirm('Delete permanently?');">
                                        <?= ram_nonce_field('post_action_' . (int) $ramVar_post['id']) ?>
                                        <input type="hidden" name="ram_action" value="post_action">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $ramVar_post['id'] ?>">
                                        <button type="submit" style="border:0;background:none;color:#EF4444;cursor:pointer;padding:0;">Delete Permanently</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>Ayodhya Darbar</td>
                        <td><span style="padding: 2px 8px; border-radius: 4px; background: <?= $ramVar_statusBg ?>; color: <?= $ramVar_statusClass ?>; font-size: 0.7rem; text-transform: uppercase; font-weight: 700;"><?= $ramVar_post['status'] ?></span></td>
                        <td style="font-size: 0.8rem; color: var(--ram-muted);"><?= date('Y/m/d', strtotime($ramVar_post['created_at'])) ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * Render the Premium Post Editor
 */
function ramFn_renderPostEditor(): void {
    require_once 'post-editor.php';
}

/**
 * Render Taxonomy Management (Mock)
 */
function ramFn_renderTaxonomy(string $type): void {
    $label = ($type === 'category') ? 'Categories' : 'Tags';
    ?>
    <div class="ayodhya-page-header">
        <h1><?php echo $label; ?></h1>
    </div>

    <div style="display: grid; grid-template-columns: 350px 1fr; gap: 2rem;">
        <div class="taxonomy-add">
            <div class="ram-card">
                <h3 style="font-size: 1rem; margin-bottom: 1.5rem;">Add New <?php echo rtrim($label, 's'); ?></h3>
                <div class="mb-2">
                    <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; color: var(--ram-muted);">Name</label>
                    <input type="text" style="width: 100%; background: var(--ram-deep); border: 1px solid var(--ram-border); border-radius: 6px; padding: 0.6rem; color: var(--ram-text);">
                </div>
                <div class="mb-2">
                    <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; color: var(--ram-muted);">Slug</label>
                    <input type="text" style="width: 100%; background: var(--ram-deep); border: 1px solid var(--ram-border); border-radius: 6px; padding: 0.6rem; color: var(--ram-text);">
                </div>
                <div class="mb-3">
                    <label style="display: block; font-size: 0.85rem; margin-bottom: 0.5rem; color: var(--ram-muted);">Description</label>
                    <textarea style="width: 100%; background: var(--ram-deep); border: 1px solid var(--ram-border); border-radius: 6px; padding: 0.6rem; color: var(--ram-text); height: 100px;"></textarea>
                </div>
                <button class="btn btn-primary" style="width: 100%; justify-content: center;">Add New <?php echo rtrim($label, 's'); ?></button>
            </div>
        </div>

        <div class="taxonomy-list">
            <div class="ram-card">
                <table class="ayodhya-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"><input type="checkbox"></th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Slug</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td><b style="color: var(--ram-saffron);">Uncategorized</b></td>
                            <td style="color: var(--ram-muted);">Default category</td>
                            <td>uncategorized</td>
                            <td>0</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
}
