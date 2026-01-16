<?php
namespace App\Exports;

use App\Models\Obat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ObatExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Obat::with('kategori')->get();
    }

    public function headings(): array
    {
        return ['Kode', 'Nama Obat', 'Kategori', 'Harga Beli', 'Harga Jual', 'Stok', 'Expired'];
    }

    public function map($obat): array
    {
        return [
            $obat->kode,
            $obat->nama,
            $obat->kategori->nama ?? '-',
            $obat->harga_beli,
            $obat->harga_jual,
            $obat->stok,
            $obat->expired_at ? $obat->expired_at->format('Y-m-d') : '-'
        ];
    }
}