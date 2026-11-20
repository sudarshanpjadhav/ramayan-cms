
# ═══════════════════════════════════════════════
# SKILL 3 — ARCHITECTURE PATTERNS
# ═══════════════════════════════════════════════

```
ARCHITECTURE RULES:

1. HOOK-DRIVEN EVERYTHING:
   - Core features must fire hooks before and after every operation
   - Plugin developers must NEVER need to edit core files
   - Every list, every output, every process must be filterable
   - Pattern:
     akashvani_do_action('ram_before_save_post', $post);
     // ... do the actual saving ...
     akashvani_do_action('ram_after_save_post', $post, $postId);

2. DEPENDENCY INJECTION:
   - Use RamContainer for resolving dependencies
   - Never use new ClassName() inside other classes
   - Constructor injection preferred over setter injection
   - Example:
     class SitaPost {
         public function __construct(
             private readonly PatalDB $db,
             private readonly AmritCache $cache,
         ) {}
     }

3. REPOSITORY PATTERN for models:
   - Models (SitaPost, SitaPage) are data objects — they hold data
   - Repositories handle all DB operations
   - Example: SitaPostRepository::ramFn_findById(int $id): ?SitaPost

4. SINGLE RESPONSIBILITY:
   - Each class does ONE thing
   - PushpakRouter only routes — it does not load plugins, not render views
   - ChitrakootEngine only renders — it does not query DB
   - PatalDB only handles DB — it does not cache

5. INTERFACE-FIRST for extensible systems:
   - Cache system: interface AmritCacheInterface → AmritFileCache, AmritRedisCache
   - Storage: interface RamStorageInterface
   - Email: interface RamMailerInterface
   - This allows plugins to swap implementations

6. IMMUTABLE CONFIG:
   - RamConfig values are readonly after loading
   - No runtime config modification allowed
   - Plugins use ram_get_option() / ram_update_option() for their settings

7. GRACEFUL DEGRADATION:
   - If Redis is not available → fall back to file cache automatically
   - If AI provider fails → fall back to next provider automatically
   - If image processing fails → save original, log error, continue
   - Never crash the entire site for optional features

8. FOLDER = NAMESPACE:
   - ram/database/ = namespace Ram\Database
   - ram/hooks/    = namespace Ram\Hooks
   - ram/router/   = namespace Ram\Router
   - Perfect 1:1 mapping always
```

---