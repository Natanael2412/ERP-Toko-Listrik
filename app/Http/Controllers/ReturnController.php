<?php

namespace App\Http\Controllers;

use App\Models\ReturnItem;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Traits\Auditable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    use Auditable;

    public function index(Request $request)
    {
        $query = ReturnItem::with(['transactionDetail.product', 'transactionDetail.transaction', 'user']);

        $sort = $request->input('sort', 'terbaru');
        switch ($sort) {
            case 'terlama':
                $query->orderBy('created_at');
                break;
            case 'terbesar':
                $query->orderByDesc('jumlah_refund');
                break;
            case 'terbaru':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        $returns = $query->paginate(20)->withQueryString();

        return view('returns.index', compact('returns'));
    }

    public function create(Request $request)
    {
        $transaction = null;
        $nomorNota = $request->input('nomor_nota');

        if ($nomorNota) {
            $transaction = Transaction::with('details.product')
                ->where('nomor_nota', $nomorNota)
                ->first();

            if (!$transaction) {
                return back()->with('error', 'Nomor nota tidak ditemukan.');
            }
        }

        return view('returns.create', compact('transaction', 'nomorNota'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'transaction_detail_id' => 'required|exists:transaction_details,id',
            'qty_retur'             => 'required|numeric|min:0.01',
            'alasan'                => 'required|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $detail = TransactionDetail::with('product', 'transaction')
                ->lockForUpdate()
                ->findOrFail($request->transaction_detail_id);

            // Cek qty retur tidak melebihi qty asli (dikurangi retur sebelumnya)
            $sudahDiretur = ReturnItem::where('transaction_detail_id', $detail->id)->sum('qty_retur');
            $sisaBisaRetur = $detail->qty - $sudahDiretur;

            $qtyRetur = (float) $request->qty_retur;

            if ($qtyRetur > $sisaBisaRetur) {
                DB::rollBack();
                return back()->with('error', "Qty retur melebihi batas. Sisa yang bisa diretur: {$sisaBisaRetur}")->withInput();
            }

            $jumlahRefund = $detail->harga_satuan * $qtyRetur;

            // Catat retur
            ReturnItem::create([
                'transaction_detail_id' => $detail->id,
                'user_id'              => auth()->id(),
                'qty_retur'            => $qtyRetur,
                'alasan'               => $request->alasan,
                'jumlah_refund'        => $jumlahRefund,
            ]);

            // Kembalikan stok
            $detail->product->increment('stok', $qtyRetur);

            DB::commit();

            $this->catatAudit('Retur Penjualan',
                "Nota: {$detail->transaction->nomor_nota}, " .
                "Produk: {$detail->product->nama_barang}, " .
                "Qty: {$request->qty_retur}, " .
                "Refund: Rp " . number_format($jumlahRefund, 0, ',', '.')
            );

            return redirect()->route('returns.index')
                ->with('success', 'Retur berhasil dicatat. Stok telah dikembalikan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
}
