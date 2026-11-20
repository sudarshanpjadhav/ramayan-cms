<?php
$ramVar_page = $_GET['page'] ?? 'dashboard';
$ramVar_isPostsOpen = in_array($ramVar_page, ['posts','post-new','post-edit','categories','tags'], true);
$ramVar_isPagesOpen = in_array($ramVar_page, ['pages','page-new','page-edit'], true);
?>
<aside class="ayodhya-sidebar">
    <div class="ayodhya-logo-box">
        <div class="ayodhya-logo-icon">🏹</div>
        <div class="ayodhya-logo-text">RAMAYAN</div>
    </div>

    <nav class="ayodhya-nav">
        <div class="ayodhya-nav-label">Ayodhya Darbar</div>
        <a href="index.php?page=dashboard" class="ayodhya-nav-item <?= $ramVar_page === 'dashboard' ? 'active' : '' ?>">
            <span>🏠</span> Darbar
        </a>

        <!-- Posts Submenu -->
        <div class="ayodhya-nav-group <?= $ramVar_isPostsOpen ? 'open' : '' ?>" data-submenu="posts">
            <a href="index.php?page=posts" class="ayodhya-nav-item <?= $ramVar_isPostsOpen ? 'active' : '' ?>">
                <span>📝</span> Sita Chronicles
            </a>
            <button type="button" class="ayodhya-submenu-toggle" data-target="posts" aria-label="Toggle Posts submenu">
                <span class="ayodhya-toggle-arrow <?= $ramVar_isPostsOpen ? 'open' : '' ?>">▶</span>
            </button>
            <div class="ayodhya-submenu <?= $ramVar_isPostsOpen ? 'open' : '' ?>" data-submenu-list="posts">
                <a href="index.php?page=posts" class="ayodhya-submenu-item <?= $ramVar_page === 'posts' ? 'active' : '' ?>">All Chronicles</a>
                <a href="index.php?page=post-new" class="ayodhya-submenu-item <?= $ramVar_page === 'post-new' ? 'active' : '' ?>">Create Chronicle</a>
                <a href="index.php?page=categories" class="ayodhya-submenu-item <?= $ramVar_page === 'categories' ? 'active' : '' ?>">Kaands</a>
                <a href="index.php?page=tags" class="ayodhya-submenu-item <?= $ramVar_page === 'tags' ? 'active' : '' ?>">Sandhis</a>
            </div>
        </div>

        <!-- Pages Submenu -->
        <div class="ayodhya-nav-group <?= $ramVar_isPagesOpen ? 'open' : '' ?>" data-submenu="pages">
            <a href="index.php?page=pages" class="ayodhya-nav-item <?= $ramVar_isPagesOpen ? 'active' : '' ?>">
                <span>📄</span> Ayodhya Patras
            </a>
            <button type="button" class="ayodhya-submenu-toggle" data-target="pages" aria-label="Toggle Pages submenu">
                <span class="ayodhya-toggle-arrow <?= $ramVar_isPagesOpen ? 'open' : '' ?>">▶</span>
            </button>
            <div class="ayodhya-submenu <?= $ramVar_isPagesOpen ? 'open' : '' ?>" data-submenu-list="pages">
                <a href="index.php?page=pages" class="ayodhya-submenu-item <?= $ramVar_page === 'pages' ? 'active' : '' ?>">All Patras</a>
                <a href="index.php?page=page-new&type=page" class="ayodhya-submenu-item <?= $ramVar_page === 'page-new' ? 'active' : '' ?>">Create Patra</a>
            </div>
        </div>

        <a href="index.php?page=media" class="ayodhya-nav-item <?= $ramVar_page === 'media' ? 'active' : '' ?>">
            <span>🖼️</span> Nandan Treasury
        </a>

        <div class="ayodhya-nav-label">Vaanar Sena</div>
        <a href="index.php?page=plugins" class="ayodhya-nav-item <?= $ramVar_page === 'plugins' ? 'active' : '' ?>">
            <span>🔌</span> Vaanar Plugins
        </a>
        <a href="index.php?page=themes" class="ayodhya-nav-item <?= $ramVar_page === 'themes' ? 'active' : '' ?>">
            <span>🎨</span> Lanka Themes
        </a>
        <a href="index.php?page=customizer" class="ayodhya-nav-item <?= $ramVar_page === 'customizer' ? 'active' : '' ?>">
            <span>✨</span> Chitrakoot Studio
        </a>

        <div class="ayodhya-nav-label">Rajya</div>
        <a href="index.php?page=users" class="ayodhya-nav-item <?= $ramVar_page === 'users' ? 'active' : '' ?>">
            <span>👥</span> Panchjanya Sabha
        </a>
        <a href="index.php?page=settings" class="ayodhya-nav-item <?= $ramVar_page === 'settings' ? 'active' : '' ?>">
            <span>⚙️</span> Rajya Settings
        </a>

        <div class="ayodhya-nav-footer">
            <a href="logout.php" class="ayodhya-nav-item" style="color: #F87171;">
                <span>🚪</span> Logout
            </a>
        </div>
    </nav>
</aside>

<script>
(function() {
    document.querySelectorAll('.ayodhya-submenu-toggle').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var target = this.getAttribute('data-target');
            var group = document.querySelector('.ayodhya-nav-group[data-submenu="' + target + '"');
            var submenu = document.querySelector('.ayodhya-submenu[data-submenu-list="' + target + '"');
            var arrow = this.querySelector('.ayodhya-toggle-arrow');
            if (group && submenu && arrow) {
                var isOpen = group.classList.contains('open');
                if (isOpen) {
                    group.classList.remove('open');
                    submenu.classList.remove('open');
                    arrow.classList.remove('open');
                } else {
                    group.classList.add('open');
                    submenu.classList.add('open');
                    arrow.classList.add('open');
                }
            }
        });
    });
})();
</script>
