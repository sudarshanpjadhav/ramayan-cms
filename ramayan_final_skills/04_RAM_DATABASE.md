
# ═══════════════════════════════════════════════
# SKILL 4 — DATABASE & QUERY BUILDER RULES
# ═══════════════════════════════════════════════

```
PATAL DATABASE LAYER RULES:

1. QUERY BUILDER STYLE (always use this pattern):
   $ramVar_results = PatalDB::ramFn_table('posts')
       ->ramFn_select(['id', 'title', 'slug', 'status', 'created_at'])
       ->ramFn_where('status', '=', 'publish')
       ->ramFn_where('post_type', '=', 'post')
       ->ramFn_orderBy('created_at', 'DESC')
       ->ramFn_limit(10)
       ->ramFn_offset(0)
       ->ramFn_get();

2. ALWAYS CAST USER INPUT before using in queries:
   ->ramFn_where('id', '=', (int) $userId)
   ->ramFn_where('slug', '=', (string) $slug)

3. SCHEMA MIGRATIONS (PatalSchema):
   - Every DB change must be a migration file
   - Never ALTER TABLE manually on production
   - Migration files named: 001_create_ram_posts.php, 002_create_ram_users.php
   - Migrations are irreversible by default (up only)
   - Track in ram_migrations table

4. DB TABLE DESIGN RULES:
   - Always include: id (BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY)
   - Always include: created_at DATETIME DEFAULT CURRENT_TIMESTAMP
   - Always include: updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE
   - Use BIGINT for IDs (not INT — future-proof)
   - Use VARCHAR(191) for indexed string columns (utf8mb4 index limit)
   - Use LONGTEXT for content fields
   - Use JSON column type for flexible metadata where appropriate
   - Always define indexes for columns used in WHERE clauses

5. NEVER SELECT *:
   - Always specify exact columns needed
   - Reduces memory, speeds up queries

6. CACHE DB QUERIES:
   Pattern for all expensive queries:
   $ramVar_cacheKey = 'posts_list_' . md5(serialize($ramVar_args));
   $ramVar_cached = AmritCache::ramFn_get($ramVar_cacheKey);
   if ($ramVar_cached !== false) return $ramVar_cached;
   $ramVar_results = PatalDB::ramFn_table(...)->...->ramFn_get();
   AmritCache::ramFn_set($ramVar_cacheKey, $ramVar_results, 3600);
   return $ramVar_results;
```

---