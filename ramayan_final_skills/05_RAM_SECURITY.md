
# ═══════════════════════════════════════════════
# SKILL 5 — SECURITY STANDARDS
# ═══════════════════════════════════════════════

```
SUDARSHANA SECURITY — EVERY FEATURE MUST FOLLOW:

1. NONCE VERIFICATION on every form and AJAX:
   // Generate:
   $ramVar_nonce = SudarshanaNonce::ramFn_create('action_name_' . $postId);
   // In form: <input type="hidden" name="_ram_nonce" value="<?= $ramVar_nonce ?>">
   // Verify:
   if (!SudarshanaNonce::ramFn_verify($_POST['_ram_nonce'], 'action_name_' . $postId)) {
       throw new RamSecurityException('Invalid nonce');
   }

2. CAPABILITY CHECKS before every admin action:
   if (!ram_current_user_can('edit_posts')) {
       ram_die('You do not have permission to do this.', 403);
   }

3. INPUT SANITIZATION on all incoming data:
   $ramVar_title   = ram_sanitize_text($_POST['title']);
   $ramVar_content = ram_sanitize_textarea($_POST['content']);
   $ramVar_url     = ram_sanitize_url($_POST['redirect_url']);
   $ramVar_email   = ram_sanitize_email($_POST['email']);
   $ramVar_int     = (int) $_POST['post_id'];

4. FILE UPLOAD VALIDATION (NandanUploader):
   - Check MIME type (not just extension)
   - Check file size limit
   - Rename file to random hash (never use original filename)
   - Store outside webroot or use .htaccess to block PHP execution in uploads/
   - Scan for PHP code in image files

5. HEADERS (auto-set by RamBootstrap):
   X-Content-Type-Options: nosniff
   X-Frame-Options: SAMEORIGIN
   X-XSS-Protection: 1; mode=block
   Referrer-Policy: strict-origin-when-cross-origin
   Content-Security-Policy: [configured per site]

6. RATE LIMITING on sensitive endpoints:
   SudarshanaFirewall::ramFn_rateLimit(
       key:    'login_' . $_SERVER['REMOTE_ADDR'],
       max:    5,
       window: 300  // 5 minutes
   );

7. NO SENSITIVE DATA IN LOGS:
   - Never log passwords, API keys, credit card numbers
   - Mask email addresses in logs: s***@gmail.com
   - Never log full request bodies
```

---