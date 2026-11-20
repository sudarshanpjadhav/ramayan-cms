
# ═══════════════════════════════════════════════
# SKILL 8 — REST API STANDARDS
# ═══════════════════════════════════════════════

```
BRAHMAAND REST API RULES:

1. ROUTE STRUCTURE:
   /api/v1/{resource}           GET (list), POST (create)
   /api/v1/{resource}/{id}      GET (single), PUT (update), DELETE (delete)
   /api/v1/{resource}/{id}/{action}  special actions

2. RESPONSE FORMAT — always this exact structure:
   Success:
   {
     "success": true,
     "data": { ... },
     "meta": {
       "total": 100,
       "page": 1,
       "per_page": 10,
       "total_pages": 10
     }
   }
   Error:
   {
     "success": false,
     "error": {
       "code": "ram_not_found",
       "message": "Post not found",
       "status": 404
     }
   }

3. HTTP STATUS CODES — use correctly:
   200 — OK (GET success, PUT success)
   201 — Created (POST success)
   204 — No Content (DELETE success)
   400 — Bad Request (validation failed)
   401 — Unauthorized (not logged in)
   403 — Forbidden (logged in but no permission)
   404 — Not Found
   429 — Too Many Requests (rate limited)
   500 — Internal Server Error

4. AUTHENTICATION:
   - JWT tokens for user auth (Authorization: Bearer {token})
   - API keys for server-to-server (X-Ram-API-Key: {key})
   - Public endpoints use permission_callback: '__return_true'
   - Always validate token on protected endpoints

5. VERSIONING:
   - Current version in URL: /api/v1/
   - When breaking changes needed: /api/v2/ (v1 stays working)
   - Deprecation notice in response headers before removal

6. RATE LIMITING on all API endpoints:
   - Public: 60 requests/minute per IP
   - Authenticated: 300 requests/minute per user
   - Return 429 with Retry-After header when exceeded
```

---