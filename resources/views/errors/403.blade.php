<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak - ERP Toko Listrik</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-6">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center border border-gray-100">
        <div class="mb-6 flex justify-center">
            <div class="h-24 w-24 bg-red-50 rounded-full flex items-center justify-center">
                <svg class="h-12 w-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
        </div>
        <h1 class="text-5xl font-black text-gray-800 mb-2 tracking-tighter">403</h1>
        <h2 class="text-xl font-bold text-gray-700 mb-4">Akses Dilarang</h2>
        <p class="text-gray-500 mb-8 leading-relaxed">
            Maaf, Anda tidak memiliki izin (hak akses) untuk melihat halaman ini berdasarkan Role akun Anda.
        </p>
        <div class="flex flex-col gap-3">
            <button onclick="window.history.back()" class="w-full py-3 px-4 rounded-xl font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors">
                Kembali
            </button>
            <a href="{{ route('dashboard') }}" class="w-full py-3 px-4 rounded-xl font-semibold text-white bg-primary-600 hover:bg-primary-700 transition-colors shadow-sm shadow-primary-500/30">
                Ke Dashboard Utama
            </a>
        </div>
    </div>
</body>
</html>
