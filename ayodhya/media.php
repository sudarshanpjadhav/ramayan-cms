<?php
/**
 * Ramayan CMS — Media Library View
 * finggu.com | Author: sudarshanpjadhav
 */

declare(strict_types=1);

use Ram\Media\NandanLibrary;

if (!defined('RAM_PATH')) exit;

if (!ram_current_user_can('upload_files')) {
    http_response_code(403);
    exit('Sudarshana Security: You do not have permission to upload media.');
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['media_file'])) {
    if (!ram_verify_nonce((string) ($_POST['_ram_nonce'] ?? ''), 'media_upload')) {
        http_response_code(403);
        exit('Sudarshana Security: Invalid upload request.');
    }

    $ramVar_allowed = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'pdf' => 'application/pdf',
        'zip' => 'application/zip',
    ];

    $ramVar_uploadDir = RAM_PATH . '/uploads/';
    if (!is_dir($ramVar_uploadDir)) {
        mkdir($ramVar_uploadDir, 0755, true);
    }
    
    $ramVar_file = $_FILES['media_file'];
    $ramVar_tmpName = $ramVar_file['tmp_name'];
    $ramVar_name = basename($ramVar_file['name']);
    $ramVar_ext = strtolower(pathinfo($ramVar_name, PATHINFO_EXTENSION));
    if (!isset($ramVar_allowed[$ramVar_ext])) {
        http_response_code(415);
        exit('Unsupported media type.');
    }

    $ramVar_mime = mime_content_type($ramVar_tmpName) ?: '';
    if ($ramVar_mime !== $ramVar_allowed[$ramVar_ext]) {
        http_response_code(415);
        exit('Media content does not match its file type.');
    }

    if (str_starts_with($ramVar_mime, 'image/') && getimagesize($ramVar_tmpName) === false) {
        http_response_code(415);
        exit('Invalid image data.');
    }

    if ((int) ($ramVar_file['size'] ?? 0) > 64 * 1024 * 1024) {
        http_response_code(413);
        exit('Media file exceeds the 64 MB limit.');
    }

    $ramVar_newName = uniqid() . '.' . $ramVar_ext;
    $ramVar_target = $ramVar_uploadDir . $ramVar_newName;
    
    if (move_uploaded_file($ramVar_tmpName, $ramVar_target)) {
        $ramVar_url = '/uploads/' . $ramVar_newName;
        $ramVar_size = filesize($ramVar_target);
        
        // Save to database
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=ramayan_db;charset=utf8mb4", "root", "");
            $stmt = $pdo->prepare("INSERT INTO ram_media (filename, path, url, mime_type, file_size, author_id, uploaded_at) VALUES (?, ?, ?, ?, ?, 1, NOW())");
            $stmt->execute([$ramVar_name, $ramVar_target, $ramVar_url, $ramVar_mime, $ramVar_size]);
        } catch (PDOException $e) {
            error_log("Media upload error: " . $e->getMessage());
        }
    }
}

?>
<div class="ayodhya-page-header">
    <h1>Nandan Treasury</h1>
    <input type="hidden" id="ram-media-nonce" value="<?= ram_esc_attr(ram_create_nonce('media_upload')) ?>">
    <button class="btn btn-primary" onclick="document.getElementById('media-upload').click()"><span>📤</span> Add Treasure</button>
    <input type="file" id="media-upload" style="display: none;" multiple onchange="ramFn_handleUpload(this.files)">
</div>

