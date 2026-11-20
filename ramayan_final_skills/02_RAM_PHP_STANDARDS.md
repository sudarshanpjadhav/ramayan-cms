
# ═══════════════════════════════════════════════
# SKILL 2 — PHP CODE QUALITY STANDARDS
# ═══════════════════════════════════════════════

```
PHP STANDARDS — ALWAYS FOLLOW:

1. DECLARATION:
   - Always declare(strict_types=1) at top of every PHP file
   - Always use proper namespace: namespace Ram\[Subfolder];
   - Always use named imports: use Ram\Database\PatalDB;

2. PHP VERSION:
   - Target PHP 8.1+ minimum
   - USE these modern features actively:
     * Enums (for post status, hook priority, cache driver)
     * Named arguments in function calls
     * Readonly properties for config/immutable objects
     * Match expressions instead of switch
     * Union types and intersection types
     * Nullsafe operator (?->)
     * First-class callable syntax
     * Fibers where async behavior helps
   - NEVER use deprecated PHP 7 patterns

3. TYPE SAFETY:
   - Every function parameter must have a type hint
   - Every function must have a return type
   - Use void, never, mixed appropriately
   - Prefer specific types over mixed

4. DATABASE SAFETY (NON-NEGOTIABLE):
   - NEVER write raw mysqli_query() or PDO->query() with concatenated strings
   - ALL database access goes through PatalDB query builder or ORM
   - ALL values in queries must be parameterized
   - Example WRONG:  $db->query("SELECT * FROM ram_posts WHERE id = " . $_GET['id'])
   - Example RIGHT:  PatalDB::ramFn_table('posts')->ramFn_where('id','=',$id)->ramFn_first()

5. OUTPUT SECURITY (NON-NEGOTIABLE):
   - NEVER echo user data directly
   - Always escape output:
     * Text content:    ram_esc_html($value)
     * HTML attributes: ram_esc_attr($value)
     * URLs:            ram_esc_url($value)
     * JavaScript:      ram_esc_js($value)
     * Rich HTML:       ram_kses($html, $allowed)

6. ERROR HANDLING:
   - Use typed exceptions (never generic Exception for everything)
   - Create specific exception classes: RamNotFoundException, RamDBException
   - Never suppress errors with @ operator
   - Log errors through Ram's logging system, not error_log() directly

7. CODE STYLE:
   - PSR-12 coding standard
   - 4 spaces indentation (no tabs)
   - Opening braces on same line for classes and methods
   - Meaningful variable names (never $a, $b, $x, $tmp)
   - One class per file
   - Max function length: 30 lines (break into smaller functions if longer)
   - Add PHPDoc to every public method

8. NO SHORTCUTS:
   - Never use var_dump() or print_r() in production code
   - Never use die() — use proper exception throwing
   - Never use global keyword — use dependency injection
   - Never use extract() — dangerous
   - Never use eval() — ever
   - Never use base64_decode() on user input
```

---