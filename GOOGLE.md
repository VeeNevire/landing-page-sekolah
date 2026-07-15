# Google OAuth Login - Configuration Guide

## 📌 Overview
Sistem PPDB memiliki 2 metode autentikasi:
1. **Manual Registration** (Email + Password) - Currently Active
2. **Google OAuth Login** - Currently Disabled

## 🔧 Current Status
**Google Login: DISABLED** ✅

Login Google sementara dinonaktifkan untuk memungkinkan registrasi manual. Code Google OAuth tetap utuh dan bisa di-enable kembali kapan saja.

---

## 🚀 How to Enable Google Login

### Step 1: Update Environment Variable
Edit file `.env`:
```env
# Change from false to true
GOOGLE_LOGIN_ENABLED=true
```

### Step 2: Uncomment Google Routes
Edit file `routes/web.php` (line 23-24):
```php
Route::prefix('ppdb')->name('ppdb.')->group(function () {
    Route::get('/daftar', [PPDBController::class, 'start'])->name('start');
    
    // Uncomment these 2 lines:
    Route::get('/auth/google', [PPDBController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [PPDBController::class, 'handleGoogleCallback']);
    
    Route::post('/register', [PPDBController::class, 'manualRegisterStore'])->name('manual.register');
    // ...
});
```

### Step 3: Update View (Optional)
Edit file `resources/views/ppdb/start.blade.php`:

Replace the entire form section with Google login button. See backup below for original Google login UI.

### Step 4: Clear Cache
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 🔙 How to Disable Google Login

### Step 1: Update Environment Variable
Edit file `.env`:
```env
GOOGLE_LOGIN_ENABLED=false
```

### Step 2: Comment Google Routes
Edit file `routes/web.php` (line 23-24):
```php
// Route::get('/auth/google', [PPDBController::class, 'redirectToGoogle'])->name('auth.google');
// Route::get('/auth/google/callback', [PPDBController::class, 'handleGoogleCallback']);
```

### Step 3: Clear Cache
```bash
php artisan config:clear
php artisan route:clear
```

---

## 📂 Files Involved

### 1. Environment Configuration
**File:** `.env`
```env
GOOGLE_LOGIN_ENABLED=false
GOOGLE_CLIENT_ID=1057596673930-86naqgv6523blo00jleieiqo7qeu6e5u.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=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
GOOGLE_REDIRECT=http://localhost:8000/ppdb/auth/google/callback
```

### 2. Service Configuration
**File:** `config/services.php` (line 38-43)
```php
'google' => [
    'enabled' => env('GOOGLE_LOGIN_ENABLED', true),
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT'),
],
```

### 3. Routes
**File:** `routes/web.php` (line 21-26)
```php
Route::prefix('ppdb')->name('ppdb.')->group(function () {
    Route::get('/daftar', [PPDBController::class, 'start'])->name('start');
    // Route::get('/auth/google', [PPDBController::class, 'redirectToGoogle'])->name('auth.google');
    // Route::get('/auth/google/callback', [PPDBController::class, 'handleGoogleCallback']);
    Route::post('/register', [PPDBController::class, 'manualRegisterStore'])->name('manual.register');
    // ...
});
```

### 4. Controller Methods
**File:** `app/Http/Controllers/PPDBController.php`

**Google OAuth Methods (lines 26-94):**
- `redirectToGoogle()` - Redirect to Google OAuth
- `handleGoogleCallback()` - Handle Google OAuth callback

**Manual Registration Method (lines 96-122):**
- `manualRegisterStore()` - Handle manual registration (email + password)

### 5. View
**File:** `resources/views/ppdb/start.blade.php`
- Current: Manual registration form
- Original: Google login button (see backup below)

### 6. Database
**Migration:** `database/migrations/2026_07_14_000001_add_google_id_to_users_table.php`
- Added `google_id` column (nullable)
- Added `avatar` column (nullable)

**Table:** `users`
- `google_id` - Google user ID (nullable)
- `avatar` - Google profile picture URL (nullable)

---

## 🔍 How It Works

### Manual Registration Flow (Current)
```
/ppdb/daftar
    ↓
User fills form (name, email, password)
    ↓
POST /ppdb/register → PPDBController@manualRegisterStore
    ↓
Create User (role: applicant, google_id: null)
    ↓
Create Applicant record
    ↓
Auto login
    ↓
Redirect to /ppdb/form?step=1
```

### Google OAuth Flow (When Enabled)
```
/ppdb/daftar
    ↓
Click "Daftar dengan Google"
    ↓
GET /ppdb/auth/google → Redirect to Google
    ↓
User authenticates with Google
    ↓
Google redirects to /ppdb/auth/google/callback
    ↓
PPDBController@handleGoogleCallback
    ↓
Create/Update User (role: applicant, google_id: [google_id], avatar: [avatar_url])
    ↓
Create Applicant record if not exists
    ↓
Auto login
    ↓
Redirect based on applicant status (form/upload/status/payment/success)
```

---

## 🎨 Original Google Login UI (Backup)

**File:** `resources/views/ppdb/start.blade.php` (Original)

