<?php

namespace App\Exports;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;

class TicketsReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize
{
    protected $totalTickets;
    protected $statusStats;
    protected $priorityStats;
    protected $categoryStats;
    protected $monthlyStats;
    protected $dailyStats;
    protected $adminStats;

    public function __construct()
    {
        $this->totalTickets = Ticket::count();
        $this->statusStats = [
            'open' => Ticket::where('status', 'open')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
        ];
        $this->priorityStats = [
            'high' => Ticket::where('priority', 'high')->count(),
            'medium' => Ticket::where('priority', 'medium')->count(),
            'low' => Ticket::where('priority', 'low')->count(),
        ];
        $this->categoryStats = Category::withCount('tickets')->get();
        $this->monthlyStats = Ticket::select(
                DB::raw('DATE_FORMAT(created_at, "%M %Y") as month'),
                DB::raw('COUNT(*) as total'),
                DB::raw('MIN(created_at) as min_date')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('min_date', 'asc')
            ->get();
        $this->dailyStats = Ticket::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        $this->adminStats = User::whereIn('role', ['admin', 'super_admin'])
            ->withCount('ticketReplies')
            ->orderBy('ticket_replies_count', 'desc')
            ->get();
    }

    public function collection()
    {
        $reportData = collect();

        // Header Laporan
        $reportData->push(['LAPORAN STATISTIK TIKET', '', '', '']);
        $reportData->push(['PERIODE: ' . now()->format('d F Y'), '', '', '']);
        $reportData->push(['DIBUAT: ' . now()->format('d F Y H:i:s'), '', '', '']);
        $reportData->push([]);
        $reportData->push(['Dibuat oleh Sistem Helpdesk', '', '', '']);
        $reportData->push([]);
        $reportData->push([]);

        // 1. RINGKASAN EKSEKUTIF
        $reportData->push(['A. RINGKASAN EKSEKUTIF', '', '', '']);
        $reportData->push(['Total Tiket', $this->totalTickets, '', '']);
        $reportData->push(['Total Admin', User::whereIn('role', ['admin', 'super_admin'])->count(), '', '']);
        $reportData->push(['Tingkat Penyelesaian', $this->totalTickets > 0 ? round(($this->statusStats['closed'] / $this->totalTickets) * 100, 2) . '%' : '0%', '', '']);
        $reportData->push([]);

        // 2. STATUS TIKET
        $reportData->push(['B. STATUS TIKET', '', '', '']);
        $reportData->push(['Status', 'Jumlah', 'Persentase', '']);
        $reportData->push(['Open', $this->statusStats['open'], $this->totalTickets > 0 ? round(($this->statusStats['open'] / $this->totalTickets) * 100, 2) . '%' : '0%', '']);
        $reportData->push(['In Progress', $this->statusStats['in_progress'], $this->totalTickets > 0 ? round(($this->statusStats['in_progress'] / $this->totalTickets) * 100, 2) . '%' : '0%', '']);
        $reportData->push(['Closed', $this->statusStats['closed'], $this->totalTickets > 0 ? round(($this->statusStats['closed'] / $this->totalTickets) * 100, 2) . '%' : '0%', '']);
        $reportData->push([]);

        // 3. PRIORITAS TIKET
        $reportData->push(['C. PRIORITAS TIKET', '', '', '']);
        $reportData->push(['Prioritas', 'Jumlah', 'Persentase', '']);
        $reportData->push(['Tinggi (High)', $this->priorityStats['high'], $this->totalTickets > 0 ? round(($this->priorityStats['high'] / $this->totalTickets) * 100, 2) . '%' : '0%', '']);
        $reportData->push(['Sedang (Medium)', $this->priorityStats['medium'], $this->totalTickets > 0 ? round(($this->priorityStats['medium'] / $this->totalTickets) * 100, 2) . '%' : '0%', '']);
        $reportData->push(['Rendah (Low)', $this->priorityStats['low'], $this->totalTickets > 0 ? round(($this->priorityStats['low'] / $this->totalTickets) * 100, 2) . '%' : '0%', '']);
        $reportData->push([]);

        // 4. KATEGORI TIKET
        $reportData->push(['D. KATEGORI TIKET', '', '', '']);
        $reportData->push(['Kategori', 'Jumlah Tiket', 'Persentase', '']);
        foreach ($this->categoryStats as $category) {
            $reportData->push([
                $category->category_name,
                $category->tickets_count,
                $this->totalTickets > 0 ? round(($category->tickets_count / $this->totalTickets) * 100, 2) . '%' : '0%',
                ''
            ]);
        }
        $reportData->push([]);

        // 5. TREN BULANAN
        $reportData->push(['E. TREN BULANAN (6 BULAN TERAKHIR)', '', '', '']);
        $reportData->push(['Bulan', 'Jumlah Tiket', '', '']);
        foreach ($this->monthlyStats as $stat) {
            $reportData->push([$stat->month, $stat->total, '', '']);
        }
        $reportData->push([]);

        // 6. AKTIVITAS HARIAN
        $reportData->push(['F. AKTIVITAS HARIAN (7 HARI TERAKHIR)', '', '', '']);
        $reportData->push(['Tanggal', 'Jumlah Tiket', '', '']);
        foreach ($this->dailyStats as $stat) {
            $reportData->push([
                \Carbon\Carbon::parse($stat->date)->format('d M Y'),
                $stat->total,
                '',
                ''
            ]);
        }
        $reportData->push([]);

        // 7. KINERJA ADMIN
        $reportData->push(['G. KINERJA ADMIN', '', '', '']);
        $reportData->push(['Nama Admin', 'Jumlah Balasan', 'Role', '']);
        foreach ($this->adminStats as $admin) {
            $reportData->push([
                $admin->name,
                $admin->ticket_replies_count,
                $admin->getRoleLabelAttribute(),
                ''
            ]);
        }
        $reportData->push([]);

        // Footer
        $reportData->push(['', '', '', '']);
        $reportData->push(['Laporan ini digenerate secara otomatis oleh sistem.', '', '', '']);
        $reportData->push(['Data diambil pada: ' . now()->format('d F Y H:i:s'), '', '', '']);

        return $reportData;
    }

    public function headings(): array
    {
        return [];
    }

    public function map($row): array
    {
        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        // Styling untuk header utama
        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('A2:D2');
        $sheet->mergeCells('A3:D3');
        $sheet->mergeCells('A5:D5');

        // Header style
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '5B5EF4']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 11],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // Section headers
        $sectionRows = [8, 15, 22, 29, 36, 43, 50];
        foreach ($sectionRows as $row) {
            $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '5B5EF4']],
            ]);
        }

        // Table headers
        $tableHeaderRows = [9, 16, 23, 30, 37, 44, 51];
        foreach ($tableHeaderRows as $row) {
            $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                'font' => ['bold' => true, 'size' => 11],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8EAF6']],
            ]);
        }

        // Border untuk semua data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A8:D' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']],
            ],
        ]);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,  // Nama / Kategori
            'B' => 20,  // Jumlah
            'C' => 20,  // Persentase / Role
            'D' => 15,  // Kosong
        ];
    }

    public function title(): string
    {
        return 'Laporan Statistik Tiket';
    }
}
