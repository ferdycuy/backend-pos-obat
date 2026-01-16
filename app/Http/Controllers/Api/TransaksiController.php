<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransaksiRequest;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Obat;
use App\Models\StokLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransaksiController extends Controller
{
   public function store(TransaksiRequest $request)
{
    return DB::transaction(function () use ($request) {

        $total = 0;
        $details = [];

        // =============================
        // 1. HITUNG TOTAL & STOK
        // =============================
        foreach ($request->items as $item) {
            $obat = Obat::lockForUpdate()->findOrFail($item['obat_id']);

            if ($obat->stok < $item['qty']) {
                abort(400, "Stok {$obat->nama} tidak mencukupi");
            }

            $subtotal = $obat->harga_jual * $item['qty'];
            $total += $subtotal;

            $obat->decrement('stok', $item['qty']);

            StokLog::create([
                'obat_id' => $obat->id,
                'user_id' => $request->user()->id,
                'tipe' => 'keluar',
                'jumlah' => $item['qty'],
                'keterangan' => 'Penjualan'
            ]);

            $details[] = [
                'obat_id' => $obat->id,
                'qty' => $item['qty'],
                'harga' => $obat->harga_jual,
                'subtotal' => $subtotal,
            ];
        }

        // =============================
        // 2. HITUNG DISKON
        // =============================
        $diskon = 0;

        if ($request->diskon_tipe && $request->diskon_value > 0) {
            if ($request->diskon_tipe === 'persen') {
                $diskon = ($request->diskon_value / 100) * $total;
            } else {
                $diskon = $request->diskon_value;
            }
        }

        $diskon = min($diskon, $total);

        // =============================
        // 3. TOTAL AKHIR
        // =============================
        $totalAkhir = $total - $diskon;

        // =============================
        // 4. VALIDASI BAYAR
        // =============================
        if ($request->bayar < $totalAkhir) {
            abort(400, 'Uang bayar kurang dari total setelah diskon');
        }

        // =============================
        // 5. SIMPAN TRANSAKSI
        // =============================
        $transaksi = Transaksi::create([
            'kode_transaksi' => 'TRX-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5)),
            'user_id' => $request->user()->id,
            'total' => $totalAkhir,
            'diskon' => $diskon,
            'bayar' => $request->bayar,
            'kembali' => $request->bayar - $totalAkhir,
            'metode_pembayaran' => $request->metode_pembayaran ?? 'cash',
            'tanggal' => now(),
        ]);

        // =============================
        // 6. SIMPAN DETAIL
        // =============================
        foreach ($details as $detail) {
            $detail['transaksi_id'] = $transaksi->id;
            TransaksiDetail::create($detail);
        }

        // =============================
        // 7. RESPONSE STRUK
        // =============================
        return response()->json([
            'message' => 'Transaksi berhasil',
            'data' => [
                'tgl' => now()->format('d-m-Y H:i'),
                'subtotal' => $total,
                'diskon' => $diskon,
                'total_harga' => $totalAkhir,
                'bayar' => $transaksi->bayar,
                'kembali' => $transaksi->kembali,
                'metode_pembayaran' => $transaksi->metode_pembayaran,
                'detail_items' => collect($details)->map(function ($d) {
                    return [
                        'nama' => Obat::find($d['obat_id'])->nama,
                        'qty' => $d['qty'],
                        'subtotal' => $d['subtotal'],
                    ];
                })
            ]
        ], 201);
    });
}
}