```blade
<div style="text-align:center;padding:2rem 0">
  <span class="kicker">Langkah 1 dari 3</span>
  <h2>Login dengan Google</h2>
  <p style="color:var(--muted);margin-top:.5rem">Kami menggunakan Google untuk verifikasi identitas. Data pribadi Anda aman.</p>

  <a href="{{ route('ppdb.auth.google') }}"
     style="display:inline-flex;align-items:center;gap:12px;padding:14px 32px;border:2px solid #e0e0e0;border-radius:40px;background:#fff;color:#444;font-size:1.1rem;font-weight:600;margin-top:2rem;text-decoration:none;transition:all .2s;box-shadow:0 2px 8px rgba(0,0,0,.06)">
    <svg width="22" height="22" viewBox="0 0 48 48"><path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/><path fill="#FF3D00" d="m6.306 14.691 6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/><path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238A11.91 11.91 0 0 1 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/><path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/></svg>
    Daftar dengan Google
  </a>

  <div style="margin-top:2.5rem;padding-top:2rem;border-top:1px solid var(--border);font-size:.9rem;color:var(--muted)">
    <p>Sudah punya akun? <a href="{{ route('login') }}" class="text-link">Masuk ke portal</a></p>
  </div>
</div>

<div class="grid grid-3" style="margin-top:2rem;gap:1rem">
  <div class="card" style="text-align:center;padding:1.5rem">
    <h4 style="margin-bottom:.3rem">1. Login Google</h4>
    <p style="font-size:.85rem;color:var(--muted)">Verifikasi dengan akun Google</p>
  </div>
  <div class="card" style="text-align:center;padding:1.5rem">
    <h4 style="margin-bottom:.3rem">2. Isi Formulir</h4>
    <p style="font-size:.85rem;color:var(--muted)">Data siswa & orang tua</p>
  </div>
  <div class="card" style="text-align:center;padding:1.5rem">
    <h4 style="margin-bottom:.3rem">3. Konfirmasi</h4>
    <p style="font-size:.85rem;color:var(--muted)">Pantau status pendaftaran</p>
  </div>
</div>
```

---

## 🧪 Testing

### Test Manual Registration
1. Visit `http://localhost:8000/ppdb/daftar`
2. Fill form with test data:
   - Nama Lengkap: John Doe
   - Email: john@example.com
   - Password: password123
   - Konfirmasi Password: password123
3. Click "Daftar Sekarang"
4. Should redirect to `/ppdb/form?step=1`
5. Check database:
   ```sql
   SELECT * FROM users WHERE email = 'john@example.com';
   -- Should have: google_id = NULL, role = 'applicant'
   
   SELECT * FROM applicants WHERE user_id = [user_id];
   -- Should exist with full_name = 'John Doe'
   ```

### Test Google OAuth (When Enabled)
1. Visit `http://localhost:8000/ppdb/daftar`
2. Click "Daftar dengan Google"
3. Authenticate with Google account
4. Should redirect back and create/update user
5. Check database:
   ```sql
   SELECT * FROM users WHERE google_id IS NOT NULL;
   -- Should have: google_id, avatar, role = 'applicant'
   ```

---

## ⚠️ Important Notes

1. **User yang sudah register via Google** tidak bisa login saat Google login disabled
2. **Manual registration** creates user with `google_id = NULL`
3. **Google OAuth code** tetap utuh di controller (lines 26-94)
4. **Validation messages** dalam Bahasa Indonesia
5. **Password minimal 8 karakter** untuk manual registration
6. **Email harus unique** di database
7. **Role otomatis set ke `applicant`** untuk kedua metode

---

## 📦 Dependencies

- **Laravel Socialite** - `composer require laravel/socialite`
- **Google OAuth** - Client ID & Client Secret dari Google Cloud Console

---

## 🔗 Related Files

- `app/Http/Controllers/PPDBController.php` - Main controller
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php` - Google token revocation on logout
- `app/Http/Controllers/ProfileController.php` - Google token revocation on account deletion
- `app/Models/User.php` - User model (fillable: google_id, avatar)
- `app/Models/Applicant.php` - Applicant model
- `routes/web.php` - Routes configuration
- `config/services.php` - Google service configuration
- `.env` - Environment variables

---

## 📝 Change Log

### 2026-07-15
- **Disabled Google Login** via `GOOGLE_LOGIN_ENABLED=false`
- **Added Manual Registration** method in PPDBController
- **Updated View** to show registration form instead of Google button
- **Commented Google Routes** in web.php
- **Created GOOGLE.md** documentation

### 2026-07-14
- **Added Google OAuth** integration
- **Migration** for google_id and avatar columns
- **Google login button** in PPDB start page

---

## 🆘 Troubleshooting

### Problem: Routes not found after enabling/disabling
**Solution:**
```bash
php artisan route:clear
php artisan config:clear
```

### Problem: Google login still showing when disabled
**Solution:**
```bash
php artisan view:clear
# Or delete: storage/framework/views/*.php
```

### Problem: Google OAuth error "redirect_uri_mismatch"
**Solution:**
- Check `.env` GOOGLE_REDIRECT matches Google Cloud Console
- Should be: `http://localhost:8000/ppdb/auth/google/callback`

### Problem: Manual registration not creating applicant
**Solution:**
- Check `manualRegisterStore()` method in PPDBController
- Verify `Applicant::create()` is being called

---

## 👤 Contact

For questions about this implementation, contact the development team.

**Last Updated:** 2026-07-15 by AI Assistant (Kiro/opencode)
