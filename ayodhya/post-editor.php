<?php
/**
 * Ramayan CMS — Visual Editor (Ayodhya)
 * finggu.com | Author: sudarshanpjadhav
 */

declare(strict_types=1);

if (!defined('RAM_PATH')) exit;

$ramVar_postId = (int) ($_GET['id'] ?? 0);
$ramVar_post = $ramVar_postId ? \Ram\Database\PatalDB::ramFn_table('posts')->ramFn_find($ramVar_postId) : null;

$ramVar_title = $ramVar_post['title'] ?? '';
$ramVar_content = $ramVar_post['content'] ?? '';
$ramVar_status = $ramVar_post['status'] ?? 'draft';
$ramVar_type = $ramVar_post['post_type'] ?? ($_GET['type'] ?? 'post');
$ramVar_isPage = $ramVar_type === 'page';
$ramVar_categories = ram_get_categories();
$ramVar_tags = ram_get_tags();
$ramVar_postCats = $ramVar_postId ? ram_get_post_terms($ramVar_postId, 'category') : [];
$ramVar_postTags = $ramVar_postId ? ram_get_post_terms($ramVar_postId, 'post_tag') : [];
$ramVar_postCatIds = array_column($ramVar_postCats, 'term_id');
$ramVar_postTagIds = array_column($ramVar_postTags, 'term_id');
?>

<style>
:root {
    --editor-bg: #0f0f0f;
    --editor-surface: #1a1a1a;
    --editor-border: #2a2a2a;
    --editor-text: #ffffff;
    --editor-text-muted: #888888;
    --editor-primary: #E11D48;
    --editor-accent: #3b82f6;
}

.ram-visual-editor { background: var(--editor-surface); border-radius: 12px; overflow: hidden; }

.ram-editor-toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem;
    padding: 0.75rem 1rem;
    background: #141414;
    border-bottom: 1px solid var(--editor-border);
    align-items: center;
}

.ram-editor-toolbar button {
    background: transparent;
    border: none;
    color: #888;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.2s;
}

