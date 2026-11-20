
# ═══════════════════════════════════════════════
# SKILL 10 — INDIA-FIRST FEATURE STANDARDS
# ═══════════════════════════════════════════════

```
INDIA-FIRST DEVELOPMENT RULES:

1. PAYMENTS:
   - Always include Razorpay as primary payment option
   - UPI is a first-class payment method (not an afterthought)
   - Show amounts in ₹ by default
   - Support GST calculation (CGST + SGST for intra-state, IGST for inter-state)
   - Support Indian phone format: +91XXXXXXXXXX or 10-digit

2. LANGUAGE:
   - All translation strings use __r() function
   - Hindi (hi_IN) translation must be included in every official plugin
   - Date format default: DD/MM/YYYY (not MM/DD/YYYY)
   - Time format default: 12-hour with AM/PM
   - Default timezone: Asia/Kolkata (IST)
   - Default currency: INR

3. COMMUNICATION:
   - WhatsApp notification support built into core notification system
   - SMS via Indian providers (MSG91, Textlocal) as built-in adapters
   - Email via SMTP (no sendmail dependency — unreliable on Indian shared hosting)

4. COMPLIANCE:
   - DPDP Act 2023 compliance helpers built in
   - Cookie consent banner in core
   - Data export endpoint for users (right to data portability)
   - Data deletion workflow for users

5. PERFORMANCE FOR INDIA:
   - Optimize for slower connections (compress all JSON responses)
   - Support image lazy loading aggressively
   - CDN-ready (Cloudflare, BunnyCDN work out of box)
   - Keep base page size under 100KB HTML+CSS (before images)

6. HOSTING REALITY:
   - Must work on Hostinger India, BigRock, MilesWeb, Bluehost India
   - Must work on PHP shared hosting where shell_exec is disabled
   - Must work without SSH access
   - Must work with cPanel File Manager as only file access tool
   - Installer must auto-detect server capabilities and adjust
```

---