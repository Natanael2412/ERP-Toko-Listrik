@extends('layouts.app')
@section('title', 'Transaksi POS')

@section('content')
<div class="flex flex-col gap-4 lg:flex-row lg:gap-6">
    {{-- KIRI: Daftar Produk --}}
    <div class="flex-1 space-y-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Transaksi Penjualan</h1>
            <p class="text-sm text-gray-500">Cari dan tambahkan barang ke keranjang</p>
        </div>

        {{-- Filter Kategori --}}
        <div class="flex flex-wrap gap-2">
            <button type="button" onclick="filterCategory('all')" class="category-btn rounded-full bg-gray-800 px-4 py-1.5 text-xs font-semibold text-white transition" data-cat="all">Semua</button>
            @foreach($categories as $cat)
                <button type="button" onclick="filterCategory({{ $cat->id }})" class="category-btn rounded-full bg-gray-100 px-4 py-1.5 text-xs font-medium text-gray-600 transition hover:bg-gray-200" data-cat="{{ $cat->id }}">{{ $cat->nama_kategori }}</button>
            @endforeach
        </div>

        {{-- Pencarian --}}
        <div class="relative">
            <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="product-search" placeholder="Cari nama barang atau SKU..."
                class="w-full rounded-xl border border-gray-300 py-3 pl-10 pr-4 text-sm shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
        </div>

        {{-- Grid Produk --}}
        <div id="product-grid" class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
            @foreach($products as $product)
            <button type="button" onclick="addToCart({{ $product->id }}, '{{ addslashes($product->nama_barang) }}', {{ $product->harga_jual }}, {{ $product->stok }}, '{{ $product->sku }}', '{{ $product->satuan ?? 'pcs' }}')"
                class="product-card group rounded-xl border border-gray-200 bg-white p-3 text-left shadow-sm transition hover:border-primary-300 hover:shadow-md active:scale-[0.97]"
                data-name="{{ strtolower($product->nama_barang) }}" data-sku="{{ strtolower($product->sku) }}" data-category="{{ $product->category_id }}">
                <p class="text-xs font-mono text-gray-400">{{ $product->sku }}</p>
                <p class="mt-1 text-sm font-semibold text-gray-800 leading-tight">{{ $product->nama_barang }}</p>
                <div class="mt-2 flex items-center justify-between">
                    <p class="text-sm font-bold text-primary-600">Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</p>
                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs {{ $product->stok <= $product->stok_minimum ? 'bg-red-100 text-red-600' : 'text-gray-500' }}">{{ $product->stok }} {{ $product->satuan ?? 'pcs' }}</span>
                </div>
            </button>
            @endforeach
        </div>

        @if($products->isEmpty())
        <div class="rounded-xl border border-gray-200 bg-white p-12 text-center text-gray-400">
            Tidak ada produk dengan stok tersedia.
        </div>
        @endif
    </div>

    {{-- KANAN: Keranjang --}}
    <div class="w-full lg:w-[400px]">
        <div class="sticky top-4 rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="text-lg font-bold text-gray-800">🛒 Keranjang</h2>
                <p id="cart-count" class="text-xs text-gray-400">0 item</p>
            </div>

            {{-- Daftar Item --}}
            <div id="cart-items" class="max-h-[400px] overflow-y-auto divide-y divide-gray-50 px-5">
                <div id="cart-empty" class="py-12 text-center text-sm text-gray-400">
                    Belum ada barang di keranjang
                </div>
            </div>

            {{-- Ringkasan --}}
            <div class="border-t border-gray-100 px-5 py-4 space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Subtotal</span>
                    <span id="cart-subtotal" class="font-semibold text-gray-800">Rp 0</span>
                </div>
                <div class="flex items-center justify-between border-t border-gray-100 pt-3">
                    <span class="font-semibold text-gray-800">Total</span>
                    <span id="cart-total" class="text-xl font-bold text-primary-600">Rp 0</span>
                </div>

                {{-- Opsi Diskon & Pembayaran --}}
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <div>
                        <label class="mb-1 block text-xs text-gray-500">Diskon</label>
                        <div class="flex rounded-lg border border-gray-300 bg-white focus-within:border-primary-500 focus-within:ring-2 focus-within:ring-primary-500/20">
                            <select id="cart-tipe-diskon" onchange="updateCartTotals()" class="w-16 rounded-l-lg border-r border-gray-300 bg-gray-50 px-1 py-1.5 text-xs text-gray-600 focus:outline-none">
                                <option value="nominal">Rp</option>
                                <option value="persen">%</option>
                            </select>
                            <input type="number" id="cart-diskon" value="0" min="0" class="w-full rounded-r-lg px-2 py-1.5 text-right text-sm focus:outline-none" oninput="updateCartTotals()">
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs text-gray-500">Metode Pembayaran</label>
                        <select id="cart-metode" class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                            <option value="Cash">Cash</option>
                            <option value="Transfer">Transfer</option>
                        </select>
                    </div>
                </div>

                <button type="button" onclick="processCheckout()" id="btn-checkout"
                    class="w-full rounded-xl bg-primary-600 py-3 text-sm font-bold text-white shadow-lg shadow-primary-600/30 transition hover:bg-primary-700 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    Bayar & Simpan Transaksi
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Form tersembunyi untuk submit --}}
<form id="checkout-form" method="POST" action="{{ route('pos.store') }}" class="hidden">
    @csrf
    <input type="hidden" name="items" id="form-items">
    <input type="hidden" name="diskon" id="form-diskon">
    <input type="hidden" name="tipe_diskon" id="form-tipe-diskon">
    <input type="hidden" name="metode_pembayaran" id="form-metode-pembayaran">