.ram-editor-toolbar button:hover { background: #2a2a2a; color: #fff; }

.ram-view-toggle {
    display: flex;
    background: #1a1a1a;
    border-radius: 6px;
    padding: 0.25rem;
}

.ram-view-toggle button {
    background: transparent;
    color: #888;
    border: none;
    padding: 0.4rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.85rem;
}

.ram-view-toggle button.active { background: var(--editor-primary); color: white; }

.separator { width: 1px; height: 24px; background: #333; margin: 0 0.5rem; }

.ram-editor-title {
    background: transparent;
    border: none;
    color: #fff;
    font-family: 'Space Grotesk', sans-serif;
    font-size: 2.5rem;
    font-weight: 700;
    padding: 1rem;
    width: 100%;
    outline: none;
}

.ram-editor-title::placeholder { color: #444; }

.ram-editor-canvas {
    background: #fff;
    color: #111;
    min-height: 400px;
    padding: 2rem;
    font-family: 'Inter', sans-serif;
    font-size: 1.1rem;
    line-height: 1.8;
    border: none;
    outline: none;
    resize: none;
    width: 100%;
}

.ram-editor-canvas:focus { outline: none; }

.ram-html-editor {
    display: none;
    background: #0a0a0a;
    color: #0f0;
    min-height: 400px;
    padding: 1rem;
    font-family: 'Fira Code', monospace;
    font-size: 0.9rem;
    border: none;
    outline: none;
    resize: none;
    width: 100%;
    line-height: 1.6;
}

.ram-editor-canvas:empty::before { content: 'Start writing your story...'; color: #999; }
</style>

<form method="POST" id="ram-post-editor-form" action="index.php?page=posts">
    <?= ram_nonce_field('save_post') ?>
    <input type="hidden" name="ram_action" value="save_post">
    <input type="hidden" name="post_id" value="<?= $ramVar_postId ?>">
    <input type="hidden" name="post_type" value="<?= ram_esc_attr($ramVar_type) ?>">

    <!-- Header -->
    <div class="ayodhya-page-header" style="margin-bottom: 1rem;">
        <h1 style="font-size: 1.25rem; font-family: 'Space Grotesk';">
            <?= $ramVar_postId ? 'Edit ' . ($ramVar_isPage ? 'Patra' : 'Sita Chronicle') : 'Create New ' . ($ramVar_isPage ? 'Patra' : 'Sita Chronicle') ?>
        </h1>
        <div style="display: flex; gap: 0.75rem;">
            <button type="submit" name="post_status" value="draft" class="btn">Keep as Manuscript</button>
            <button type="submit" name="post_status" value="pending" class="btn">Send for Sabha Review</button>
            <button type="submit" name="post_status" value="publish" class="btn btn-primary">Publish to Ramrajya</button>
        </div>
    </div>

    <!-- Visual Editor -->
    <div class="ram-visual-editor">
        <!-- Toolbar -->
        <div class="ram-editor-toolbar">
            <div class="ram-view-toggle">
                <button type="button" class="active" id="btn-visual" onclick="ramFn_switchView('visual')">Visual</button>
                <button type="button" id="btn-text" onclick="ramFn_switchView('text')">HTML</button>
            </div>
            
            <span class="separator"></span>
            
            <button type="button" onclick="ramFn_formatText('bold')" title="Bold"><b>B</b></button>
            <button type="button" onclick="ramFn_formatText('italic')" title="Italic"><i>I</i></button>
            <button type="button" onclick="ramFn_formatText('underline')" title="Underline"><u>U</u></button>
            
            <span class="separator"></span>
            
            <button type="button" onclick="ramFn_insertBlock('h2')" style="font-weight:bold;">H2</button>
            <button type="button" onclick="ramFn_insertBlock('h3')" style="font-weight:bold;">H3</button>
            
            <span class="separator"></span>
            
            <button type="button" onclick="ramFn_insertBlock('ul')">• List</button>
            <button type="button" onclick="ramFn_insertBlock('quote')">❝ Quote</button>
            <button type="button" onclick="ramFn_insertBlock('code')">&lt;/&gt;</button>
            
            <span class="separator"></span>
            
            <button type="button" onclick="ramFn_openMediaModal(ramFn_insertImage)">🖼️ Nandan Treasury</button>
            <button type="button" onclick="ramFn_insertLink()">🔗 Link</button>
        </div>

        <!-- Title -->
        <input type="text" name="post_title" class="ram-editor-title" value="<?= ram_esc_attr($ramVar_title) ?>" placeholder="Enter chapter title here...">

        <!-- Visual Editor (WYSIWYG) -->
        <div id="ram-visual-editor" class="ram-editor-canvas" contenteditable="true"><?= $ramVar_content ?: '' ?></div>

        <!-- HTML Editor -->
        <textarea id="ram-html-editor" class="ram-html-editor" name="post_content_html" placeholder="Enter HTML code..."><?= ram_esc_html($ramVar_content) ?></textarea>
    </div>

    <!-- Sidebar -->
    <div style="display: grid; grid-template-columns: 1fr 300px; gap: 1.5rem; margin-top: 1.5rem;">
        <div></div>
        
        <div class="editor-sidebar">
            <div class="ram-card mb-3">
                <h3 style="font-size: 0.9rem; margin-bottom: 1rem;">Ramrajya Publication</h3>
                <div style="font-size: 0.85rem; color: var(--ram-muted); display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span>Status:</span> 
                        <b style="color: var(--ram-saffron); text-transform: capitalize;"><?= $ramVar_status ?></b>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Visibility:</span> 
                        <b style="color: var(--ram-text);">Open to the Realm</b>
                    </div>
                </div>
                <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-sm" style="flex:1;">Seal Update</button>
                </div>
            </div>

            <div class="ram-card mb-3">
                <h3 style="font-size: 0.9rem; margin-bottom: 1rem;">Kaands</h3>
                <div style="max-height: 150px; overflow-y: auto; font-size: 0.85rem; display: flex; flex-direction: column; gap: 0.5rem;">
                    <?php foreach ($ramVar_categories as $ramVar_cat): ?>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="categories[]" value="<?= $ramVar_cat['term_id'] ?>" <?= in_array($ramVar_cat['term_id'], $ramVar_postCatIds) ? 'checked' : '' ?>> <?= ram_esc_html($ramVar_cat['name']) ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="ram-card mb-3">
                <h3 style="font-size: 0.9rem; margin-bottom: 1rem;">Sandhis</h3>
                <div style="margin-bottom: 0.75rem;">
                    <?php foreach ($ramVar_tags as $ramVar_tag): ?>
                    <label style="display: inline-flex; align-items: center; gap: 0.25rem; cursor: pointer; margin-right: 0.75rem; margin-bottom: 0.5rem;">
                        <input type="checkbox" name="tags[]" value="<?= $ramVar_tag['term_id'] ?>" <?= in_array($ramVar_tag['term_id'], $ramVar_postTagIds) ? 'checked' : '' ?>> <?= ram_esc_html($ramVar_tag['name']) ?>
                    </label>
                    <?php endforeach; ?>
                </div>
                <input type="text" name="new_tags" placeholder="Add new tags (comma)" style="width: 100%; background: var(--ram-card); border: 1px solid var(--ram-border); border-radius: 6px; padding: 0.5rem; color: var(--ram-text); font-size: 0.8rem;">
            </div>

            <div class="ram-card">
                <h3 style="font-size: 0.9rem; margin-bottom: 1rem;">Featured Image</h3>
                <div id="ram-featured-image-box" onclick="ramFn_openMediaModal(ramFn_setFeaturedImage)" style="aspect-ratio: 16/9; background: rgba(255,255,255,0.02); border: 2px dashed var(--ram-border); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--ram-muted); font-size: 0.85rem; cursor: pointer; overflow: hidden;">
                    <?php if (!empty($ramVar_post['featured_image'])): ?>
                    <img src="<?= $ramVar_post['featured_image'] ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
                    <?php else: ?>
                    <div style="text-align: center;">
                        <div style="font-size: 2rem;">🖼️</div>
                        Set featured image
                    </div>
                    <?php endif; ?>
                </div>
                <input type="hidden" name="featured_image" id="ram-featured-image-input" value="<?= $ramVar_post['featured_image'] ?? '' ?>">
            </div>
        </div>
    </div>
</form>

<!-- Hidden textarea for form submission -->
<textarea name="post_content" id="ram-editor-textarea" style="display: none;"><?= ram_esc_html($ramVar_content) ?></textarea>

<script>
let currentView = 'visual';

function ramFn_switchView(view) {
    currentView = view;
    document.getElementById('btn-visual').classList.toggle('active', view === 'visual');
    document.getElementById('btn-text').classList.toggle('active', view === 'text');
    
    const visualEditor = document.getElementById('ram-visual-editor');
    const htmlEditor = document.getElementById('ram-html-editor');
    const textarea = document.getElementById('ram-editor-textarea');
    
    if (view === 'text') {
        visualEditor.style.display = 'none';
        htmlEditor.style.display = 'block';
        htmlEditor.value = visualEditor.innerHTML;
    } else {
        visualEditor.style.display = 'block';
        htmlEditor.style.display = 'none';
        if (htmlEditor.value) {
            visualEditor.innerHTML = htmlEditor.value;
        }
        textarea.value = visualEditor.innerHTML;
    }
}

function ramFn_formatText(format) {
    const editor = document.getElementById('ram-visual-editor');
    editor.focus();
    
    switch(format) {
        case 'bold': document.execCommand('bold', false, null); break;
        case 'italic': document.execCommand('italic', false, null); break;
        case 'underline': document.execCommand('underline', false, null); break;
    }
    ramFn_updateContent();
}

function ramFn_insertBlock(type) {
    const editor = document.getElementById('ram-visual-editor');
    editor.focus();
    
    switch(type) {
        case 'h2': document.execCommand('formatBlock', false, '<h2>'); break;
        case 'h3': document.execCommand('formatBlock', false, '<h3>'); break;
        case 'ul': document.execCommand('insertUnorderedList', false, null); break;
        case 'quote': document.execCommand('formatBlock', false, '<blockquote>'); break;
        case 'code': document.execCommand('formatBlock', false, '<pre>'); break;
    }
    ramFn_updateContent();
}

function ramFn_insertLink() {
    const url = prompt('Enter URL:');
    if (url) {
        document.execCommand('createLink', false, url);
        ramFn_updateContent();
    }
}

function ramFn_insertImage(media) {
    const editor = document.getElementById('ram-visual-editor');
    editor.focus();
    const imgHtml = `<img src="${media.url}" alt="${media.alt_text || media.filename}" style="max-width: 100%; border-radius: 8px; margin: 1rem 0;">`;
    document.execCommand('insertHTML', false, imgHtml);
    ramFn_updateContent();
}

function ramFn_setFeaturedImage(media) {
    const box = document.getElementById('ram-featured-image-box');
    const input = document.getElementById('ram-featured-image-input');
    if (box && input) {
        box.innerHTML = `<img src="${media.url}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">`;
        input.value = media.url;
    }
}

function ramFn_updateContent() {
    const visualEditor = document.getElementById('ram-visual-editor');
    const htmlEditor = document.getElementById('ram-html-editor');
    const textarea = document.getElementById('ram-editor-textarea');
    
    if (currentView === 'visual') {
        textarea.value = visualEditor.innerHTML;
    } else {
        textarea.value = htmlEditor.value;
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    const visualEditor = document.getElementById('ram-visual-editor');
    const htmlEditor = document.getElementById('ram-html-editor');
    const textarea = document.getElementById('ram-editor-textarea');
    
    // Sync initial content
    if (!textarea.value && visualEditor.innerHTML) {
        textarea.value = visualEditor.innerHTML;
    }
    if (!htmlEditor.value && visualEditor.innerHTML) {
        htmlEditor.value = visualEditor.innerHTML;
    }
    
    // Update on input
    visualEditor.addEventListener('input', ramFn_updateContent);
    htmlEditor.addEventListener('input', ramFn_updateContent);
});
</script>