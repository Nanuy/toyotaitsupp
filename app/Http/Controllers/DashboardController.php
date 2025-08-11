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

    /** 2. IT‑Support teraktif (solo / tim) dengan detail individual */
    $teamCounts = DB::table('report_user')
        ->join('users', 'users.id', '=', 'report_user.user_id')
        ->select('report_user.report_id', 'users.name')
        ->get()
        ->groupBy('report_id')
        ->map(fn($rows) =>
            $rows->pluck('name')->sort()->implode(' & ')
        )
        ->countBy()
        ->sortDesc();

    // Individual IT Support counts
    $individualCounts = DB::table('report_user')
        ->join('users', 'users.id', '=', 'report_user.user_id')
        ->where('users.role', 'it_supp')
        ->select('users.name', DB::raw('COUNT(*) as total'))
        ->groupBy('users.name')
        ->orderByDesc('total')
        ->get()
        ->pluck('total', 'name');

    // Combined team work (reports with multiple IT support)
    $combinedWork = DB::table('report_user')
        ->join('users', 'users.id', '=', 'report_user.user_id')
        ->select('report_user.report_id')
        ->groupBy('report_user.report_id')
        ->havingRaw('COUNT(*) > 1')
        ->get()
        ->count();

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

    /**
     * Menampilkan tracking jumlah laporan per nomor telepon
     */
    public function phoneTracking()
    {
        $phoneStats = Report::select('contact', 'reporter_name')
            ->selectRaw('COUNT(*) as total_reports')
            ->selectRaw('MAX(created_at) as last_report_date')
            ->groupBy('contact', 'reporter_name')
            ->having('total_reports', '>', 1)
            ->orderBy('total_reports', 'desc')
            ->get();

        return view('dashboard.phone-tracking', compact('phoneStats'));
    }

    /**
     * Menampilkan detail laporan dari nomor telepon tertentu
     */
    public function phoneDetail($phone)
    {
        $reports = Report::where('contact', $phone)
            ->with(['location', 'details.item'])
            ->orderBy('created_at', 'desc')
            ->get();

        $phoneInfo = [
            'phone' => $phone,
            'reporter_name' => $reports->first()->reporter_name ?? 'Unknown',
            'total_reports' => $reports->count(),
            'first_report' => $reports->last()->created_at ?? null,
            'last_report' => $reports->first()->created_at ?? null,
        ];

        return view('dashboard.phone-detail', compact('reports', 'phoneInfo'));
    }
}

?>