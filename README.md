# Website & Portal InvestaSchool

Sistem informasi sekolah berbasis Laravel dengan website publik, portal orang tua, dan portal guru.

## Fitur

### Website Publik

-   Beranda, profil sekolah, program akademik (SMA & SMK), ekstrakurikuler
-   Informasi PPDB dengan formulir minat
-   Halaman kontak & FAQ
-   Login portal terintegrasi di navbar

### Portal Orang Tua (`/portal/*`)

-   Dashboard perkembangan akademik
-   Laporan nilai (PDF & CSV)
-   Rekap kehadiran
-   Jadwal pelajaran
-   Tagihan & pembayaran
-   Profil siswa
-   Pesan & notifikasi

### Portal Guru (`/guru/*`)

-   Dashboard dengan KPI
-   Kelas saya
-   Input & publikasi nilai
-   Absensi harian
-   Jadwal mengajar
-   Catatan siswa
-   Materi & lampiran (URL-based)

### Sistem

-   Autentikasi berbasis role (`parent`, `teacher`, `homeroom`, `admin`, `principal`)
-   Sidebar aktif indicator
-   Dark mode toggle
-   Custom scrollbar sesuai tema
-   Responsive mobile (hamburger menu)
-   Contextual login page (guru/orang tua)

## Tech Stack

-   **Backend:** Laravel 13, PHP 8.3+
-   **Database:** MySQL 8
-   **Frontend:** Blade templates, custom CSS (CSS Variables), vanilla JS
-   **Auth:** Laravel Breeze
-   **Libraries:** Chart.js (grafik dashboard)

## Persyaratan

-   PHP 8.3+
-   Composer
-   MySQL 8.0+
-   Node.js & npm (untuk Vite, opsional)

## Instalasi

```bash
# Clone repository
git clone <url>
cd laravel-sekolah

# Install dependencies
composer install

# Copy .env
cp .env.example .env

# Generate app key
php artisan key:generate

# Konfigurasi database di .env
# DB_DATABASE=sekolah
# DB_USERNAME=root
# DB_PASSWORD=

# Migrasi & seed
php artisan migrate
php artisan db:seed

# Jalankan server
php artisan serve
```

Buka `http://localhost:8000`

## Akun Demo

| Role                  | Email                    | Password   |
| --------------------- | ------------------------ | ---------- |
| Orang Tua             | `orangtua@demo.sch.id`   | `Demo123!` |
| Guru                  | `guru@demo.sch.id`       | `Demo123!` |
| Wali Kelas (XI RPL 1) | `rina@InvestaSchool.sch.id`  | `Demo123!` |
| Wali Kelas (X TKJ 2)  | `dimas@InvestaSchool.sch.id` | `Demo123!` |

## Struktur Folder

```
app/
├── Helpers/PortalHelper.php      # Fungsi helper untuk portal
├── Http/Controllers/
│   ├── Auth/                     # Login & autentikasi
│   ├── Guru/                     # Portal guru (GuruController)
│   └── Portal/                   # Portal orang tua (Dashboard, Report, Student)
├── Http/Middleware/CheckRole.php  # Role-based access control
├── Models/                       # 12 model Eloquent
database/
├── migrations/                   # 16 migration
├── seeders/DatabaseSeeder.php    # Data demo
├── demo-data/                    # JSON data (students, billing, schedule)
resources/
├── views/
│   ├── auth/                     # Login page (contextual)
│   ├── guru/                     # 8 view portal guru
│   ├── layouts/                  # portal.blade, guru.blade, public.blade
│   ├── portal/                   # 5 view portal orang tua
│   └── *.blade.php               # Halaman website publik
public/
├── css/
│   ├── style.css                 # Global styles + website
│   ├── portal.css                # Portal orang tua & guru styles
│   └── guru.css                  # Tambahan styles khusus guru
└── js/
    ├── script.js                 # Dropdown, sidebar toggle
    └── portal.js                 # Dark mode, theme toggle
```

## Database

15 tabel utama: `users`, `students`, `teaching_assignments`, `subjects`, `assessments`, `assessment_scores`, `attendance`, `teacher_notes`, `publications`, `materials`, `billing_items`, dan lainnya.

## License

MIT

