<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran PPDB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-3xl">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Pembayaran PPDB</h1>
                    <p class="text-gray-600 mt-1">Selesaikan pembayaran untuk menyelesaikan pendaftaran</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-700 font-medium transition">
                        Logout
                    </button>
                </form>
            </div>

            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 mb-6">
                <div class="flex items-start">
                    <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center text-white text-2xl font-bold mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <polyline stroke-linecap="round" stroke-linejoin="round" points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-2">Selamat! Anda Diterima</h2>
                        <p class="text-gray-700">Pendaftaran Anda telah diverifikasi dan diterima oleh sekolah. Silakan lakukan pembayaran untuk menyelesaikan proses pendaftaran.</p>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-xl p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Pendaftar</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Nama Lengkap</p>
                        <p class="font-semibold text-gray-800">{{ $applicant->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Asal Sekolah</p>
                        <p class="font-semibold text-gray-800">{{ $applicant->asal_sekolah ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Jenjang</p>
                        <p class="font-semibold text-gray-800">{{ $applicant->jenjang ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Program Diminati</p>
                        <p class="font-semibold text-gray-800">{{ $applicant->program_diminati ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-amber-50 to-yellow-50 border-2 border-amber-200 rounded-xl p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pembayaran</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-700">Bank</span>
                        <span class="font-semibold">BCA</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-700">No. Rekening</span>
                        <span class="font-semibold">1234567890</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-700">Atas Nama</span>
                        <span class="font-semibold">SMK MADYA DEPOK</span>
                    </div>
                    <div class="border-t border-amber-200 my-3"></div>
                    <div class="flex justify-between">
                        <span class="text-lg font-bold text-gray-800">Nominal</span>
                        <span class="text-2xl font-bold text-green-600">Rp 250.000</span>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                <p class="text-sm text-blue-800">
                    <strong>Catatan:</strong> Setelah melakukan transfer, klik tombol "Sudah Bayar" di bawah. Akun siswa dan portal orang tua akan otomatis dibuat dan dikirim ke email masing-masing.
                </p>
            </div>

            <form id="paymentForm" action="{{ route('ppdb.pay') }}" method="POST">
                @csrf
                <div class="text-center">
                    <button type="button" onclick="confirmPayment()" class="w-full px-8 py-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-lg font-semibold rounded-lg shadow-lg hover:from-green-600 hover:to-emerald-700 transition transform hover:scale-105">
                        Sudah Bayar
                    </button>
                </div>
            </form>

            <div class="text-center mt-6">
                <a href="{{ route('ppdb.status') }}" class="text-gray-600 hover:text-gray-800 underline">
                    Kembali ke Status
                </a>
            </div>
        </div>
    </div>

    <script>
        function confirmPayment() {
            Swal.fire({
                title: 'Konfirmasi Pembayaran',
                html: `
                    <div class="text-left">
                        <p class="mb-2">Apakah Anda sudah melakukan pembayaran?</p>
                        <p class="text-sm text-gray-600">Setelah konfirmasi, akun siswa dan portal orang tua akan otomatis dibuat.</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Sudah Bayar',
                cancelButtonText: 'Belum',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Mengirim Konfirmasi...',
                        html: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    setTimeout(() => {
                        document.getElementById('paymentForm').submit();
                    }, 1500);
                }
            });
        }
    </script>
</body>
</html>
