<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use App\Models\Transaksi;
use Illuminate\Http\Request;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        // Data Stok Menipis
        $stokMenipis = Obat::with('kategori')
        ->whereColumn('stok', '<=', 'stok_minimal')
        ->paginate(5); // 10 data per halaman

        // Transaksi & Pendapatan HARI INI
        $totalTransaksiToday = Transaksi::whereDate('tanggal', $today)->count();
        $totalPendapatanToday = Transaksi::whereDate('tanggal', $today)->sum('total');

        // Transaksi & Pendapatan KEMARIN
        $totalTransaksiYesterday = Transaksi::whereDate('tanggal', $yesterday)->count();
        $totalPendapatanYesterday = Transaksi::whereDate('tanggal', $yesterday)->sum('total');

        $response = [
            'tanggal' => $today,
            'total_transaksi' => $totalTransaksiToday,
            'total_pendapatan' => $totalPendapatanToday,
            'stok_menipis' => $stokMenipis,
            'stats' => [
                'transaksi' => [
                    'today' => $totalTransaksiToday,
                    'yesterday' => $totalTransaksiYesterday
                ],
                'pendapatan' => [
                    'today' => $totalPendapatanToday,
                    'yesterday' => $totalPendapatanYesterday
                ]
            ]
        ];

        // Role-specific: admin dapat semua data
        if ($user->role->name === 'admin') {
            $response['master_data'] = [
                'total_obat' => Obat::count(),
                'total_kategori' => \App\Models\Kategori::count(),
                'total_karyawan' => \App\Models\User::whereHas('role', fn($q) => $q->where('name', 'karyawan'))->count(),
            ];
        }

        return response()->json($response);
    }
}
