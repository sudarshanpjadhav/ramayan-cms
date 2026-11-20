<?php
/**
 * Plugin Name: Ramayan Dashboard Widgets
 * Slug: ram-dashboard-widgets
 * Version: 1.0.0
 * Author: Finggu
 * Description: Adds premium widgets to the Ayodhya dashboard.
 */

declare(strict_types=1);

if (!defined('RAM_PATH')) exit;

akashvani_add_action('ram_admin_dashboard_bottom', 'ramFn_dashboard_stats_widget');

function ramFn_dashboard_stats_widget(): void
{
    ?>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
        <div class="ram-card">
            <h3 style="font-family: 'Space Grotesk'; margin-bottom: 1rem;">🚀 Performance Score</h3>
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <div style="width: 80px; height: 80px; border-radius: 50%; border: 6px solid #4ADE80; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; color: #4ADE80;">98</div>
                <div>
                    <p style="font-size: 0.9rem; font-weight: 600; color: var(--ram-text);">Excellent! Your site is ultra-fast.</p>
                    <p style="font-size: 0.8rem; color: var(--ram-muted);">Lighthouse Optimization active by Amrit Cache.</p>
                </div>
            </div>
        </div>
        
        <div class="ram-card">
            <h3 style="font-family: 'Space Grotesk'; margin-bottom: 1rem;">🛡️ Security Shield</h3>
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <div style="width: 80px; height: 80px; border-radius: 50%; border: 6px solid var(--ram-saffron); display: flex; align-items: center; justify-content: center; font-size: 2rem;">🛡️</div>
                <div>
                    <p style="font-size: 0.9rem; font-weight: 600; color: var(--ram-text);">Sudarshana Active</p>
                    <p style="font-size: 0.8rem; color: var(--ram-muted);">0 blocked attacks in the last 24h.</p>
                </div>
            </div>
        </div>
    </div>
    <?php
}
