<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Report;

class ChartController extends Controller
{
    /* -----------------------------------------------------------------
       1. Perangkat paling sering rusak (label lengkap: item — divisi —
          cabang — pelapor) | Top 10
    ------------------------------------------------------------------*/
    public function perangkat()
    {
        $stats = DB::table('report_details')
            ->join('reports',    'report_details.report_id', '=', 'reports.id')
            ->join('items',      'report_details.item_id',   '=', 'items.id')
            ->join('locations',  'reports.location_id',      '=', 'locations.id')
            ->selectRaw("
                CONCAT(items.name, ' — ', reports.division, ' — ', locations.name, ' — ', reports.reporter_name) AS label,
                COUNT(*) AS total
            ")
            ->groupBy('label')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return view('chart.perangkat', [
            'labels' => $stats->pluck('label'),
            'data'   => $stats->pluck('total'),
        ]);
    }

    /* -----------------------------------------------------------------
       2. IT Support paling aktif
          - Hitung solo (A, B, C, …)
          - Hitung kombinasi tim (A&B, B&C, …)
    ------------------------------------------------------------------*/
    public function itSupport()
    {
        $teamStats = DB::table('reports')
            ->join('report_user', 'reports.id', '=', 'report_user.report_id')
            ->join('users',       'users.id',   '=', 'report_user.user_id')
            ->select('reports.id', 'users.name')
            ->get()
            ->groupBy('id')                              // kumpulkan per laporan
            ->map(fn($rows) =>                           // bentuk label combo
                $rows->pluck('name')->sort()->implode('&')
            )
            ->countBy()                                 // hitung frekuensi setiap label
            ->sortDesc();

        return view('chart.it_support', [
            'labels' => $teamStats->keys(),
            'data'   => $teamStats->values(),
        ]);
    }

    /* -----------------------------------------------------------------
       3. Jumlah laporan per bulan | 12 bulan terakhir
    ------------------------------------------------------------------*/
    public function perBulan()
    {
        $stats = Report::selectRaw("
                    DATE_FORMAT(created_at, '%Y-%m') AS ym,
                    COUNT(*) AS total
                ")
                ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
                ->groupBy('ym')
                ->orderBy('ym')
                ->get();

        return view('chart.bulanan', [
            'labels' => $stats->pluck('ym')->map(
                fn($v) => Carbon::createFromFormat('Y-m', $v)->translatedFormat('M Y')
            ),
            'data'   => $stats->pluck('total'),
        ]);
    }

    /* -----------------------------------------------------------------
       4. Jumlah laporan per triwulan | 4 kuartal terakhir
    ------------------------------------------------------------------*/
    public function perTriwulan()
    {
        $stats = Report::selectRaw("
                    CONCAT(YEAR(created_at), '-Q', QUARTER(created_at)) AS yq,
                    COUNT(*) AS total
                ")
                ->where('created_at', '>=', now()->subQuarters(3)->firstOfQuarter())
                ->groupBy('yq')
                ->orderBy('yq')
                ->get();

        return view('chart.triwulan', [
            'labels' => $stats->pluck('yq'),
            'data'   => $stats->pluck('total'),
        ]);
    }

    /* -----------------------------------------------------------------
       5. Cabang paling sering rusak
    ------------------------------------------------------------------*/
    public function cabang()
    {
        $stats = DB::table('reports')
            ->join('locations', 'reports.location_id', '=', 'locations.id')
            ->select('locations.name', DB::raw('COUNT(*) AS total'))
            ->groupBy('locations.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return view('chart.cabang', [
            'labels' => $stats->pluck('name'),
            'data'   => $stats->pluck('total'),
        ]);
    }
}
