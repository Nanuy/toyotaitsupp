<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Location;
use App\Models\Item;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ChartExport;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ChartController extends Controller
{
    public function export()
    {
        // Get chart data from session - adjust based on your actual data source
        $chartData = session()->get('chart_data') ?? [
            ['Category', 'Values'],
            ['A', 45],
            ['B', 65],
            ['C', 32]
        ];

        return Excel::download(new ChartExport($chartData), 'chart-data.xlsx');
    }
    /**
     * Menampilkan halaman dashboard dengan layout 'superadmin'.
     * View yang digunakan adalah 'chart.perangkat'.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $locations = Location::orderBy('name')->get();
        $items = Item::orderBy('name')->get();
        
        return view('chart.perangkat', compact('locations', 'items'));
    }

    /**
     * Menampilkan halaman chart untuk IT Support
     *
     * @return \Illuminate\View\View
     */
    public function itSupportCharts()
    {
        $locations = Location::orderBy('name')->get();
        $items = Item::orderBy('name')->get();
        
        return view('chart.itsupport', compact('locations', 'items'));
    }

    /**
     * Mengambil data untuk semua chart berdasarkan filter tanggal dan sorting.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChartData(Request $request)
    {
        try {
            Log::info('=== ChartController::getChartData called ===');
            
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $locationFilter = $request->input('location_filter');
            $itemFilter = $request->input('item_filter');
            $sortBy = $request->input('sort_by', 'total_desc');
            $limitData = $request->input('limit_data', '5');

            Log::info('Chart data request parameters', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'location_filter' => $locationFilter,
                'item_filter' => $itemFilter,
                'sort_by' => $sortBy,
                'limit_data' => $limitData,
                'all_inputs' => $request->all()
            ]);

            // Base query
            $reports = Report::query();
            Log::info('Base query created');

            // Apply date filter
            if ($startDate && $endDate) {
                $reports->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                Log::info('Date filter applied', ['start' => $startDate, 'end' => $endDate]);
            } else {
                // Default to last 30 days if no date specified
                $reports->where('created_at', '>=', now()->subDays(30));
                Log::info('Default date filter applied (last 30 days)');
            }

            // Apply location filter
            if ($locationFilter) {
                $reports->where('location_id', $locationFilter);
                Log::info('Location filter applied', ['location_id' => $locationFilter]);
            }

            // Apply item filter (dari tabel report_details, bukan reports)
            if ($itemFilter) {
                // Join ke report_details dan filter berdasarkan item_id
                $reports->whereHas('details', function($q) use ($itemFilter) {
                    $q->where('item_id', $itemFilter);
                });
                Log::info('Item filter applied via report_details', ['item_id' => $itemFilter]);
            }
            
            // Log the total count before processing
            $totalReports = $reports->count();
            Log::info('Total reports after filters', ['count' => $totalReports]);

            // Get summary data
            $summaryData = $this->getSummaryData($reports->clone(), $startDate, $endDate);

            // Data untuk Chart per Cabang
            $cabangData = $this->getCabangData($reports->clone(), $sortBy, $limitData);

            // Data untuk Chart per Kategori (berdasarkan report_details - satu item per laporan)
            $kategoriData = $this->getKategoriData($reports->clone(), $sortBy, $limitData);

            // Data untuk Chart Rentang Waktu (per hari)
            $waktuData = $this->getWaktuData($reports->clone());

            // Data untuk Chart Top Items (berdasarkan report_details)
            $topItemData = $this->getTopItemData($reports->clone(), $sortBy, $limitData);

            $response = [
                'summary' => $summaryData,
                'cabangData' => $cabangData,
                'kategoriData' => $kategoriData,
                'waktuData' => $waktuData,
                'topItemData' => $topItemData,
            ];

            Log::info('Chart data response prepared', [
                'summary_count' => count($summaryData),
                'cabang_count' => count($cabangData),
                'kategori_count' => count($kategoriData),
                'waktu_count' => count($waktuData),
                'topItem_count' => count($topItemData)
            ]);

            Log::info('=== ChartController::getChartData completed successfully ===');
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('=== Error in ChartController::getChartData ===', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Terjadi kesalahan saat memuat data chart',
                'message' => $e->getMessage(),
                'debug_info' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Get IT Support specific chart data
     */
    public function getITSupportChartData(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $locationFilter = $request->input('location_filter');
            $itemFilter = $request->input('item_filter');
            $sortBy = $request->input('sort_by', 'total_desc');
            $limitData = $request->input('limit_data', '5');

            // Base query
            $reports = Report::query();

            // Apply date filter
            if ($startDate && $endDate) {
                $reports->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            } else {
                $reports->where('created_at', '>=', now()->subDays(30));
            }

            // Apply location filter
            if ($locationFilter) {
                $reports->where('location_id', $locationFilter);
            }

            // Apply item filter
            if ($itemFilter) {
                $reports->whereHas('details', function($q) use ($itemFilter) {
                    $q->where('item_id', $itemFilter);
                });
            }

            // Get IT Support specific data
            $summaryData = $this->getITSupportSummaryData($reports->clone(), $startDate, $endDate);
            $statusData = $this->getStatusData($reports->clone());
            $itSupportPerformanceData = $this->getITSupportPerformanceData($reports->clone(), $sortBy, $limitData);
            $monthlyData = $this->getMonthlyData($reports->clone());
            $topItemsData = $this->getTopItemData($reports->clone(), $sortBy, $limitData);

            $response = [
                'summary' => $summaryData,
                'statusData' => $statusData,
                'itSupportPerformanceData' => $itSupportPerformanceData,
                'monthlyData' => $monthlyData,
                'topItemsData' => $topItemsData,
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Error in getITSupportChartData: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Terjadi kesalahan saat memuat data chart IT Support',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get IT Support summary data
     */
    private function getITSupportSummaryData($query, $startDate, $endDate)
    {
        $totalReports = $query->count();
        $waitingReports = $query->clone()->where('status', 'waiting')->count();
        $acceptedReports = $query->clone()->where('status', 'accepted')->count();
        $completedReports = $query->clone()->where('status', 'completed')->count();

        // Calculate average per day
        $days = 1;
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $days = max(1, $start->diffInDays($end) + 1);
        } else {
            $days = 30;
        }
        
        $avgPerDay = $totalReports / $days;

        return [
            'totalReports' => $totalReports,
            'waitingReports' => $waitingReports,
            'acceptedReports' => $acceptedReports,
            'completedReports' => $completedReports,
            'avgPerDay' => round($avgPerDay, 1)
        ];
    }

    /**
     * Get status distribution data
     */
    private function getStatusData($query)
    {
        $statusData = $query->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        return [
            'labels' => $statusData->pluck('status'),
            'values' => $statusData->pluck('total'),
        ];
    }

    /**
     * Get IT Support performance data
     */
    private function getITSupportPerformanceData($query, $sortBy, $limitData)
    {
        $performanceData = DB::table('report_user')
            ->join('users', 'users.id', '=', 'report_user.user_id')
            ->join('reports', 'reports.id', '=', 'report_user.report_id')
            ->where('users.role', 'it_supp')
            ->whereIn('reports.id', $query->pluck('id'))
            ->select('users.name', DB::raw('COUNT(*) as total'))
            ->groupBy('users.name')
            ->orderBy('total', 'desc')
            ->limit($limitData === 'all' ? 1000 : (int)$limitData)
            ->get();

        return [
            'labels' => $performanceData->pluck('name'),
            'values' => $performanceData->pluck('total'),
        ];
    }

    /**
     * Get monthly data
     */
    private function getMonthlyData($query)
    {
        $monthlyData = $query->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'labels' => $monthlyData->pluck('month'),
            'values' => $monthlyData->pluck('total'),
        ];
    }

    /**
     * Get summary statistics
     */
    private function getSummaryData($query, $startDate, $endDate)
    {
        $totalReports = $query->count();
        
        $activeBranches = $query->clone()
            ->distinct('location_id')
            ->whereNotNull('location_id')
            ->count();
        
        $itemCategories = $query->clone()
            ->distinct('item_id')
            ->whereNotNull('item_id')
            ->count();

        // Calculate average per day
        $days = 1;
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $days = max(1, $start->diffInDays($end) + 1);
        } else {
            $days = 30; // Default period
        }
        
        $avgPerDay = $totalReports / $days;

        return [
            'totalReports' => $totalReports,
            'activeBranches' => $activeBranches,
            'itemCategories' => $itemCategories,
            'avgPerDay' => round($avgPerDay, 1)
        ];
    }

    /**
     * Get data for branch chart
     */
    private function getCabangData($query, $sortBy, $limitData)
    {
        $cabangQuery = $query->with('location')
            ->select('location_id', DB::raw('count(*) as total'))
            ->groupBy('location_id')
            ->whereNotNull('location_id');

        // Apply sorting
        $this->applySorting($cabangQuery, $sortBy, 'location');

        // Apply limit
        if ($limitData !== 'all') {
            $cabangQuery->limit((int)$limitData);
        }

        $cabangData = $cabangQuery->get();
        
        $labels = $cabangData->map(function ($item) {
            return $item->location ? $item->location->name : 'Tidak Ada Lokasi';
        });
        
        $values = $cabangData->pluck('total');

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * Data untuk Chart per Kategori (berdasarkan report_details - satu item per laporan)
     */
    private function getKategoriData($query, $sortBy, $limitData)
    {
        // Ambil data kategori dari tabel report_details
        // Gunakan DISTINCT untuk memastikan satu item per laporan
        $kategori = DB::table('report_details')
            ->join('reports', 'report_details.report_id', '=', 'reports.id')
            ->join('items', 'report_details.item_id', '=', 'items.id')
            ->select('items.name as label', DB::raw('COUNT(DISTINCT reports.id) as total'))
            ->whereIn('report_details.report_id', $query->pluck('id'))
            ->groupBy('items.name')
            ->orderBy('total', 'desc')
            ->limit($limitData === 'all' ? 1000 : (int)$limitData)
            ->get();

        return [
            'labels' => $kategori->pluck('label'),
            'values' => $kategori->pluck('total'),
        ];
    }

    /**
     * Get data for time trend chart
     */
    private function getWaktuData($query)
    {
        $waktuData = $query->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = $waktuData->pluck('date');
        $values = $waktuData->pluck('total');

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * Data untuk Chart Top Items (berdasarkan report_details - satu item per laporan)
     */
    private function getTopItemData($query, $sortBy, $limitData)
    {
        $topItems = DB::table('report_details')
            ->join('reports', 'report_details.report_id', '=', 'reports.id')
            ->join('items', 'report_details.item_id', '=', 'items.id')
            ->select('items.name as label', DB::raw('COUNT(DISTINCT reports.id) as total'))
            ->whereIn('report_details.report_id', $query->pluck('id'))
            ->groupBy('items.name')
            ->orderBy('total', 'desc')
            ->limit($limitData === 'all' ? 1000 : (int)$limitData)
            ->get();

        return [
            'labels' => $topItems->pluck('label'),
            'values' => $topItems->pluck('total'),
        ];
    }

    /**
     * Apply sorting to query based on sort parameter
     */
    private function applySorting($query, $sortBy, $relation = null)
    {
        switch ($sortBy) {
            case 'total_desc':
                $query->orderByDesc('total');
                break;
            case 'total_asc':
                $query->orderBy('total');
                break;
            case 'name_asc':
                if ($relation) {
                    $tableName = $relation === 'location' ? 'locations' : 'items';
                    $foreignKey = $relation === 'location' ? 'reports.location_id' : 'reports.item_id';
                    $primaryKey = $tableName . '.id';
                    $nameColumn = $tableName . '.name';
                    
                    $query->join($tableName, $foreignKey, '=', $primaryKey)
                          ->orderBy($nameColumn);
                }
                break;
            case 'name_desc':
                if ($relation) {
                    $tableName = $relation === 'location' ? 'locations' : 'items';
                    $foreignKey = $relation === 'location' ? 'reports.location_id' : 'reports.item_id';
                    $primaryKey = $tableName . '.id';
                    $nameColumn = $tableName . '.name';
                    
                    $query->join($tableName, $foreignKey, '=', $primaryKey)
                          ->orderByDesc($nameColumn);
                }
                break;
            case 'date_desc':
                $query->orderByDesc('reports.created_at');
                break;
            case 'date_asc':
                $query->orderBy('reports.created_at');
                break;
            default:
                $query->orderByDesc('total');
        }
    }

    /**
     * Get reports data with pagination for table view
     */
    public function getReportsData(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $locationFilter = $request->input('location_filter');
            $itemFilter = $request->input('item_filter');
            $search = $request->input('search');
            $perPage = $request->input('per_page', 10);

            $query = Report::with(['location', 'details.item', 'user']);

            // Apply filters
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }

            if ($locationFilter) {
                $query->where('location_id', $locationFilter);
            }

            if ($itemFilter) {
                $query->whereHas('details', function($q) use ($itemFilter) {
                    $q->where('item_id', $itemFilter);
                });
            }

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('issue_description', 'like', "%{$search}%")
                      ->orWhere('reporter_name', 'like', "%{$search}%")
                      ->orWhereHas('location', function($locationQuery) use ($search) {
                          $locationQuery->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('details.item', function($itemQuery) use ($search) {
                          $itemQuery->where('name', 'like', "%{$search}%");
                      });
                });
            }

            $reports = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Transform the data to include the first item from report_details
            $reports->getCollection()->transform(function ($report) {
                $firstDetail = $report->details->first();
                $report->item = $firstDetail ? $firstDetail->item : null;
                return $report;
            });

            return response()->json($reports);
        } catch (\Exception $e) {
            Log::error('Error in getReportsData: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Terjadi kesalahan saat memuat data laporan',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comparison data between two periods
     */
    public function getComparisonData(Request $request)
    {
        $period1Start = $request->input('period1_start');
        $period1End = $request->input('period1_end');
        $period2Start = $request->input('period2_start');
        $period2End = $request->input('period2_end');

        // Period 1 data
        $period1Reports = Report::whereBetween('created_at', [
            $period1Start . ' 00:00:00', 
            $period1End . ' 23:59:59'
        ])->count();

        // Period 2 data
        $period2Reports = Report::whereBetween('created_at', [
            $period2Start . ' 00:00:00', 
            $period2End . ' 23:59:59'
        ])->count();

        // Calculate percentage change
        $percentageChange = 0;
        if ($period2Reports > 0) {
            $percentageChange = (($period1Reports - $period2Reports) / $period2Reports) * 100;
        }

        return response()->json([
            'period1' => [
                'start' => $period1Start,
                'end' => $period1End,
                'reports' => $period1Reports
            ],
            'period2' => [
                'start' => $period2Start,
                'end' => $period2End,
                'reports' => $period2Reports
            ],
            'comparison' => [
                'difference' => $period1Reports - $period2Reports,
                'percentage_change' => round($percentageChange, 2),
                'trend' => $period1Reports > $period2Reports ? 'up' : ($period1Reports < $period2Reports ? 'down' : 'same')
            ]
        ]);
    }

    /**
     * Get real-time statistics
     */
    public function getRealTimeStats()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisWeek = Carbon::now()->startOfWeek();
        $lastWeek = Carbon::now()->subWeek()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        return response()->json([
            'today' => Report::whereDate('created_at', $today)->count(),
            'yesterday' => Report::whereDate('created_at', $yesterday)->count(),
            'this_week' => Report::where('created_at', '>=', $thisWeek)->count(),
            'last_week' => Report::whereBetween('created_at', [
                $lastWeek, 
                $lastWeek->copy()->endOfWeek()
            ])->count(),
            'this_month' => Report::where('created_at', '>=', $thisMonth)->count(),
            'last_month' => Report::whereBetween('created_at', [
                $lastMonth, 
                $lastMonth->copy()->endOfMonth()
            ])->count(),
            'total_all_time' => Report::count(),
            'last_updated' => now()->toISOString()
        ]);
    }

    /**
     * Export data to Excel/CSV
     */
    public function exportData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $locationFilter = $request->input('location_filter');
        $itemFilter = $request->input('item_filter');
        $format = $request->input('format', 'excel'); // excel, csv, pdf

        $query = Report::with(['location', 'details.item', 'user']);

        // Apply filters
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        } else {
            $query->where('created_at', '>=', now()->subDays(30));
        }

        if ($locationFilter) {
            $query->where('location_id', $locationFilter);
        }

        if ($itemFilter) {
            $query->whereHas('details', function($q) use ($itemFilter) {
                $q->where('item_id', $itemFilter);
            });
        }

        $reports = $query->orderBy('created_at', 'desc')->get();

        // Transform data for export
        $exportData = $reports->map(function ($report) {
            $firstDetail = $report->details->first();
            $itemName = $firstDetail && $firstDetail->item ? $firstDetail->item->name : 'Tidak Ada';
            
            return [
                'ID' => $report->id,
                'Tanggal' => $report->created_at->format('d/m/Y H:i'),
                'Cabang' => $report->location ? $report->location->name : 'Tidak Ada',
                'Item/Kategori' => $itemName,
                'Deskripsi' => $report->description ?? $report->issue_description ?? '',
                'Status' => $report->status ?? 'Pending',
                'Pelapor' => $report->reporter_name ?? 'Tidak Diketahui',
                'Prioritas' => $report->priority ?? 'Normal',
                'Catatan' => $report->notes ?? ''
            ];
        });

        // For now, return JSON response
        // In production, implement with Laravel Excel package
        if ($format === 'csv') {
            return $this->exportToCsv($exportData, $startDate, $endDate);
        } elseif ($format === 'pdf') {
            return $this->exportToPdf($exportData, $startDate, $endDate);
        } else {
            // Excel format (default)
            return response()->json([
                'message' => 'Export ready for download',
                'format' => $format,
                'total_records' => $exportData->count(),
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                'preview_data' => $exportData->take(5),
                'download_url' => route('reports.download', [
                    'format' => $format,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'location_filter' => $locationFilter,
                    'item_filter' => $itemFilter
                ])
            ]);
        }
    }

    /**
     * Export to CSV format
     */
    private function exportToCsv($data, $startDate, $endDate)
    {
        $filename = 'laporan_' . ($startDate ? $startDate . '_to_' . $endDate : 'all') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            if ($data->isNotEmpty()) {
                fputcsv($file, array_keys($data->first()));
            }
            
            // Add data rows
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to PDF format
     */
    private function exportToPdf($data, $startDate, $endDate)
    {
        // This would require a PDF library like TCPDF or DomPDF
        // For now, return a placeholder response
        return response()->json([
            'message' => 'PDF export feature - implement with PDF library',
            'suggestion' => 'Use barryvdh/laravel-dompdf or similar package',
            'total_records' => $data->count()
        ]);
    }

    /**
     * Download exported file
     */
    public function downloadExport(Request $request)
    {
        // This method would handle the actual file download
        // after processing in exportData method
        $format = $request->input('format', 'excel');
        
        return response()->json([
            'message' => 'Download endpoint ready',
            'format' => $format,
            'note' => 'Implement actual file generation and download here'
        ]);
    }

    /**
     * Get dashboard widgets data
     */
    public function getWidgetsData(Request $request)
    {
        try {
            $period = $request->input('period', '30'); // days

            $startDate = now()->subDays((int)$period)->startOfDay();
            $endDate = now()->endOfDay();

            // Top locations by reports
            $topLocations = Report::with('location')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select('location_id', DB::raw('count(*) as total'))
                ->groupBy('location_id')
                ->orderByDesc('total')
                ->limit(5)
                ->get()
                ->map(function($item) {
                    return [
                        'name' => $item->location ? $item->location->name : 'Tidak Ada',
                        'total' => $item->total
                    ];
                });

            // Recent reports
            $recentReports = Report::with(['location', 'details.item', 'user'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($report) {
                    $firstDetail = $report->details->first();
                    $itemName = $firstDetail && $firstDetail->item ? $firstDetail->item->name : 'Tidak Ada';
                    
                    return [
                        'id' => $report->id,
                        'date' => $report->created_at->format('d/m/Y H:i'),
                        'location' => $report->location ? $report->location->name : 'Tidak Ada',
                        'item' => $itemName,
                        'user' => $report->reporter_name ?? 'Tidak Diketahui',
                        'status' => $report->status ?? 'Pending'
                    ];
                });

            // Trend data for mini charts
            $trendData = Report::whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(function($item) {
                    return [
                        'date' => $item->date,
                        'total' => $item->total
                    ];
                });

            return response()->json([
                'top_locations' => $topLocations,
                'recent_reports' => $recentReports,
                'trend_data' => $trendData,
                'period_info' => [
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'days' => $period
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getWidgetsData: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Terjadi kesalahan saat memuat data widget',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
