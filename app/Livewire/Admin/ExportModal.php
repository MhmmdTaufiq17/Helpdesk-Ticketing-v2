<?php

namespace App\Livewire\Admin;

use App\Exports\TicketsReportExport;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ExportModal extends Component
{
    public $showModal = false;

    public $exportType = 'excel';

    public $isExporting = false;

    protected $listeners = ['openExportModal' => 'openModal'];

    public function openModal()
    {
        $this->showModal = true;
        $this->exportType = 'excel';
        $this->isExporting = false;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function export()
    {
        $this->isExporting = true;
        $fileName = 'laporan-statistik-tiket-'.now()->format('Y-m-d-His');

        try {
            switch ($this->exportType) {
                case 'excel':
                    // Simpan sementara ke storage
                    $filePath = 'exports/'.$fileName.'.xlsx';
                    Excel::store(new TicketsReportExport, $filePath, 'public');
                    $this->dispatch('download-file', url('storage/'.$filePath));
                    break;

                case 'csv':
                    $csvContent = $this->generateCsv();
                    $filePath = 'exports/'.$fileName.'.csv';
                    \Storage::disk('public')->put($filePath, $csvContent);
                    $this->dispatch('download-file', url('storage/'.$filePath));
                    break;

                case 'pdf':
                    $data = $this->getPdfData();
                    $pdf = Pdf::loadView('admin.reports.export-pdf', $data);
                    $filePath = 'exports/'.$fileName.'.pdf';
                    \Storage::disk('public')->put($filePath, $pdf->output());
                    $this->dispatch('download-file', url('storage/'.$filePath));
                    break;
            }

            $this->dispatch('success', 'File berhasil diekspor dan akan segera diunduh.');

        } catch (\Exception $e) {
            $this->dispatch('error', 'Gagal mengekspor data: '.$e->getMessage());
        }

        $this->isExporting = false;
        $this->closeModal();
    }

    private function generateCsv()
    {
        $handle = fopen('php://temp', 'w+');

        // Header Utama
        fputcsv($handle, ['LAPORAN STATISTIK TIKET']);
        fputcsv($handle, ['PERIODE: '.now()->format('d F Y')]);
        fputcsv($handle, ['DIBUAT: '.now()->format('d F Y H:i:s')]);
        fputcsv($handle, []);
        fputcsv($handle, ['Dibuat oleh Sistem Helpdesk']);
        fputcsv($handle, []);
        fputcsv($handle, []);

        // 1. RINGKASAN EKSEKUTIF
        fputcsv($handle, ['A. RINGKASAN EKSEKUTIF']);
        fputcsv($handle, ['Total Tiket', Ticket::count()]);
        fputcsv($handle, ['Total Admin', User::whereIn('role', ['admin', 'super_admin'])->count()]);
        $totalTickets = Ticket::count();
        $closedTickets = Ticket::where('status', 'closed')->count();
        $completionRate = $totalTickets > 0 ? round(($closedTickets / $totalTickets) * 100, 2) : 0;
        fputcsv($handle, ['Tingkat Penyelesaian', $completionRate.'%']);
        fputcsv($handle, []);

        // 2. STATUS TIKET
        fputcsv($handle, ['B. STATUS TIKET']);
        fputcsv($handle, ['Status', 'Jumlah', 'Persentase']);
        fputcsv($handle, ['Open', Ticket::where('status', 'open')->count(), $totalTickets > 0 ? round((Ticket::where('status', 'open')->count() / $totalTickets) * 100, 2).'%' : '0%']);
        fputcsv($handle, ['In Progress', Ticket::where('status', 'in_progress')->count(), $totalTickets > 0 ? round((Ticket::where('status', 'in_progress')->count() / $totalTickets) * 100, 2).'%' : '0%']);
        fputcsv($handle, ['Closed', $closedTickets, $completionRate.'%']);
        fputcsv($handle, []);

        // 3. PRIORITAS TIKET
        fputcsv($handle, ['C. PRIORITAS TIKET']);
        fputcsv($handle, ['Prioritas', 'Jumlah', 'Persentase']);
        fputcsv($handle, ['Tinggi (High)', Ticket::where('priority', 'high')->count(), $totalTickets > 0 ? round((Ticket::where('priority', 'high')->count() / $totalTickets) * 100, 2).'%' : '0%']);
        fputcsv($handle, ['Sedang (Medium)', Ticket::where('priority', 'medium')->count(), $totalTickets > 0 ? round((Ticket::where('priority', 'medium')->count() / $totalTickets) * 100, 2).'%' : '0%']);
        fputcsv($handle, ['Rendah (Low)', Ticket::where('priority', 'low')->count(), $totalTickets > 0 ? round((Ticket::where('priority', 'low')->count() / $totalTickets) * 100, 2).'%' : '0%']);
        fputcsv($handle, []);

        // 4. KATEGORI TIKET
        fputcsv($handle, ['D. KATEGORI TIKET']);
        fputcsv($handle, ['Kategori', 'Jumlah Tiket', 'Persentase']);
        $categories = Category::withCount('tickets')->get();
        foreach ($categories as $cat) {
            fputcsv($handle, [$cat->category_name, $cat->tickets_count, $totalTickets > 0 ? round(($cat->tickets_count / $totalTickets) * 100, 2).'%' : '0%']);
        }
        fputcsv($handle, []);

        // 5. TREN BULANAN
        fputcsv($handle, ['E. TREN BULANAN (6 BULAN TERAKHIR)']);
        fputcsv($handle, ['Bulan', 'Jumlah Tiket']);
        $monthlyStats = Ticket::select(
            DB::raw('DATE_FORMAT(created_at, "%M %Y") as month'),
            DB::raw('COUNT(*) as total'),
            DB::raw('MIN(created_at) as min_date')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('min_date', 'asc')
            ->get();
        foreach ($monthlyStats as $stat) {
            fputcsv($handle, [$stat->month, $stat->total]);
        }
        fputcsv($handle, []);

        // 6. AKTIVITAS HARIAN
        fputcsv($handle, ['F. AKTIVITAS HARIAN (7 HARI TERAKHIR)']);
        fputcsv($handle, ['Tanggal', 'Jumlah Tiket']);
        $dailyStats = Ticket::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        foreach ($dailyStats as $stat) {
            fputcsv($handle, [\Carbon\Carbon::parse($stat->date)->format('d M Y'), $stat->total]);
        }
        fputcsv($handle, []);

        // 7. KINERJA ADMIN
        fputcsv($handle, ['G. KINERJA ADMIN']);
        fputcsv($handle, ['Nama Admin', 'Jumlah Balasan', 'Role']);
        $adminStats = User::whereIn('role', ['admin', 'super_admin'])
            ->withCount('ticketReplies')
            ->orderBy('ticket_replies_count', 'desc')
            ->get();
        foreach ($adminStats as $admin) {
            fputcsv($handle, [$admin->name, $admin->ticket_replies_count, $admin->getRoleLabelAttribute()]);
        }
        fputcsv($handle, []);

        // Footer
        fputcsv($handle, []);
        fputcsv($handle, ['Laporan ini digenerate secara otomatis oleh sistem.']);
        fputcsv($handle, ['Data diambil pada: '.now()->format('d F Y H:i:s')]);

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        // Add BOM for UTF-8
        return "\xEF\xBB\xBF".$content;
    }

    private function getPdfData()
    {
        // ✅ Tren Bulanan (diperbaiki)
        $monthlyStats = Ticket::select(
            DB::raw('DATE_FORMAT(created_at, "%M %Y") as month'),
            DB::raw('COUNT(*) as total'),
            DB::raw('MIN(created_at) as min_date')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('min_date', 'asc')
            ->get();

        return [
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
            'monthlyStats' => $monthlyStats,
        ];
    }

    public function render()
    {
        return view('livewire.admin.export-modal');
    }
}
