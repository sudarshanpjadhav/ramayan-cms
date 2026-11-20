    <footer style="margin-left: var(--sidebar-width); padding: 2rem; border-top: 1px solid var(--ram-border); text-align: center; color: var(--ram-muted); font-size: 0.8rem;">
        <p>Built with ❤️ for Bharat. 🏹 Ramayan CMS &copy; <?= date('Y') ?> finggu.com</p>
    </footer>

    <!-- Media Modal -->
    <div id="ram-media-modal" class="ram-modal-overlay">
        <div class="ram-modal">
            <div class="ram-modal-header">
                <h2 style="font-family: 'Space Grotesk'; font-size: 1.25rem;">Choose from Nandan Treasury</h2>
                <button class="btn btn-sm" onclick="ramFn_closeMediaModal()">✕</button>
            </div>
            <div class="ram-modal-content">
                <div id="ram-media-browser-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 1rem;">
                    <!-- Content loaded via AJAX/Fetch -->
                    <div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: var(--ram-muted);">Opening Nandan Treasury...</div>
                </div>
            </div>
            <div class="ram-modal-footer">
                <button class="btn" onclick="ramFn_closeMediaModal()">Cancel</button>
                <button class="btn btn-primary" id="ram-media-select-btn" disabled>Place in Chronicle</button>
            </div>
        </div>
    </div>

    <script>
    let selectedMedia = null;
    let mediaCallback = null;
    
    function ramFn_openMediaModal(callback) {
        mediaCallback = callback;
        const modal = document.getElementById('ram-media-modal');
        modal.classList.add('active');
        ramFn_loadMediaItems();
    }

    function ramFn_closeMediaModal() {
        document.getElementById('ram-media-modal').classList.remove('active');
        selectedMedia = null;
        mediaCallback = null;
    }

    async function ramFn_loadMediaItems() {
        const grid = document.getElementById('ram-media-browser-grid');
        try {
            const response = await fetch('index.php?ram_api=media');
            const json = await response.json();
            const data = await json.data;
            
            if (!json.success) {
                grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: #F87171;">Nandan Treasury could not be opened. ' + (json.message || '') + '</div>';
                return;
            }
            
            if (!data || data.length === 0) {
                grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: var(--ram-muted);">No treasures found. <a href="index.php?page=media" style="color: var(--ram-accent);">Add to Nandan Treasury</a></div>';
                return;
            }

            grid.innerHTML = '';
            data.forEach((item, idx) => {
                const div = document.createElement('div');
                div.className = 'media-thumb';
                div.style.cssText = 'aspect-ratio: 1; border-radius: 8px; overflow: hidden; border: 2px solid transparent; cursor: pointer; background: var(--ram-deep);';
                div.onclick = () => ramFn_selectMediaItem(div, item);
                
                const img = document.createElement('img');
                img.src = item.url;
                img.style.cssText = 'width: 100%; height: 100%; object-fit: cover;';
                
                div.appendChild(img);
                grid.appendChild(div);
            });
        } catch (e) {
            grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: #F87171;">Failed to load media. Error: ' + e.message + '</div>';
        }
    }

    function ramFn_selectMediaItem(el, item) {
        document.querySelectorAll('.media-thumb').forEach(t => t.style.borderColor = 'transparent');
        el.style.borderColor = 'var(--ram-saffron)';
        selectedMedia = item;
        
        const btn = document.getElementById('ram-media-select-btn');
        btn.disabled = false;
        btn.onclick = () => {
            if (mediaCallback) mediaCallback(selectedMedia);
            ramFn_closeMediaModal();
        };
    }
    
    function ramFn_insertImage(media) {
        const textarea = document.getElementById('ram-editor-textarea');
        if (textarea) {
            const imgHtml = `\n![${media.alt_text || media.filename}](${media.url})\n`;
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;
            textarea.value = text.substring(0, start) + imgHtml + text.substring(end);
        }
    }

    function ramFn_setFeaturedImage(media) {
        const box = document.getElementById('ram-featured-image-box');
        const input = document.getElementById('ram-featured-image-input');
        if (box && input && media) {
            box.innerHTML = `<img src="${media.url}" style="width: 100%; height: 100%; object-fit: cover;">`;
            input.value = media.url;
        }
    }
    </script>

    <?php ram_footer(); ?>
</body>
</html>