</form>
@endsection

@push('scripts')
<script>
    // === State Keranjang ===
    let cart = {};

    function addToCart(id, name, price, stock, sku, satuan) {
        if (cart[id]) {
            if (cart[id].qty >= stock) {
                alert('Stok tidak mencukupi!');
                return;
            }
            cart[id].qty++;
        } else {
            cart[id] = { id, name, price, stock, sku, satuan, qty: 1 };
        }
        renderCart();
    }

    function updateQty(id, newQty) {
        if (newQty <= 0) {
            delete cart[id];
        } else if (newQty > cart[id].stock) {
            alert('Stok tidak mencukupi!');
            return;
        } else {
            cart[id].qty = newQty;
        }
        renderCart();
    }

    function removeItem(id) {
        delete cart[id];
        renderCart();
    }

    function renderCart() {
        const container = document.getElementById('cart-items');
        const items = Object.values(cart);
        const countEl = document.getElementById('cart-count');
        const emptyEl = document.getElementById('cart-empty');
        const btnCheckout = document.getElementById('btn-checkout');

        if (items.length === 0) {
            container.innerHTML = '<div id="cart-empty" class="py-12 text-center text-sm text-gray-400">Belum ada barang di keranjang</div>';
            countEl.textContent = '0 item';
            btnCheckout.disabled = true;
        } else {
            let html = '';
            items.forEach(item => {
                const subtotal = item.price * item.qty;
                html += `
                <div class="flex items-start gap-3 py-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">${item.name}</p>
                        <p class="text-xs text-gray-400">Rp ${formatNumber(item.price)} / ${item.satuan} × ${item.qty}</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <button type="button" onclick="updateQty(${item.id}, ${item.qty - 1})"
                            class="flex h-7 w-7 items-center justify-center rounded-lg border border-gray-300 text-gray-500 hover:bg-gray-100 text-sm">−</button>
                        <input type="number" value="${item.qty}" min="0.01" max="${item.stock}" step="any"
                            onchange="updateQty(${item.id}, parseFloat(this.value) || 1)"
                            class="w-14 rounded-lg border border-gray-300 px-1 py-1 text-center text-sm focus:border-primary-500 focus:outline-none">
                        <button type="button" onclick="updateQty(${item.id}, ${item.qty + 1})"
                            class="flex h-7 w-7 items-center justify-center rounded-lg border border-gray-300 text-gray-500 hover:bg-gray-100 text-sm">+</button>
                    </div>
                    <div class="text-right w-24 shrink-0">
                        <p class="text-sm font-semibold text-gray-800">Rp ${formatNumber(subtotal)}</p>
                        <button type="button" onclick="removeItem(${item.id})" class="text-xs text-red-400 hover:text-red-600">Hapus</button>
                    </div>
                </div>`;
            });
            container.innerHTML = html;
            countEl.textContent = items.length + ' item';
            btnCheckout.disabled = false;
        }

        updateCartTotals();
    }

    function updateCartTotals() {
        const items = Object.values(cart);
        const subtotal = items.reduce((sum, item) => sum + (item.price * item.qty), 0);
        
        let diskon = parseFloat(document.getElementById('cart-diskon').value) || 0;
        const tipeDiskon = document.getElementById('cart-tipe-diskon').value;
        
        let diskonNominal = diskon;
        if (tipeDiskon === 'persen') {
            if (diskon > 100) diskon = 100; // max 100%
            diskonNominal = subtotal * diskon / 100;
        }

        const total = Math.max(subtotal - diskonNominal, 0);

        document.getElementById('cart-subtotal').textContent = 'Rp ' + formatNumber(subtotal);
        document.getElementById('cart-total').textContent = 'Rp ' + formatNumber(total);
    }

    function processCheckout() {
        const items = Object.values(cart);
        if (items.length === 0) return;

        if (!confirm('Proses transaksi ini?')) return;

        const formItems = items.map(item => ({
            product_id: item.id,
            qty: item.qty,
        }));

        document.getElementById('form-items').value = JSON.stringify(formItems);
        document.getElementById('form-diskon').value = document.getElementById('cart-diskon').value || 0;
        document.getElementById('form-tipe-diskon').value = document.getElementById('cart-tipe-diskon').value;
        document.getElementById('form-metode-pembayaran').value = document.getElementById('cart-metode').value;

        // Ubah items menjadi array format Laravel
        const form = document.getElementById('checkout-form');
        // Hapus hidden inputs lama
        form.querySelectorAll('.dynamic-input').forEach(el => el.remove());

        formItems.forEach((item, i) => {
            const inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = `items[${i}][product_id]`;
            inputId.value = item.product_id;
            inputId.className = 'dynamic-input';
            form.appendChild(inputId);

            const inputQty = document.createElement('input');
            inputQty.type = 'hidden';
            inputQty.name = `items[${i}][qty]`;
            inputQty.value = item.qty;
            inputQty.className = 'dynamic-input';
            form.appendChild(inputQty);
        });

        form.submit();
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    // === Filter Kategori ===
    let currentCategory = 'all';
    function filterCategory(catId) {
        currentCategory = catId;
        
        // Update styling tombol
        document.querySelectorAll('.category-btn').forEach(btn => {
            if (btn.dataset.cat == catId) {
                btn.classList.remove('bg-gray-100', 'text-gray-600');
                btn.classList.add('bg-gray-800', 'text-white');
            } else {
                btn.classList.remove('bg-gray-800', 'text-white');
                btn.classList.add('bg-gray-100', 'text-gray-600');
            }
        });
        applyFilters();
    }

    // === Pencarian Produk ===
    let currentQuery = '';
    document.getElementById('product-search').addEventListener('input', function(e) {
        currentQuery = e.target.value.toLowerCase();
        applyFilters();
    });

    function applyFilters() {
        document.querySelectorAll('.product-card').forEach(card => {
            const name = card.dataset.name;
            const sku = card.dataset.sku;
            const cat = card.dataset.category;
            
            const matchQuery = name.includes(currentQuery) || sku.includes(currentQuery);
            const matchCategory = currentCategory === 'all' || cat == currentCategory;
            
            card.style.display = (matchQuery && matchCategory) ? '' : 'none';
        });
    }
</script>
@endpush
