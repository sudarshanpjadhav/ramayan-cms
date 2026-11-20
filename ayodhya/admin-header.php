<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayodhya Darbar — Ramayan CMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin-ui.css?v=<?= time() ?>">
    <?php ram_head(); ?>
</head>
<body>
    <header class="ayodhya-header">
        <div class="ayodhya-header-left">
            <?php
            $ramVar_breadcrumbNames = [
                'dashboard' => 'Darbar',
                'posts' => 'Sita Chronicles',
                'post-new' => 'Create Chronicle',
                'post-edit' => 'Edit Chronicle',
                'categories' => 'Kaands',
                'tags' => 'Sandhis',
                'pages' => 'Ayodhya Patras',
                'page-new' => 'Create Patra',
                'page-edit' => 'Edit Patra',
                'media' => 'Nandan Treasury',
                'plugins' => 'Vaanar Plugins',
                'themes' => 'Lanka Themes',
                'customizer' => 'Chitrakoot Studio',
                'users' => 'Panchjanya Sabha',
                'settings' => 'Rajya Settings',
            ];
            $ramVar_currentPage = $_GET['page'] ?? 'dashboard';
            $ramVar_breadcrumb = $ramVar_breadcrumbNames[$ramVar_currentPage] ?? ucfirst($ramVar_currentPage);
            ?>
            <span style="color: var(--ram-muted); font-size: 0.8rem;">Ayodhya / <?= ram_esc_html($ramVar_breadcrumb) ?></span>
        </div>
        <div class="ayodhya-header-right" style="display: flex; align-items: center; gap: 1.5rem;">
            <a href="../" target="_blank" class="ayodhya-nav-item" style="padding: 0; font-size: 1.2rem;" title="View Site">🌐</a>
            <a href="logout.php" class="ayodhya-nav-item" style="padding: 0.5rem 0.75rem; color: #F87171 !important;" title="Logout">
                <span>🚪</span> Logout
            </a>
            <div class="ayodhya-user-menu" style="display: flex; align-items: center; gap: 0.75rem;">
                <span style="font-size: 0.85rem; font-weight: 500;"><?= ram_esc_html(ram_current_user()['display_name']) ?></span>
                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--ram-accent); display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700;">
                    <?= substr(ram_current_user()['display_name'], 0, 1) ?>
                </div>
            </div>
        </div>
    </header>
