<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Stat Cards ──────────────────────────────────────────
        $stats = [
            'total'       => Ticket::count(),
            'open'        => Ticket::where('status', 'open')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'closed'      => Ticket::where('status', 'closed')->count(),
        ];

        // ── 10 Tiket Terbaru ────────────────────────────────────
        $recentTickets = Ticket::with('category')
            ->latest()
            ->take(10)
            ->get();

        // ── Chart: Tiket per Bulan (tahun ini) ──────────────────
        $year = now()->year;

        $incomingRaw = Ticket::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $closedRaw = Ticket::selectRaw('MONTH(updated_at) as month, COUNT(*) as total')
            ->where('status', 'closed')
            ->whereYear('updated_at', $year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $chartMonthly = [
            'incoming' => collect(range(1, 12))->map(fn($m) => $incomingRaw->get($m, 0))->values()->toArray(),
            'closed'   => collect(range(1, 12))->map(fn($m) => $closedRaw->get($m, 0))->values()->toArray(),
        ];

        // ── Chart: Top 10 Kategori ───────────────────────────────
        $categoryData = Ticket::select('category_id', DB::raw('COUNT(*) as total'))
            ->with('category')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $chartCategory = [
            'labels' => $categoryData->map(fn($t) => optional($t->category)->category_name ?? 'Tidak Dikategori')->toArray(),
            'values' => $categoryData->pluck('total')->toArray(),
        ];

        return view('admin.dashboard', compact(
            'stats',
            'recentTickets',
            'chartMonthly',
            'chartCategory',
        ));
    }
}
