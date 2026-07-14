<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pendaftaran - PPDB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Status Pendaftaran</h1>
                    <p class="text-gray-600 mt-1">{{ $applicant->full_name }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-700 font-medium transition">
                        Logout
                    </button>
                </form>
            </div>

            <div class="mb-12">
                <div class="flex items-center justify-between mb-4">
                    @php
                        $steps = [
                            ['key' => 'draft', 'label' => 'Draft'],
                            ['key' => 'submitted', 'label' => 'Terkirim'],
                            ['key' => 'verified', 'label' => 'Terverifikasi'],
                            ['key' => 'paid', 'label' => 'Lunas'],
                        ];
                        
                        $statusIndex = array_search($applicant->status, array_column($steps, 'key'));
                        if ($statusIndex === false) $statusIndex = 0;
                    @endphp

                    @foreach($steps as $index => $step)
                        <div class="flex items-center {{ $index < count($steps) - 1 ? 'flex-1' : '' }}">
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg
                                    {{ $index < $statusIndex ? 'bg-green-500 text-white' : '' }}
                                    {{ $index == $statusIndex ? 'bg-blue-500 text-white' : '' }}
                                    {{ $index > $statusIndex ? 'bg-gray-300 text-gray-600' : '' }}">
                                    @if($index < $statusIndex)
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <polyline stroke-linecap="round" stroke-linejoin="round" points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </div>
                                <span class="text-xs mt-2 font-medium {{ $index <= $statusIndex ? 'text-gray-800' : 'text-gray-500' }}">
                                    {{ $step['label'] }}
                                </span>
                            </div>
                            @if($index < count($steps) - 1)
                                <div class="flex-1 h-1 mx-2 rounded
                                    {{ $index < $statusIndex ? 'bg-green-500' : 'bg-gray-300' }}">
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-2">
                    Status Saat Ini:
                    <span class="
                        {{ $applicant->status === 'draft' ? 'text-gray-600' : '' }}
                        {{ $applicant->status === 'submitted' ? 'text-blue-600' : '' }}
                        {{ $applicant->status === 'verified' ? 'text-indigo-600' : '' }}
                        {{ $applicant->status === 'paid' ? 'text-green-600' : '' }}
                        {{ $applicant->status === 'rejected' ? 'text-red-600' : '' }}
                        {{ $applicant->status === 'paid' ? 'text-purple-600' : '' }}
                    ">
                        {{ ucfirst($applicant->status) }}
                    </span>
                </h2>
                <p class="text-gray-700">
                    @if($applicant->status === 'draft')
                        Pendaftaran Anda masih dalam tahap draft. Silakan lengkapi formulir pendaftaran.
                    @elseif($applicant->status === 'submitted')
                        Data Anda sedang ditinjau oleh tim admin sekolah. Harap tunggu verifikasi.
                    @elseif($applicant->status === 'verified')
                        Data Anda telah diverifikasi. Silakan lakukan pembayaran untuk melanjutkan pendaftaran.
                    @elseif($applicant->status === 'rejected')
                        Mohon maaf, pendaftaran Anda ditolak. 
                        @if($applicant->admin_note)
                            <br><strong>Catatan:</strong> {{ $applicant->admin_note }}
                        @endif
                    @elseif($applicant->status === 'paid')
                        Pembayaran berhasil! Anda telah resmi terdaftar sebagai siswa.
                    @endif
                </p>
            </div>

            @if($applicant->status === 'verified')
                <div class="text-center">
                    <a href="{{ route('ppdb.payment') }}" class="inline-block px-8 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-lg shadow-lg hover:from-green-600 hover:to-green-700 transition">
                        Lanjut ke Pembayaran
                    </a>
                </div>
            @endif

            @if($applicant->status === 'paid')
                <div class="text-center">
                    <a href="{{ route('beranda') }}" class="inline-block px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-lg shadow-lg hover:from-blue-600 hover:to-indigo-700 transition">
                        Kembali ke Beranda
                    </a>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
