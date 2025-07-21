<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Report;

class DashboardController extends Controller
{
    public function index()
{
    /** 1. Top 10 item rusak */
    $items = DB::table('reports')
        ->join('items', 'items.id', '=', 'reports.item_id')
        ->select('items.name', DB::raw('COUNT(*) total'))
        ->groupBy('items.name')
        ->orderByDesc('total')
        ->limit(10)
        ->get();

    /** 2. IT‑Support teraktif (solo / tim) */
    $teamCounts = DB::table('report_user')
        ->join('users', 'users.id', '=', 'report_user.user_id')
        ->select('report_user.report_id', 'users.name')
        ->get()
        ->groupBy('report_id')                 // ✅ pakai report_id, bukan id
        ->map(fn($rows) =>
            $rows->pluck('name')->sort()->implode(' & ')
        )
        ->countBy()
        ->sortDesc();

    /** 3. Laporan per bulan (rolling 12) */
    $monthStats = Report::selectRaw('DATE_FORMAT(created_at,"%Y-%m") ym, COUNT(*) total')
        ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
        ->groupBy('ym')
        ->orderBy('ym')
        ->pluck('total','ym');

    /** 4. Laporan per kuartal (rolling 4) */
    $quarterStats = Report::selectRaw('CONCAT(YEAR(created_at),"-Q",QUARTER(created_at)) yq, COUNT(*) total')
        ->where('created_at', '>=', now()->subMonths(9)->startOfQuarter()) // 4 kuartal
        ->groupBy('yq')
        ->orderBy('yq')
        ->pluck('total','yq');

    /** 5. Cabang tersibuk */
    $branches = DB::table('reports')
        ->join('locations','locations.id','=','reports.location_id')
        ->select('locations.name', DB::raw('COUNT(*) total'))
        ->groupBy('locations.name')
        ->orderByDesc('total')
        ->get();

    return view('dashboard', [
        'itemLabels'    => $items->pluck('name')->toArray(),
        'itemData'      => $items->pluck('total')->toArray(),

        'teamLabels'    => $teamCounts->keys()->toArray(),
        'teamData'      => $teamCounts->values()->toArray(),

        'monthLabels'   => $monthStats->keys()
                                       ->map(fn($ym)=>Carbon::createFromFormat('Y-m',$ym)->translatedFormat('M Y'))
                                       ->values()
                                       ->toArray(),
        'monthData'     => $monthStats->values()->toArray(),

        'quarterLabels' => $quarterStats->keys()->toArray(),
        'quarterData'   => $quarterStats->values()->toArray(),

        'branchLabels'  => $branches->pluck('name')->toArray(),
        'branchData'    => $branches->pluck('total')->toArray(),
    ]);
}

}

?>