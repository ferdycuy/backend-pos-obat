<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exports\PenjualanDetailExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function harian(Request $request)
    {
        $tanggal = $request->get('tanggal', now()->toDateString());

        // Gunakan paginate dan load user
        $transaksi = Transaksi::with('user', 'detail.obat')
            ->whereDate('tanggal', $tanggal)
            ->latest()
            ->paginate(10);

        // Grafik (Grup per Jam)
        $grafik = Transaksi::selectRaw('HOUR(tanggal) as jam, SUM(total) as total')
            ->whereDate('tanggal', $tanggal)
            ->groupBy('jam')
            ->orderBy('jam', 'asc')
            ->get();

        return response()->json([
            'total_transaksi' => (int) Transaksi::whereDate('tanggal', $tanggal)->count(),
            'total_pendapatan' => (int) Transaksi::whereDate('tanggal', $tanggal)->sum('total'),
            'data' => $transaksi->items(), // Mengambil array data saja
            'meta' => [
                'current_page' => $transaksi->currentPage(),
                'last_page' => $transaksi->lastPage(),
                'total' => $transaksi->total(),
            ],
            'grafik' => $grafik
        ]);
    }

    public function mingguan(Request $request)
    {
        $start = Carbon::parse($request->get('start', now()->startOfWeek()))->startOfDay();
        $end = Carbon::parse($request->get('end', now()->endOfWeek()))->endOfDay();

        $transaksi = Transaksi::with('user', 'detail.obat')
            ->whereBetween('tanggal', [$start, $end])
            ->latest()
            ->paginate(10);

        $grafik = Transaksi::selectRaw('DATE(tanggal) as tanggal, SUM(total) as total')
            ->whereBetween('tanggal', [$start, $end])
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc') // Grafik dari kiri ke kanan (kronologis)
            ->get();

        return response()->json([
            'total_transaksi' => (int) Transaksi::whereBetween('tanggal', [$start, $end])->count(),
            'total_pendapatan' => (int) Transaksi::whereBetween('tanggal', [$start, $end])->sum('total'),
            'data' => $transaksi->items(),
            'meta' => [
                'current_page' => $transaksi->currentPage(),
                'last_page' => $transaksi->lastPage(),
                'total' => $transaksi->total(),
            ],
            'grafik' => $grafik
        ]);
    }

    // Di LaporanController.php
    public function bulanan(Request $request)
    {
        try {
            $bulan = $request->get('bulan', now()->month);
            $tahun = $request->get('tahun', now()->year);

            // Gunakan 'total' sesuai kolom database kamu
            $queryBase = Transaksi::whereMonth('tanggal', $bulan)->whereYear('tahun', $tahun);

            $dataTabel = Transaksi::with('user', 'detail.obat')
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->latest()
                ->paginate(10);

            $grafik = Transaksi::selectRaw('DATE(tanggal) as tanggal, SUM(total) as total') // Pastikan 'total' atau 'total_harga'
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->groupBy('tanggal')
                ->orderBy('tanggal', 'asc')
                ->get();

            return response()->json([
                'total_transaksi' => (int) $dataTabel->total(),
                'total_pendapatan' => (int) Transaksi::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->sum('total'),
                'data' => $dataTabel->items(),
                'meta' => [
                    'current_page' => $dataTabel->currentPage(),
                    'last_page' => $dataTabel->lastPage(),
                    'total' => $dataTabel->total(),
                ],
                'grafik' => $grafik
            ]);
        } catch (\Exception $e) {
            // Jika error, kirim pesan errornya ke frontend untuk debug
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function exportExcel(Request $request) 
    {
        return Excel::download(new PenjualanDetailExport($request), 'laporan-penjualan-detail.xlsx');
    }
}