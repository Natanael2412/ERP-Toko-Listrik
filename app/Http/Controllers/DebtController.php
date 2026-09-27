<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Traits\Auditable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebtController extends Controller
{
    use Auditable;

    /**
     * Daftar Utang Usaha (semua pembelian yang masih belum lunas).
     */
    public function index(Request $request)
    {
        $query = Purchase::with(['product', 'payments']);

        // Filter status
        $status = $request->input('status', 'belum_lunas');
        if ($status !== 'semua') {
            $query->where('status_bayar', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_faktur', 'like', "%{$search}%")
                  ->orWhere('nama_supplier', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sort = $request->input('sort', 'jatuh_tempo');
        switch ($sort) {
            case 'hutang_terbesar':
                $query->orderByDesc('sisa_hutang');
                break;
            case 'hutang_terkecil':
                $query->orderBy('sisa_hutang');
                break;
            case 'total_terbesar':
                $query->orderByDesc('total_beli');
                break;
            case 'terbaru':
                $query->orderByDesc('tanggal_masuk');
                break;
            default: // jatuh_tempo
                $query->orderByRaw("CASE WHEN status_bayar = 'belum_lunas' THEN 0 ELSE 1 END")
                    ->orderBy('tanggal_jatuh_tempo');
                break;
        }

        $debts = $query->paginate(20)->withQueryString();

        // Ringkasan
        $totalUtang = Purchase::where('status_bayar', 'belum_lunas')->sum('sisa_hutang');
        $totalJatuhTempo = Purchase::where('status_bayar', 'belum_lunas')
            ->whereDate('tanggal_jatuh_tempo', '<=', now()->toDateString())
            ->sum('sisa_hutang');
        $jumlahFaktur = Purchase::where('status_bayar', 'belum_lunas')->count();

        // Rekap Hutang per Supplier
        $debtsBySupplier = Purchase::where('status_bayar', 'belum_lunas')
            ->select('nama_supplier', DB::raw('SUM(sisa_hutang) as total_hutang'), DB::raw('COUNT(id) as jumlah_faktur'))
            ->groupBy('nama_supplier')
            ->orderByDesc('total_hutang')
            ->get();

        return view('debts.index', compact('debts', 'totalUtang', 'totalJatuhTempo', 'jumlahFaktur', 'status', 'debtsBySupplier'));
    }

    /**
     * Detail utang per faktur + riwayat pembayaran.
     */
    public function show(Purchase $purchase)
    {
        $purchase->load(['product', 'payments.user', 'user']);
        return view('debts.show', compact('purchase'));
    }

    /**
     * Bayar utang (cicilan atau lunas).
     */
    public function pay(Request $request, Purchase $purchase)
    {
        $request->validate([
            'jumlah_bayar' => "required|numeric|min:1|max:{$purchase->sisa_hutang}",
            'tanggal_bayar' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $jumlahBayar = (float) $request->jumlah_bayar;

            // Catat pembayaran
            PurchasePayment::create([
                'purchase_id' => $purchase->id,
                'user_id' => auth()->id(),
                'jumlah_bayar' => $jumlahBayar,
                'tanggal_bayar' => $request->tanggal_bayar,
                'keterangan' => $request->keterangan,
            ]);

            // Update sisa hutang
            $sisaBaru = $purchase->sisa_hutang - $jumlahBayar;
            $purchase->update([
                'sisa_hutang' => $sisaBaru,
                'status_bayar' => $sisaBaru <= 0 ? 'lunas' : 'belum_lunas',
            ]);

            DB::commit();

            $this->catatAudit('Pembayaran Utang',
                "Faktur: {$purchase->nomor_faktur}, " .
                "Bayar: Rp " . number_format($jumlahBayar, 0, ',', '.') . ", " .
                "Sisa: Rp " . number_format(max($sisaBaru, 0), 0, ',', '.') . ", " .
                "Supplier: {$purchase->nama_supplier}"
            );

            $msg = $sisaBaru <= 0
                ? "Faktur {$purchase->nomor_faktur} telah LUNAS!"
                : "Pembayaran Rp " . number_format($jumlahBayar, 0, ',', '.') . " berhasil dicatat. Sisa: Rp " . number_format($sisaBaru, 0, ',', '.');

            return redirect()->route('debts.show', $purchase)->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Bayar lunas semua hutang supplier tertentu.
     */
    public function paySupplier(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required|string',
            'tanggal_bayar' => 'required|date',
        ]);

        $supplier = $request->input('nama_supplier');
        $tanggal = $request->input('tanggal_bayar');

        $purchases = Purchase::where('nama_supplier', $supplier)
            ->where('status_bayar', 'belum_lunas')
            ->get();

        if ($purchases->isEmpty()) {
            return back()->with('error', 'Tidak ada hutang belum lunas untuk supplier ' . $supplier);
        }

        DB::beginTransaction();

        try {
            $totalBayar = 0;
            foreach ($purchases as $purchase) {
                $jumlahBayar = $purchase->sisa_hutang;
                $totalBayar += $jumlahBayar;

                PurchasePayment::create([
                    'purchase_id' => $purchase->id,
                    'user_id' => auth()->id(),
                    'jumlah_bayar' => $jumlahBayar,
                    'tanggal_bayar' => $tanggal,
                    'keterangan' => 'Pelunasan massal per supplier',
                ]);

                $purchase->update([
                    'sisa_hutang' => 0,
                    'status_bayar' => 'lunas',
                ]);
            }

            DB::commit();

            $this->catatAudit('Pelunasan Utang Supplier', 
                "Supplier: {$supplier}, Total Lunas: Rp " . number_format($totalBayar, 0, ',', '.')
            );

            return back()->with('success', "Semua utang kepada {$supplier} berhasil dilunasi (Total: Rp " . number_format($totalBayar, 0, ',', '.') . ")");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
