<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Status Tiket Support</title>
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f8f9fa;
            padding: 20px;
            margin: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .email-header {
            background: linear-gradient(135deg, #5b5ef4 0%, #4a4de0 100%);
            color: white;
            padding: 32px 30px;
            text-align: center;
        }

        .company-name {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .email-title {
            font-size: 24px;
            font-weight: 600;
            margin: 12px 0 6px 0;
        }

        .email-subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: 14px;
            font-weight: 400;
        }

        .email-body {
            padding: 35px 30px;
        }

        .greeting {
            font-size: 15px;
            color: #334155;
            margin-bottom: 24px;
            line-height: 1.7;
        }

        .ticket-section {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-left: 4px solid #5b5ef4;
            padding: 24px;
            margin: 25px 0;
            border-radius: 8px;
        }

        .ticket-label {
            color: #64748b;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .ticket-number {
            font-size: 32px;
            font-weight: 700;
            color: #1e293b;
            font-family: 'Courier New', 'SF Mono', monospace;
            letter-spacing: 1px;
            margin: 8px 0;
        }

        .ticket-title {
            color: #64748b;
            font-size: 13px;
            margin-top: 8px;
        }

        .status-change {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 25px;
            margin: 25px 0;
            border-radius: 12px;
            text-align: center;
        }

        .status-label {
            color: #64748b;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            margin: 5px;
        }

        .status-old {
            background-color: #ef4444;
            color: white;
        }

        .status-new {
            background-color: #10b981;
            color: white;
        }

        .status-arrow {
            font-size: 20px;
            margin: 0 12px;
            color: #94a3b8;
            font-weight: 600;
        }

        .status-info {
            background-color: #f0fdf4;
            border: 1px solid #dcfce7;
            padding: 20px;
            margin: 25px 0;
            border-radius: 10px;
        }

        .status-title {
            color: #166534;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .status-text {
            color: #14532d;
            font-size: 14px;
            line-height: 1.6;
        }

        .note-section {
            background-color: #fffbeb;
            border-left: 3px solid #f59e0b;
            padding: 14px 16px;
            margin: 20px 0;
            font-size: 13px;
            color: #b45309;
            border-radius: 6px;
        }

        .note-title {
            font-weight: 700;
            margin-bottom: 6px;
        }

        .action-section {
            text-align: center;
            margin: 30px 0;
            padding: 25px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 12px;
        }

        .action-title {
            color: #1e293b;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 18px;
        }

        .track-button {
            display: inline-block;
            background: linear-gradient(135deg, #5b5ef4 0%, #4a4de0 100%);
            color: white;
            text-decoration: none;
            padding: 12px 32px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(91, 94, 244, 0.25);
        }

        .track-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(91, 94, 244, 0.35);
        }

        .info-box {
            margin: 25px 0;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
        }

        .info-title {
            color: #1e293b;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 14px;
        }

        .info-list {
            color: #475569;
            font-size: 14px;
            line-height: 1.8;
            padding-left: 20px;
        }

        .info-list li {
            margin-bottom: 8px;
        }

        .closing {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .closing-text {
            color: #334155;
            font-size: 14px;
            line-height: 1.7;
        }

        .signature {
            margin-top: 16px;
            font-weight: 600;
            color: #1e293b;
        }

        .email-footer {
            background-color: #f8fafc;
            color: #64748b;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }

        .footer-contact {
            margin-bottom: 12px;
        }

        .contact-label {
            color: #94a3b8;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .contact-info {
            color: #334155;
            font-size: 13px;
            font-weight: 500;
        }

        .copyright {
            color: #94a3b8;
            font-size: 11px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-body {
                padding: 24px 20px;
            }

            .ticket-number {
                font-size: 24px;
            }

            .status-badge {
                display: block;
                margin: 10px auto;
            }

            .status-arrow {
                display: block;
                margin: 10px 0;
                transform: rotate(90deg);
            }

            .track-button {
                padding: 11px 24px;
                font-size: 13px;
                width: 100%;
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="company-name">{{ config('app.name', 'Helpdesk System') }}</div>
            <h1 class="email-title">Update Status Tiket</h1>
            <div class="email-subtitle">Status tiket support Anda telah diperbarui</div>
        </div>

        <!-- Body -->
        <div class="email-body">
            <!-- Greeting -->
            <div class="greeting">
                Kepada {{ $ticket->client_name }},<br><br>
                Ada perkembangan terbaru mengenai tiket support Anda.
            </div>

            <!-- Ticket Information -->
            <div class="ticket-section">
                <div class="ticket-label">Nomor Referensi Tiket</div>
                <div class="ticket-number">{{ $ticket->ticket_code }}</div>
                <div class="ticket-title">Judul: {{ $ticket->title }}</div>
            </div>

            <!-- Status Change -->
            <div class="status-change">
                <div class="status-label">Perubahan Status</div>
                <div>
                    <span class="status-badge status-old">{{ ucfirst($oldStatus) }}</span>
                    <span class="status-arrow">→</span>
                    <span class="status-badge status-new">{{ ucfirst($newStatus) }}</span>
                </div>
            </div>

            <!-- Status Information -->
            <div class="status-info">
                <div class="status-title">Informasi Status Baru</div>
                <div class="status-text">
                    @php
                        $statusMessages = [
                            'open' => 'Tiket Anda telah diterima dan sedang menunggu untuk diproses oleh tim support. Kami akan segera menindaklanjuti laporan Anda.',
                            'in_progress' => 'Tiket Anda sedang dalam penanganan oleh tim technical support. Tim kami sedang menganalisis dan mengerjakan solusi untuk permasalahan Anda.',
                            'closed' => 'Tiket Anda telah diselesaikan. Jika Anda masih mengalami kendala atau memiliki pertanyaan lebih lanjut, silakan buka tiket baru atau balas email ini.'
                        ];
                    @endphp
                    {{ $statusMessages[$newStatus] ?? 'Status tiket Anda telah diperbarui.' }}
                </div>
            </div>

            <!-- Note if any -->
            @if(!empty($note))
            <div class="note-section">
                <div class="note-title">Catatan dari Tim Support</div>
                {{ $note }}
            </div>
            @endif

            <!-- Next Steps -->
            @if($newStatus === 'in_progress')
            <div class="info-box">
                <div class="info-title">Langkah Selanjutnya</div>
                <ul class="info-list">
                    <li>Tim support sedang menganalisis masalah Anda</li>
                    <li>Anda akan menerima update berikutnya melalui email</li>
                    <li>Jika diperlukan informasi tambahan, tim support akan menghubungi Anda</li>
                </ul>
            </div>
            @elseif($newStatus === 'closed')
            <div class="info-box">
                <div class="info-title">Apakah Masalah Anda Terselesaikan?</div>
                <ul class="info-list">
                    <li>Jika masalah masih berlanjut, Anda dapat membuka tiket baru</li>
                    <li>Referensikan tiket ini untuk mempercepat proses</li>
                    <li>Terima kasih telah menggunakan layanan support kami</li>
                </ul>
            </div>
            @endif

            <!-- Action Section -->
            <div class="action-section">
                <div class="action-title">Lacak Perkembangan Tiket</div>
                <a href="{{ route('user.tickets.track') }}" class="track-button">Lihat Detail Tiket</a>
            </div>

            <!-- Closing -->
            <div class="closing">
                <div class="closing-text">
                    Terima kasih atas kepercayaan Anda.
                </div>
                <div class="signature">
                    Tim Support {{ config('app.name', 'Helpdesk System') }}
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-contact">
                <div class="contact-label">Butuh bantuan lebih lanjut?</div>
                <div class="contact-info">support@helpdesk.com</div>
            </div>
            <div class="copyright">
                &copy; {{ date('Y') }} {{ config('app.name', 'Helpdesk System') }}. Email ini dikirim secara otomatis.
            </div>
        </div>
    </div>
</body>
</html>
