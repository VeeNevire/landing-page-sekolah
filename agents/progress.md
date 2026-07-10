# Status Implementasi Portal Sekolah

## ✅ Modul Selesai (Laravel)

### Portal Orang Tua (`/portal/*`)

-   [x] Login orang tua (Breeze auth + role redirect)
-   [x] Pemilihan anak (student switcher)
-   [x] Dashboard: rata-rata, kehadiran, ketuntasan, karakter
-   [x] Tren nilai antarsemester (SVG chart)
-   [x] Rincian nilai per komponen + otomatis nilai akhir
-   [x] Status ketuntasan terhadap KKM
-   [x] Catatan guru, karakter, ekstrakurikuler, rekomendasi
-   [x] Cetak/simpan PDF & ekspor CSV
-   [x] Portal Kehadiran — rekap hadir/sakit/izin/alpha + progress bar
-   [x] Portal Jadwal — jadwal mingguan per hari
-   [x] Portal Tagihan — rincian tagihan + status bayar + KPI total
-   [x] Portal Profil Siswa — data diri, karakter, ekstrakurikuler
-   [x] Portal Pesan & Notifikasi — daftar notifikasi dari sekolah
-   [x] SVG icons (semua emoji sudah diganti)

### Portal Guru (`/guru/*`)

-   [x] Akun demo guru (`guru@demo.sch.id` / `Demo123!`)
-   [x] CheckRole middleware — role-based access (`teacher`, `homeroom`, `admin`, `principal`)
-   [x] Role-based redirect setelah login (`parent` → portal, `teacher` → guru)
-   [x] Dropdown "Portal" di navbar (Orang Tua / Guru) — hover + click
-   [x] Login page contextual (judul + demo akun beda berdasarkan role)
-   [x] Guru layout (sidebar, topbar, footer khusus guru)
-   [x] Guru dashboard — KPI kelas/siswa/mapel, jadwal hari ini, kelas diajar, daftar siswa wali kelas
-   [x] Database seeder — teaching assignments untuk demo guru
-   [x] Kelas Saya — grid card kelas + siswa + subjek + link input nilai
-   [x] Input Nilai — selector kelas/mapel → form penilaian baru + tabel input siswa + riwayat
-   [x] Absensi — pilih kelas/tanggal → radio hadir/sakit/izin/alpha/terlambat per siswa
-   [x] Jadwal Mengajar — grid mingguan (jam × hari) + daftar list
-   [x] Catatan Siswa — form catatan (kategorisasi) + riwayat catatan per siswa
-   [x] Publikasi Nilai — status publikasi per kelas + tombol publikasikan semua
-   [x] Sidebar active indicator — warna biru muda (#4a8ad4)

### Website Publik

-   [x] Dropdown Portal di navbar (hover) + footer
-   [x] Demo data (JSON) — students, grades, schedule, billing, notifications
-   [x] Sidebar + topbar desain portal

## 🚧 Modul Belum Dibangun

-   [x] Guru: Upload materi / lampiran per kelas
-   [ ] Portal admin — CRUD master data
-   [ ] Notifikasi email/WA
-   [ ] Integrasi LMS, absensi, SIAKAD
-   [ ] Approval/publikasi nilai (draft vs final)
-   [ ] Security: audit log, rate limiting

## 📦 Data

-   Database: MySQL, 15 migrations, 11 models
-   Tabel: users, students, subjects, teaching_assignments, assessments, assessment_scores, attendance, teacher_notes, behavior_scores, parent_student, academic_periods, audit_logs
-   Demo JSON: `database/demo-data/`
-   Akun demo orang tua: `orangtua@demo.sch.id` / `Demo123!`
-   Akun demo guru: `guru@demo.sch.id` / `Demo123!`
-   Akun wali kelas: `rina@cakrawala.sch.id` / `Demo123!`
