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
              <h1 style="font-size:1.25rem;color:#1e293b;margin:0">SMK MADYA DEPOK</h1>
              <p style="font-size:.85rem;color:#64748b;margin:4px 0 0">Portal Orang Tua / Wali</p>
            </td>
          </tr>
          <tr>
            <td style="padding:24px 40px">
              <h2 style="font-size:1.1rem;color:#1e293b;margin:0 0 8px">Yth. {{ $parentName }}</h2>
              <p style="color:#475569;line-height:1.7;margin:0 0 16px">
                Akun portal orang tua telah dibuat untuk memantau perkembangan <strong>{{ $studentName }}</strong> di SMK MADYA DEPOK.
              </p>

              <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:12px;padding:20px;margin-bottom:20px">
                <tr>
                  <td style="font-size:.85rem;color:#64748b;padding-bottom:4px">Email</td>
                </tr>
                <tr>
                  <td style="font-size:.95rem;font-weight:600;color:#1e293b;padding-bottom:14px">{{ $parentEmail }}</td>
                </tr>
                <tr>
                  <td style="font-size:.85rem;color:#64748b;padding-bottom:4px">Password</td>
                </tr>
                <tr>
                  <td style="font-size:.95rem;font-weight:600;color:#1e293b;padding-bottom:14px;font-family:monospace">{{ $password }}</td>
                </tr>
              </table>

              <a href="{{ url('/login') }}"
                 style="display:block;text-align:center;padding:14px;background:#2563eb;color:#fff;border-radius:10px;font-size:.95rem;font-weight:600;text-decoration:none;margin-bottom:16px">
                Masuk ke Portal Orang Tua
              </a>

              <p style="font-size:.82rem;color:#94a3b8;line-height:1.6;margin:0">
                * Harap simpan informasi ini dengan aman. Jangan bagikan password kepada siapa pun.
                Tim sekolah tidak akan pernah meminta password Anda.
              </p>
            </td>
          </tr>
          <tr>
            <td style="padding:20px 40px;text-align:center;border-top:1px solid #f1f5f9">
              <p style="font-size:.78rem;color:#94a3b8;margin:0">&copy; {{ date('Y') }} SMK MADYA DEPOK .</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
