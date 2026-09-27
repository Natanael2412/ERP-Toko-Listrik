<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Adit Kejut POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-900">
    <div class="flex min-h-screen">
        
        {{-- BAGIAN KIRI: Image Background (Sembunyi di Mobile, Tampil di Desktop) --}}
        <div class="relative hidden w-1/2 lg:block">
            {{-- Stock Photo Kelistrikan dari Unsplash --}}
            <img src="https://images.unsplash.com/photo-1555664424-778a1e5e1b48?q=80&w=2070&auto=format&fit=crop" 
                 alt="Alat Listrik" 
                 class="absolute inset-0 h-full w-full object-cover">
            
            {{-- Overlay Gradient agar video tidak terlalu mencolok dan teks lebih terbaca --}}
            <div class="absolute inset-0 bg-gradient-to-r from-slate-900/80 to-slate-900/20 mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-blue-900/20"></div>

            {{-- Teks/Quote di atas video (Opsional, memberi kesan premium) --}}
            <div class="absolute bottom-12 left-12 right-12">
                <div class="rounded-2xl border border-white/10 bg-black/30 p-6 backdrop-blur-md">
                    <h2 class="text-2xl font-bold text-white">Sistem Kelistrikan Modern</h2>
                    <p class="mt-2 text-sm text-gray-300">Kelola inventaris, transaksi penjualan, dan rantai pasok dengan cepat, akurat, dan real-time.</p>
                </div>
            </div>
        </div>

        {{-- BAGIAN KANAN: Form Login (Full Width di Mobile, 1/2 di Desktop) --}}
        <div class="flex w-full flex-col justify-center px-6 py-12 lg:w-1/2 lg:px-20 xl:px-32 bg-slate-900 lg:bg-white lg:shadow-[-20px_0_30px_-15px_rgba(0,0,0,0.3)] z-10 relative">
            
            <div class="mx-auto w-full max-w-sm">
                {{-- Logo & Judul --}}
                <div class="mb-10 text-center lg:text-left">
                    <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-2xl bg-white lg:bg-gray-50 shadow-xl border border-gray-100 lg:mx-0">
                        <img src="{{ asset('images/logo-icon.webp') }}" alt="Adit Kejut Logo" class="h-14 w-14 object-contain">
                    </div>
                    <h1 class="text-3xl font-bold tracking-tight text-white lg:text-gray-900">Selamat Datang</h1>
                    <p class="mt-2 text-sm text-gray-400 lg:text-gray-500">Masuk ke sistem kasir Adit Kejut Anda.</p>
                </div>

                {{-- Form Alert --}}
                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-400 lg:text-red-600 lg:bg-red-50 lg:border-red-200">
                        <ul class="list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form Input --}}
                <form method="POST" action="{{ route('login.process') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-300 lg:text-gray-700">Username</label>
                        <div class="mt-1.5 relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input type="text" name="username" id="username" required value="{{ old('username') }}"
                                class="block w-full rounded-xl border-gray-600 bg-white/5 py-3 pl-10 text-white placeholder-gray-500 focus:border-primary-500 focus:ring-primary-500 lg:border-gray-300 lg:bg-white lg:text-gray-900 lg:shadow-sm" 
                                placeholder="Masukkan username">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300 lg:text-gray-700">Password</label>
                        <div class="mt-1.5 relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input type="password" name="password" id="password" required
                                class="block w-full rounded-xl border-gray-600 bg-white/5 py-3 pl-10 text-white placeholder-gray-500 focus:border-primary-500 focus:ring-primary-500 lg:border-gray-300 lg:bg-white lg:text-gray-900 lg:shadow-sm" 
                                placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-gray-600 bg-white/5 text-primary-600 focus:ring-primary-600 lg:border-gray-300 lg:bg-white">
                            <label for="remember" class="ml-2 block text-sm text-gray-400 lg:text-gray-600">Ingat saya</label>
                        </div>
                    </div>

                    <button type="submit" 
                        class="mt-4 flex w-full justify-center rounded-xl bg-primary-600 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-600/30 hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-all active:scale-[0.98]">
                        Masuk ke Sistem
                    </button>
                </form>

                {{-- Footer Info --}}
                <p class="mt-10 text-center text-xs text-gray-500">
                    &copy; {{ date('Y') }} Adit Kejut. All rights reserved.
                </p>
            </div>
            
        </div>
    </div>
</body>
</html>
