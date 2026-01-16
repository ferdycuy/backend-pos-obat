<?php

namespace App\Exports;

use App\Models\TransaksiDetail;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PenjualanDetailExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        // Menggunakan relasi 'transaksi' yang baru dibuat
        $query = TransaksiDetail::with(['obat', 'transaksi.user']);

        if ($this->request->periode === 'harian') {
            $query->whereHas('transaksi', function($q) {
                // Pastikan kolom di tabel transaksi namanya 'tanggal'
                $q->whereDate('tanggal', $this->request->tanggal);
            });
        } elseif ($this->request->periode === 'mingguan') {
            $query->whereHas('transaksi', function($q) {
                $q->whereBetween('tanggal', [$this->request->start, $this->request->end]);
            });
        } elseif ($this->request->periode === 'bulanan') {
            $query->whereHas('transaksi', function($q) {
                $q->whereMonth('tanggal', $this->request->bulan)
                ->whereYear('tanggal', $this->request->tahun);
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Kode Transaksi',
            'Nama Obat',
            'Qty',
            'Harga Satuan',
            'Subtotal',
            'Kasir'
        ];
    }

    public function map($detail): array
    {
        return [
            $detail->transaksi->tanggal->format('d/m/Y H:i'),
            $detail->transaksi->kode_transaksi,
            $detail->obat->nama,
            $detail->qty,
            $detail->harga,
            $detail->subtotal,
            $detail->transaksi->user->name,
        ];
    }
}