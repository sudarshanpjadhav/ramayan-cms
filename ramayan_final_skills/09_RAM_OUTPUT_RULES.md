
# ═══════════════════════════════════════════════
# SKILL 9 — FILE STRUCTURE & OUTPUT RULES
# ═══════════════════════════════════════════════

```
WHEN GENERATING CODE — ALWAYS:

1. OUTPUT FORMAT:
   - Give each file separately with its full path as heading
   - Example heading: ### FILE: ram/bootstrap/RamBootstrap.php
   - Give COMPLETE file content — never truncate with "// rest of code here"
   - Never use placeholder comments like "// TODO" or "// implement this"
   - Every function must have a real working implementation

2. FILE COMPLETENESS CHECK before outputting:
   - Does it have the Finggu file header comment? ✓
   - Does it have declare(strict_types=1)? ✓
   - Does it have correct namespace? ✓
   - Are all functions typed? ✓
   - Is all output escaped? ✓
   - Are all DB queries parameterized? ✓
   - Does it follow naming convention? ✓

3. FOLDER STRUCTURE:
   ramayan-cms/
   ├── index.php
   ├── ramayan-config.php
   ├── ramayan-config-sample.php
   ├── global-helpers.php
   ├── .htaccess
   ├── ram/              ← core engine (never edit)
   ├── ayodhya/          ← admin panel
   ├── vaanar/           ← plugins
   ├── lanka/            ← themes
   ├── uploads/          ← user uploads
   ├── cache/            ← cache storage
   ├── logs/             ← system logs
   └── swayamvar/        ← installer

4. WHEN ASKED TO FIX A BUG:
   - Show the exact line(s) changed
   - Explain why the bug existed
   - Explain what the fix does
   - Check if same bug exists elsewhere and fix all instances

5. WHEN ASKED TO ADD A FEATURE:
   - Add the feature hook points (before/after actions)
   - Update the relevant helper functions
   - Add admin UI if feature needs configuration
   - Add to REST API if feature needs external access
```

---
