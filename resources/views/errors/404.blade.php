<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan - ERP Toko Listrik</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-6">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center border border-gray-100">
        <div class="mb-6 flex justify-center">
            <div class="h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center">
                <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 14h.01M14 10h.01M10 10h.01M14 14h.01"></path>
                </svg>
            </div>
        </div>
        <h1 class="text-6xl font-black text-gray-800 mb-2 tracking-tighter">404</h1>
        <h2 class="text-xl font-bold text-gray-700 mb-4">Halaman Tidak Ditemukan</h2>
        <p class="text-gray-500 mb-8 leading-relaxed">
            Maaf, halaman yang Anda cari tidak ada, sudah dihapus, atau Anda salah memasukkan URL.
        </p>
        <div class="flex flex-col gap-3">
            <button onclick="window.history.back()" class="w-full py-3 px-4 rounded-xl font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors">
                Kembali ke Halaman Sebelumnya
            </button>
            <a href="{{ route('dashboard') }}" class="w-full py-3 px-4 rounded-xl font-semibold text-white bg-primary-600 hover:bg-primary-700 transition-colors shadow-sm shadow-primary-500/30">
                Ke Dashboard Utama
            </a>
        </div>
    </div>
</body>
</html>
