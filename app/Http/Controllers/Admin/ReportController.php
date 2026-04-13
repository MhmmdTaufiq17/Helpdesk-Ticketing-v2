<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Category;
use App\Models\User;
use App\Exports\TicketsReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // ... kode yang sudah ada
        $statusStats = [
            'open' => Ticket::where('status', 'open')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
        ];

        $priorityStats = [
            'high' => Ticket::where('priority', 'high')->count(),
            'medium' => Ticket::where('priority', 'medium')->count(),
            'low' => Ticket::where('priority', 'low')->count(),
        ];

        $categories = Category::withCount('tickets')->get();
        $categoryLabels = $categories->pluck('category_name');
        $categoryCounts = $categories->pluck('tickets_count');

        $monthlyStats = Ticket::select(
                DB::raw('DATE_FORMAT(created_at, "%M %Y") as month'),
                DB::raw('COUNT(*) as total'),
                DB::raw('MIN(created_at) as min_date')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('min_date', 'asc')
            ->get();

        $monthlyLabels = $monthlyStats->pluck('month');
        $monthlyTotals = $monthlyStats->pluck('total');

        $dailyStats = Ticket::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('MIN(created_at) as min_date')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('min_date', 'asc')
            ->get();

        $dailyLabels = $dailyStats->pluck('date')->map(function($date) {
            return \Carbon\Carbon::parse($date)->format('d M');
        });
        $dailyTotals = $dailyStats->pluck('total');

        $adminStats = User::whereIn('role', ['admin', 'super_admin'])
            ->withCount('ticketReplies')
            ->get()
            ->map(function($admin) {
                return [
                    'name' => $admin->name,
                    'replies_count' => $admin->ticket_replies_count,
                ];
            });

        $totalTickets = Ticket::count();
        $totalAdmins = User::whereIn('role', ['admin', 'super_admin'])->count();
        $avgResponseTime = '2.5 jam';

        return view('admin.reports.index', compact(
            'statusStats',
            'priorityStats',
            'categoryLabels',
            'categoryCounts',
            'monthlyLabels',
            'monthlyTotals',
            'dailyLabels',
            'dailyTotals',
            'adminStats',
            'totalTickets',
            'totalAdmins',
            'avgResponseTime'
        ));
    }

    // ✅ Export Excel
    public function exportExcel()
    {
        $fileName = 'laporan-statistik-tiket-' . now()->format('Y-m-d-His') . '.xlsx';
        return Excel::download(new TicketsReportExport(), $fileName);
    }

    // ✅ Export CSV
    public function exportCsv()
    {
        $fileName = 'laporan-statistik-tiket-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() {
            $handle = fopen('php://output', 'w');

            // Header
            fputcsv($handle, ['LAPORAN STATISTIK TIKET']);
            fputcsv($handle, ['Dicetak: ' . now()->format('d F Y H:i:s')]);
            fputcsv($handle, []);

            // Total Tiket
            fputcsv($handle, ['A. RINGKASAN TOTAL']);
            fputcsv($handle, ['Total Tiket', Ticket::count()]);
            fputcsv($handle, ['Total Admin', User::whereIn('role', ['admin', 'super_admin'])->count()]);
            fputcsv($handle, []);

            // Status Tiket
            fputcsv($handle, ['B. STATUS TIKET']);
            fputcsv($handle, ['Status', 'Jumlah']);
            fputcsv($handle, ['Open', Ticket::where('status', 'open')->count()]);
            fputcsv($handle, ['In Progress', Ticket::where('status', 'in_progress')->count()]);
            fputcsv($handle, ['Closed', Ticket::where('status', 'closed')->count()]);
            fputcsv($handle, []);

            // Prioritas
            fputcsv($handle, ['C. PRIORITAS TIKET']);
            fputcsv($handle, ['Prioritas', 'Jumlah']);
            fputcsv($handle, ['Tinggi', Ticket::where('priority', 'high')->count()]);
            fputcsv($handle, ['Sedang', Ticket::where('priority', 'medium')->count()]);
            fputcsv($handle, ['Rendah', Ticket::where('priority', 'low')->count()]);
            fputcsv($handle, []);

            // Kategori
            fputcsv($handle, ['D. KATEGORI TIKET']);
            fputcsv($handle, ['Kategori', 'Jumlah']);
            $categories = Category::withCount('tickets')->get();
            foreach ($categories as $cat) {
                fputcsv($handle, [$cat->category_name, $cat->tickets_count]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ✅ Export PDF
    public function exportPdf()
    {
        $data = [
            'totalTickets' => Ticket::count(),
            'totalAdmins' => User::whereIn('role', ['admin', 'super_admin'])->count(),
            'statusStats' => [
                'open' => Ticket::where('status', 'open')->count(),
                'in_progress' => Ticket::where('status', 'in_progress')->count(),
                'closed' => Ticket::where('status', 'closed')->count(),
            ],
            'priorityStats' => [
                'high' => Ticket::where('priority', 'high')->count(),
                'medium' => Ticket::where('priority', 'medium')->count(),
                'low' => Ticket::where('priority', 'low')->count(),
            ],
            'categoryStats' => Category::withCount('tickets')->get(),
            'monthlyStats' => Ticket::select(
                    DB::raw('DATE_FORMAT(created_at, "%M %Y") as month'),
                    DB::raw('COUNT(*) as total')
                )
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('created_at', 'asc')
                ->get(),
        ];

        $pdf = Pdf::loadView('admin.reports.export-pdf', $data);
        $fileName = 'laporan-statistik-tiket-' . now()->format('Y-m-d-His') . '.pdf';

        return $pdf->download($fileName);
    }
}
