<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Adit Kejut') — POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans text-gray-800 antialiased">
    <div class="flex h-screen overflow-hidden">

        {{-- Sidebar --}}
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 transform bg-sidebar text-white transition-transform duration-200 ease-in-out lg:relative lg:translate-x-0 -translate-x-full print:hidden">
            {{-- Logo / Nama Toko --}}
            <div class="flex h-16 items-center gap-3 border-b border-white/10 px-6">
                <img src="{{ asset('images/logo-icon.webp') }}" alt="Adit Kejut Logo" class="h-9 w-9 rounded-lg object-contain bg-white/10 p-1">
                <div>
                    <h1 class="text-sm font-semibold leading-tight">Adit Kejut</h1>
                    <p class="text-xs text-gray-400">Sistem POS</p>
                </div>
            </div>

            {{-- Menu Navigasi --}}
            <nav class="mt-4 space-y-1 px-3">
                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition {{ request()->routeIs('dashboard') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/></svg>
                    Dashboard
                </a>

                {{-- Kasir, Admin, Owner: Transaksi POS --}}
                <a href="{{ route('pos.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition {{ request()->routeIs('pos.index', 'pos.store') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                    Transaksi POS
                </a>

                {{-- Riwayat Transaksi --}}
                <a href="{{ route('pos.history') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition {{ request()->routeIs('pos.history', 'pos.receipt') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Riwayat Transaksi
                </a>



                {{-- Admin & Owner --}}
                @if(auth()->user()->hasRole('admin', 'owner'))
                    <div class="pt-4">
                        <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Manajemen</p>
                    </div>

                    <a href="{{ route('products.index') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition {{ request()->routeIs('products.*') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Produk
                    </a>

                    <a href="{{ route('categories.index') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition {{ request()->routeIs('categories.*') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                        Kategori
                    </a>

                    <a href="{{ route('suppliers.index') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition {{ request()->routeIs('suppliers.*') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Supplier
                    </a>

                    <a href="{{ route('purchases.index') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition {{ request()->routeIs('purchases.*') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Pembelian / Restock
                    </a>

                    <a href="{{ route('returns.index') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition {{ request()->routeIs('returns.*') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                        Retur Penjualan
                    </a>

                    <div class="pt-4">
                        <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Laporan</p>
                    </div>

                    <a href="{{ route('reports.sales') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition {{ request()->routeIs('reports.sales') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Laporan Penjualan
                    </a>

                    <a href="{{ route('reports.stock-card') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition {{ request()->routeIs('reports.stock-card') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Kartu Stok
                    </a>
                @endif

                {{-- Owner Only --}}
                @if(auth()->user()->isOwner())
                    <div class="pt-4">
                        <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Keuangan</p>
                    </div>

                    <a href="{{ route('debts.index') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition {{ request()->routeIs('debts.*') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Utang Usaha
                    </a>

                    <a href="{{ route('reports.financial-statement') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition {{ request()->routeIs('reports.financial-statement') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Laporan Keuangan
                    </a>

                    <div class="pt-4">
                        <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Pengaturan</p>
                    </div>

                    <a href="{{ route('users.index') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition {{ request()->routeIs('users.*') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Kelola User
                    </a>

                    <a href="{{ route('reports.audit-log') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition {{ request()->routeIs('reports.audit-log') ? 'bg-sidebar-active text-white' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        Audit Log
                    </a>
                @endif
            </nav>
        </aside>

        {{-- Main Content --}}
        <div class="flex flex-1 flex-col overflow-hidden">
            {{-- Top Bar --}}
            <header class="flex h-16 items-center justify-between border-b border-gray-200 bg-white px-4 lg:px-6 print:hidden">
                {{-- Tombol Hamburger (Mobile) --}}
                <button id="sidebar-toggle" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                {{-- Page Title --}}
                <h2 class="text-lg font-semibold text-gray-800">@yield('title', 'Dashboard')</h2>

                {{-- User Info + Logout --}}
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-700">{{ auth()->user()->username }}</p>
                        <p class="text-xs capitalize text-gray-400">{{ auth()->user()->role }}</p>
                    </div>
                    <div class="h-9 w-9 rounded-full bg-primary-600 flex items-center justify-center text-white text-sm font-semibold">
                        {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg p-2 text-gray-400 transition hover:bg-red-50 hover:text-red-500" title="Logout">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </header>

            {{-- Konten Halaman --}}
            <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="mb-4 flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        <svg class="h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <svg class="h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    {{-- Sidebar Toggle Script --}}
    <script>
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebar-toggle');

        toggle?.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });

        // Tutup sidebar saat klik di luar (mobile)
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 1024 && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.add('-translate-x-full');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
