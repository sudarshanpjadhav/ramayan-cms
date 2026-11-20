
# ═══════════════════════════════════════════════
# SKILL 6 — FRONTEND & ADMIN UI STANDARDS
# ═══════════════════════════════════════════════

```
AYODHYA ADMIN PANEL UI RULES:

1. ZERO JQUERY — Vanilla JS only:
   - Never import jQuery, never use $() syntax
   - Use fetch() for AJAX (not $.ajax)
   - Use querySelector/querySelectorAll (not $())
   - Use classList.add/remove (not .addClass/.removeClass)
   - Use addEventListener (not .on())

2. ADMIN DESIGN SYSTEM (Ayodhya Design Tokens):
   CSS Variables to always use:
   --ayodhya-bg-primary:     #0f1117
   --ayodhya-bg-secondary:   #1a1d27
   --ayodhya-bg-card:        #1e2130
   --ayodhya-accent:         #f97316   (saffron orange — India flag)
   --ayodhya-accent-hover:   #ea6c0a
   --ayodhya-text-primary:   #f1f5f9
   --ayodhya-text-secondary: #94a3b8
   --ayodhya-border:         #2d3148
   --ayodhya-success:        #22c55e
   --ayodhya-warning:        #f59e0b
   --ayodhya-danger:         #ef4444
   --ayodhya-radius:         8px
   --ayodhya-radius-lg:      12px
   --ayodhya-shadow:         0 4px 24px rgba(0,0,0,0.4)

3. ADMIN FONT:
   Primary:  'Plus Jakarta Sans' (Google Fonts)
   Mono:     'JetBrains Mono' (for code blocks)
   Never use: Arial, Helvetica, system-ui as primary

4. COMPONENT CLASSES (always follow BEM):
   .ayodhya-card {}
   .ayodhya-card__header {}
   .ayodhya-card__body {}
   .ayodhya-card__footer {}
   .ayodhya-btn {}
   .ayodhya-btn--primary {}
   .ayodhya-btn--danger {}
   .ayodhya-btn--ghost {}
   .ayodhya-table {}
   .ayodhya-table__row {}
   .ayodhya-input {}
   .ayodhya-badge {}
   .ayodhya-badge--success {}
   .ayodhya-notice {}
   .ayodhya-notice--error {}

5. ADMIN AJAX PATTERN:
   // All admin AJAX goes through /ayodhya/ajax.php
   // Request:
   const ramVar_response = await fetch('/ayodhya/ajax.php', {
       method: 'POST',
       headers: { 'Content-Type': 'application/json' },
       body: JSON.stringify({
           action: 'ram_save_post',
           nonce: ramVar_nonce,
           data: ramVar_postData
       })
   });
   // Response always: { success: true/false, data: {}, message: '' }

6. MOBILE-FIRST ADMIN:
   - Admin must be usable on 375px wide screen
   - Sidebar collapses to bottom nav on mobile
   - Tables become card lists on mobile
   - All touch targets minimum 44x44px

7. FRONTEND THEME CSS:
   Use ram- prefix always:
   .ram-container { max-width: 1200px; margin: 0 auto; padding: 0 1rem; }
   .ram-grid {}
   .ram-post {}
   Never hardcode colors in theme templates — always use CSS variables
```

---