<div class="ram-card">
    <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; align-items: center;">
        <select class="ram-input" style="width: auto;">
            <option>All Nandan treasures</option>
            <option>Images</option>
            <option>Audio</option>
            <option>Video</option>
            <option>Documents</option>
        </select>
        <select class="ram-input" style="width: auto;">
            <option>All dates</option>
        </select>
        <button class="btn">Filter</button>
    </div>

    <div id="media-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem;">
        <?php
        $ramVar_media = NandanLibrary::ramFn_listMedia();
        if (empty($ramVar_media)): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 5rem; color: var(--ram-muted);">
                <div style="font-size: 3rem; opacity: 0.3; margin-bottom: 1rem;">🖼️</div>
                No treasures found. Add some files above.
            </div>
        <?php else: foreach ($ramVar_media as $ramVar_item): ?>
            <?php
            $ramVar_mediaUrl = ram_media_url((string) ($ramVar_item['url'] ?? ''));

            // Resolve the on-disk file so we never render a broken <img>.
            $ramVar_localPath = (string) ($ramVar_item['path'] ?? '');
            if ($ramVar_localPath === '' || !is_file($ramVar_localPath)) {
                $ramVar_basename  = basename((string) parse_url($ramVar_mediaUrl, PHP_URL_PATH));
                $ramVar_localPath = RAM_UPLOAD_PATH . '/' . $ramVar_basename;
            }
            $ramVar_exists = is_file($ramVar_localPath);
            $ramVar_isImage = $ramVar_exists && str_starts_with((string) ($ramVar_item['mime_type'] ?? ''), 'image/');
            ?>
            <div class="media-item" onclick="ramFn_selectMedia('<?= ram_esc_attr($ramVar_mediaUrl) ?>')" style="aspect-ratio: 1; border-radius: 8px; overflow: hidden; background: var(--ram-deep); border: 1px solid var(--ram-border); cursor: pointer; transition: 0.2s; position: relative;">
                <?php if ($ramVar_isImage): ?>
                    <img src="<?= ram_esc_url($ramVar_mediaUrl) ?>" alt="<?= ram_esc_attr($ramVar_item['filename']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                <?php elseif ($ramVar_exists): ?>
                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; font-size: 2rem;">📄</div>
                <?php else: ?>
                    <div title="File missing on disk" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; gap: .35rem; font-size: 1.75rem; color: #EF4444;">⚠️<span style="font-size:.6rem;color:#94A3B8;">Missing file</span></div>
                <?php endif; ?>
                <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.7); padding: 0.25rem 0.5rem; font-size: 0.7rem; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <?= ram_esc_html($ramVar_item['filename']) ?>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>

<script>
function ramFn_selectMedia(url) {
    alert('Selected: ' + url);
}

async function ramFn_handleUpload(files) {
    if (!files || files.length === 0) return;
    
    const btn = document.querySelector('.btn-primary');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<span>⏳</span> Uploading...';
    
    for (let file of files) {
        const formData = new FormData();
        formData.append('media_file', file);
        formData.append('_ram_nonce', document.getElementById('ram-media-nonce').value);
        
        try {
            const response = await fetch('index.php?page=media', {
                method: 'POST',
                body: formData
            });
            
            const text = await response.text();
            if (response.ok && text.includes('Media Library')) {
                // Also fetch via AJAX to get updated grid without reload
                await ramFn_refreshMediaGrid();
            }
        } catch (e) {
            console.error('Upload error:', e);
        }
    }
    
    btn.innerHTML = originalText;
    ramFn_refreshMediaGrid();
}

async function ramFn_refreshMediaGrid() {
    const grid = document.getElementById('media-grid');
    try {
        const response = await fetch('index.php?ram_api=media');
        const json = await response.json();
        const items = json.data || [];
        
        if (items.length === 0) {
            grid.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 5rem; color: var(--ram-muted);"><div style="font-size: 3rem; opacity: 0.3; margin-bottom: 1rem;">🖼️</div>No media files found. Upload some files above.</div>';
            return;
        }
        
        grid.innerHTML = items.map(item => `
            <div class="media-item" onclick="ramFn_selectMedia('${item.url}')" style="aspect-ratio: 1; border-radius: 8px; overflow: hidden; background: var(--ram-deep); border: 1px solid var(--ram-border); cursor: pointer; transition: 0.2s; position: relative;">
                <img src="${item.url}" style="width: 100%; height: 100%; object-fit: ${item.mime_type?.startsWith('image/') ? 'cover' : 'contain'};">
                <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.7); padding: 0.25rem 0.5rem; font-size: 0.7rem; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    ${item.filename}
                </div>
            </div>
        `).join('');
    } catch (e) {
        console.error('Error loading media:', e);
    }
}
</script>
