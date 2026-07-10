# Rencana Implementasi Produksi Portal Orang Tua

## Modul yang sudah tersedia pada prototipe

-   Login orang tua berbasis sesi PHP.
-   Pemilihan anak untuk satu akun orang tua.
-   Dashboard rata-rata nilai, kehadiran, ketuntasan tugas, dan karakter.
-   Tren nilai antarsemester.
-   Rincian nilai kuis, PR/tugas, proyek/praktik, UTS, dan UAS.
-   Perhitungan otomatis nilai akhir berdasarkan bobot.
-   Status ketuntasan terhadap KKM.
-   Catatan guru, karakter, kegiatan ekstrakurikuler, dan rekomendasi tindak lanjut.
-   Cetak/simpan laporan melalui browser serta ekspor CSV.

## Modul produksi yang perlu dikembangkan

1. Portal guru untuk membuat penilaian, memasukkan nilai, dan mempublikasikan hasil.
2. Portal wali kelas untuk validasi rapor, catatan perkembangan, dan rekap kehadiran.
3. Portal administrator untuk data siswa, akun orang tua, kelas, mata pelajaran, bobot, dan periode akademik.
4. Notifikasi email/WhatsApp ketika nilai atau catatan baru dipublikasikan.
5. Integrasi dengan LMS, sistem absensi, dan sistem informasi akademik sekolah.
6. Persetujuan/publikasi nilai agar data draf tidak langsung terlihat oleh orang tua.

## Keamanan minimum

-   Gunakan HTTPS.
-   Simpan kata sandi menggunakan `password_hash()` dan verifikasi dengan `password_verify()`.
-   Terapkan kontrol akses berbasis peran dan relasi orang tuaâ€“siswa.
-   Regenerasi session ID setelah login, timeout sesi, CSRF token, dan rate limiting.
-   Catat audit log untuk login, perubahan nilai, publikasi, ekspor, serta akses data.
-   Jangan menampilkan data siswa lain hanya dengan mengganti parameter URL.
-   Enkripsi backup dan batasi akses database.
-   Terapkan kebijakan retensi data dan persetujuan pemrosesan data pribadi.

## Struktur integrasi

Frontend portal â†’ PHP/API â†’ MySQL â†’ modul guru/admin â†’ notifikasi.

File `database_schema.
