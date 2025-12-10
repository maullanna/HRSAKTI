---
name: "Plan: Implementasi Anti-Bot Protection"
overview: ""
todos: []
---

# Plan: Implementasi Anti-Bot Protection

## Analisis Proteksi Saat Ini

### Yang Sudah Ada:

- CSRF Protection: Aktif via VerifyCsrfToken middleware
- Admin Login: Sudah punya throttling built-in (5 attempts/minute via AuthenticatesUsers trait)
- API Rate Limiting: 60 requests/minute
- Session Security: Aktif

### Yang Perlu Ditambahkan:

- Employee Login: Belum ada rate limiting
- Form Protection: Belum ada CAPTCHA/Turnstile
- IP-based blocking: Belum ada

## Opsi Implementasi

### Opsi 1: Rate Limiting Saja (Recommended untuk Internal)

**File yang perlu diubah:**

- `app/Http/Controllers/EmployeeAuthController.php`: Tambahkan rate limiting
- `app/Providers/RouteServiceProvider.php`: Tambahkan rate limiter untuk login

**Implementasi:**

- Rate limiting: 5 attempts per 15 menit per IP
- Lockout: 15 menit setelah 5 failed attempts
- Logging: Log semua failed login attempts

**Pros:**

- Tidak perlu dependency eksternal
- User experience lebih baik (tidak ada CAPTCHA)
- Cukup untuk website internal

**Cons:**

- Kurang efektif untuk public website
- Tidak bisa detect sophisticated bots

### Opsi 2: Rate Limiting + Cloudflare Turnstile

**File yang perlu diubah:**

- Semua file dari Opsi 1
- `resources/views/auth/login.blade.php`: Tambahkan Turnstile widget
- `resources/views/auth/employee_login.blade.php`: Tambahkan Turnstile widget
- `app/Http/Controllers/Auth/LoginController.php`: Validasi Turnstile token
- `app/Http/Controllers/EmployeeAuthController.php`: Validasi Turnstile token
- `.env`: Tambahkan TURNSTILE_SITE_KEY dan TURNSTILE_SECRET_KEY

**Implementasi:**

- Rate limiting: 5 attempts per 15 menit
- Turnstile: Validasi di semua form login
- Fallback: Jika Turnstile gagal, tetap bisa login (untuk development)

**Pros:**

- Gratis dan lebih ringan dari reCAPTCHA
- Better UX (invisible challenge)
- Tidak perlu Google account

**Cons:**

- Perlu Cloudflare account (gratis)
- Perlu setup di Cloudflare dashboard

### Opsi 3: Rate Limiting + Google reCAPTCHA v3

**File yang perlu diubah:**

- Sama seperti Opsi 2, tapi pakai reCAPTCHA

**Pros:**

- Google reCAPTCHA v3 (invisible)
- Tidak perlu user interaction

**Cons:**

- Perlu Google account
- Privacy concerns (tracking)

## Rekomendasi

**Untuk Website Internal (Intranet):**

- Gunakan Opsi 1 (Rate Limiting saja)
- Cukup untuk mencegah brute force
- User experience lebih baik

**Untuk Website Public:**

- Gunakan Opsi 2 (Rate Limiting + Turnstile)
- Lebih aman dari sophisticated bots
- Better protection untuk public access

## Detail Implementasi (Opsi 1 - Rate Limiting)

### 1. Update RouteServiceProvider

**File:** `app/Providers/RouteServiceProvider.php`

- Tambahkan rate limiter 'login' dengan 5 attempts per 15 menit

### 2. Update EmployeeAuthController

**File:** `app/Http/Controllers/EmployeeAuthController.php`

- Tambahkan rate limiting di method login()
- Tambahkan logging untuk failed attempts
- Tambahkan lockout message

### 3. Update LoginController (Optional)

**File:** `app/Http/Controllers/Auth/LoginController.php`

- Pastikan throttling aktif (sudah ada via trait)
- Tambahkan custom maxAttempts jika perlu
- Hapus debug logging yang tidak perlu

### 4. Update Routes

**File:** `routes/web.php`

- Tambahkan throttle middleware ke login routes

### 5. Cleanup Debug Routes

**File:** `routes/web.php`

- Hapus debug routes (/debug-employee, /debug-password, dll) untuk security

## Detail Implementasi (Opsi 2 - Rate Limiting + Turnstile)

### Semua dari Opsi 1, plus:

### 6. Install Cloudflare Turnstile

- Register di Cloudflare (gratis)
- Dapatkan Site Key dan Secret Key
- Tambahkan ke .env

### 7. Update Login Views

**Files:**

- `resources/views/auth/login.blade.php`
- `resources/views/auth/employee_login.blade.php`
- Tambahkan Turnstile script dan widget

### 8. Create Turnstile Validation

**File:** `app/Http/Requests/ValidateTurnstile.php` (new)

- Validasi Turnstile token via API

### 9. Update Controllers

- Validasi Turnstile token sebelum proses login
- Handle Turnstile validation errors

## Testing Checklist

- [ ] Test rate limiting (5 attempts -> lockout)
- [ ] Test lockout duration (15 menit)
- [ ] Test logging failed attempts
- [ ] Test Turnstile validation (jika Opsi 2)
- [ ] Test normal login flow
- [ ] Test dengan multiple IP addresses
- [ ] Verify debug routes sudah dihapus

## Security Notes

- Rate limiting berdasarkan IP address
- Session regeneration setelah login
- Logging semua failed attempts untuk monitoring
- Consider IP whitelist untuk trusted networks (opsional)