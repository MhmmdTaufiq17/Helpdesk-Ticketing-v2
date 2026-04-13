<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Statistik Tiket</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #5b5ef4;
            padding-bottom: 10px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #5b5ef4;
        }
        .subtitle {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            background-color: #5b5ef4;
            color: white;
            padding: 5px 10px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #999;
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">LAPORAN STATISTIK TIKET</div>
        <div class="subtitle">Dicetak: {{ now()->format('d F Y H:i:s') }}</div>
    </div>

    <div class="section">
        <div class="section-title">A. RINGKASAN TOTAL</div>
        <table>
            <tr>
                <th>Total Tiket</th>
                <td>{{ $totalTickets }}</td>
            </tr>
            <tr>
                <th>Total Admin</th>
                <td>{{ $totalAdmins }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">B. STATUS TIKET</div>
        <table>
            <tr>
                <th>Status</th>
                <th>Jumlah</th>
                <th>Persentase</th>
            </tr>
            <tr>
                <td>Open</td>
                <td>{{ $statusStats['open'] }}</td>
                <td>{{ $totalTickets > 0 ? round(($statusStats['open'] / $totalTickets) * 100, 2) : 0 }}%</td>
            </tr>
            <tr>
                <td>In Progress</td>
                <td>{{ $statusStats['in_progress'] }}</td>
                <td>{{ $totalTickets > 0 ? round(($statusStats['in_progress'] / $totalTickets) * 100, 2) : 0 }}%</td>
            </tr>
            <tr>
                <td>Closed</td>
                <td>{{ $statusStats['closed'] }}</td>
                <td>{{ $totalTickets > 0 ? round(($statusStats['closed'] / $totalTickets) * 100, 2) : 0 }}%</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">C. PRIORITAS TIKET</div>
        <table>
            <tr>
                <th>Prioritas</th>
                <th>Jumlah</th>
                <th>Persentase</th>
            </tr>
            <tr>
                <td>Tinggi</td>
                <td>{{ $priorityStats['high'] }}</td>
                <td>{{ $totalTickets > 0 ? round(($priorityStats['high'] / $totalTickets) * 100, 2) : 0 }}%</td>
            </tr>
            <tr>
                <td>Sedang</td>
                <td>{{ $priorityStats['medium'] }}</td>
                <td>{{ $totalTickets > 0 ? round(($priorityStats['medium'] / $totalTickets) * 100, 2) : 0 }}%</td>
            </tr>
            <tr>
                <td>Rendah</td>
                <td>{{ $priorityStats['low'] }}</td>
                <td>{{ $totalTickets > 0 ? round(($priorityStats['low'] / $totalTickets) * 100, 2) : 0 }}%</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">D. KATEGORI TIKET</div>
        <table>
            <tr>
                <th>Kategori</th>
                <th>Jumlah Tiket</th>
                <th>Persentase</th>
            </tr>
            @foreach($categoryStats as $category)
            <tr>
                <td>{{ $category->category_name }}</td>
                <td>{{ $category->tickets_count }}</td>
                <td>{{ $totalTickets > 0 ? round(($category->tickets_count / $totalTickets) * 100, 2) : 0 }}%</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="section">
        <div class="section-title">E. TREN BULANAN (6 BULAN TERAKHIR)</div>
        <table>
            <tr>
                <th>Bulan</th>
                <th>Jumlah Tiket</th>
            </tr>
            @foreach($monthlyStats as $stat)
            <tr>
                <td>{{ $stat->month }}</td>
                <td>{{ $stat->total }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="footer">
        Sistem Helpdesk - Laporan ini digenerate secara otomatis
    </div>
</body>
</html>
