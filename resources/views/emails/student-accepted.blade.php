<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0;padding:0;background:#f4f7fb;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7fb;padding:40px 20px">
    <tr>
      <td align="center">
        <table width="560" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.06)">
          <tr>
            <td style="padding:32px 40px 0;text-align:center">
              <img src="https://cdn-icons-png.flaticon.com/512/2991/2991234.png" alt="Logo" width="48" height="48" style="margin-bottom:8px">
              <h1 style="font-size:1.25rem;color:#1e293b;margin:0">InvestaSchool</h1>
              <p style="font-size:.85rem;color:#64748b;margin:4px 0 0">Penerimaan Siswa Baru</p>
            </td>
          </tr>
          <tr>
            <td style="padding:24px 40px">
              <div style="text-align:center;margin-bottom:24px">
                <div style="width:72px;height:72px;border-radius:50%;background:#d1fae5;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">
                  <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                </div>
                <h2 style="font-size:1.3rem;color:#059669;margin:0">Selamat!</h2>
              </div>

              <h3 style="font-size:1.05rem;color:#1e293b;margin:0 0 12px;text-align:center">Anda Resmi Diterima sebagai Siswa</h3>

              <p style="color:#475569;line-height:1.7;margin:0 0 16px;text-align:center">
                Yth. <strong>{{ $studentName }}</strong>,<br>
                Selamat! Anda telah resmi diterima sebagai siswa baru di <strong>InvestaSchool</strong>.
              </p>

              <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:12px;padding:20px;margin-bottom:20px">
                <tr>
                  <td style="font-size:.85rem;color:#64748b;padding-bottom:4px">Nama Siswa</td>
                </tr>
                <tr>
                  <td style="font-size:.95rem;font-weight:600;color:#1e293b;padding-bottom:14px">{{ $studentName }}</td>
                </tr>
                <tr>
                  <td style="font-size:.85rem;color:#64748b;padding-bottom:4px">NIS (Nomor Induk Siswa)</td>
                </tr>
                <tr>
                  <td style="font-size:.95rem;font-weight:600;color:#1e293b;padding-bottom:14px;letter-spacing:1px">{{ $nis }}</td>
                </tr>
                @if($className)
                <tr>
                  <td style="font-size:.85rem;color:#64748b;padding-bottom:4px">Kelas</td>
                </tr>
                <tr>
                  <td style="font-size:.95rem;font-weight:600;color:#1e293b;padding-bottom:14px">{{ $className }}</td>
                </tr>
                @endif
                @if($programName)
                <tr>
                  <td style="font-size:.85rem;color:#64748b;padding-bottom:4px">Program Studi</td>
                </tr>
                <tr>
                  <td style="font-size:.95rem;font-weight:600;color:#1e293b;padding-bottom:14px">{{ $programName }}</td>
                </tr>
                @endif
                <tr>
                  <td style="font-size:.85rem;color:#64748b;padding-bottom:4px">Password Akun Portal</td>
                </tr>
                <tr>
                  <td style="font-size:.95rem;font-weight:600;color:#1e293b;padding-bottom:14px;letter-spacing:2px">{{ $password }}</td>
                </tr>
              </table>

              <p style="color:#475569;line-height:1.7;margin:0 0 16px;text-align:center;font-size:.9rem">
                Informasi lebih lanjut mengenai jadwal masuk dan kegiatan awal semester akan dikirimkan melalui email ini dalam waktu dekat.
              </p>

              <a href="{{ url('/login') }}"
                 style="display:block;text-align:center;padding:14px;background:#2563eb;color:#fff;border-radius:10px;font-size:.95rem;font-weight:600;text-decoration:none;margin-bottom:16px">
                Masuk ke Portal Sekolah
              </a>

              <p style="font-size:.82rem;color:#94a3b8;line-height:1.6;margin:0;text-align:center">
                Harap simpan email ini sebagai bukti penerimaan Anda di InvestaSchool.
              </p>
            </td>
          </tr>
          <tr>
            <td style="padding:20px 40px;text-align:center;border-top:1px solid #f1f5f9">
              <p style="font-size:.78rem;color:#94a3b8;margin:0">&copy; {{ date('Y') }} InvestaSchool</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>



