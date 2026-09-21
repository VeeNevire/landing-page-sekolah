# Absensi RFID InvestaSchool

## Setup server

1. Jalankan `php artisan migrate` setelah mencadangkan database. Dua migrasi RFID menambah UID, jam masuk, sumber absensi, dan satu baris status pembaca.
2. Buat token acak menggunakan `php -r "echo bin2hex(random_bytes(32));"`. Simpan sebagai `RFID_DEVICE_TOKEN` di `.env` dan salin nilai yang sama ke `deviceToken` pada sketch. Jangan commit token atau password Wi-Fi.
3. Opsional: isi `RFID_RECORDED_BY_USER_ID` dengan ID Admin/Kepala Sekolah aktif yang menjadi pencatat. Jika kosong, sistem memakai Admin/Kepala Sekolah aktif dengan ID terkecil. Jalankan `php artisan config:clear` setelah mengubah konfigurasi.
4. Jalankan Laragon/Apache, arahkan document root ke folder `public` Laravel. Alternatif uji LAN: `php artisan serve --host=0.0.0.0 --port=8000` dan gunakan `http://IP_SERVER:8000/api/rfid/scan`.
5. Temukan IPv4 adaptor Wi-Fi server melalui `ipconfig`. Hubungkan ESP32 ke Wi-Fi yang sama. Izinkan port server pada Windows Firewall untuk jaringan privat; jangan mematikan firewall. Hindari jaringan guest dengan isolasi perangkat. Gunakan reservasi DHCP supaya IP server stabil.

Endpoint hanya menerima POST JSON `{"uid":"12:AB:34:CD"}` dengan header `X-RFID-Token`. GET di browser akan memberi 405; ini bukan pengujian scan. HTTP 401 berarti token tidak cocok, 404 kartu tak dikenal, 422 UID tidak valid, dan 503 petugas belum tersedia. Token melalui HTTP lokal tidak terenkripsi; untuk penggunaan di luar LAN gunakan HTTPS dengan validasi sertifikat pada perangkat.

## ESP32

Buka `esp32-rfid-attendance.ino` di Arduino IDE, install dukungan board ESP32 dan library MFRC522. Pilih board sesuai perangkat (misalnya DOIT ESP32 DEVKIT V1), port yang benar, lalu isi Wi-Fi, IP/port endpoint, dan token sebelum upload. Serial Monitor menggunakan 115200 baud.

| RC522 | ESP32 GPIO |
|---|---|
| SDA / SS | 5 |
| SCK | 18 |
| MISO | 19 |
| MOSI | 23 |
| RST | 4 |
| 3.3V | 3V3 |
| GND | GND |
| IRQ | Tidak dipakai |

## Penggunaan

Admin/Kepala Sekolah membuka **Absensi RFID → Registrasi Kartu**, mencari siswa aktif, lalu menekan **Mulai Scan**. Tap kartu, periksa nama dan UID, kemudian konfirmasi penyimpanan. Kartu pertama dikunci selama sesi; untuk mengganti hasil scan, batalkan lalu mulai lagi. Sesi berlaku dua menit, hanya pemilik sesi dapat mengonfirmasi atau membatalkan. Jika tab ditutup, sesi berakhir otomatis dan pembaca kembali ke mode absensi.

Satu siswa memiliki satu kartu. UID milik siswa lain ditolak; cari pemilik kartu dan gunakan **Lepaskan** sebelum pemindahan. Pelepasan/penggantian membutuhkan konfirmasi dan dicatat di audit log.

Di luar sesi registrasi, scan mencatat Hadir sekali per tanggal server (Asia/Jakarta). Data manual yang sudah ada tetap dipertahankan. Tab **Absensi Hari Ini** memuat seluruh sumber absensi siswa aktif, filter nama/NIS/kelas, rekap, dan pembaruan otomatis. Belum tercatat tidak berarti alpa. Jam kosong berarti tidak ada waktu scan RFID pada catatan tersebut.

ESP32 dapat memakai adaptor USB, tetapi server dan Wi-Fi harus tetap hidup. Tidak ada antrean offline: jika pengiriman gagal, tap ulang setelah koneksi pulih. Cooldown kartu sama adalah tiga detik. Setelah registrasi selesai, angkat kartu dan tap ulang untuk mencatat kehadiran.

## Verifikasi perangkat

Daftarkan kartu percobaan, konfirmasi, lalu tap kembali dan cek satu baris Hadir. Tap ulang untuk memastikan tidak terduplikasi. Coba kartu tak dikenal, token salah, registrasi dengan dua browser Admin, batal/kedaluwarsa, serta kartu yang sudah terikat. Pengujian otomatis server: `php artisan test --filter=RfidAttendanceTest` memakai database pengujian terpisah.
