<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Tiket Support</title>
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

        .ticket-date {
            color: #94a3b8;
            font-size: 13px;
            margin-top: 8px;
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
            border: none;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(91, 94, 244, 0.25);
        }

        .track-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(91, 94, 244, 0.35);
        }

        .instructions {
            margin: 25px 0;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
        }

        .instructions-title {
            color: #1e293b;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 14px;
        }

        .instructions-list {
            color: #475569;
            font-size: 14px;
            line-height: 1.8;
            padding-left: 20px;
        }

        .instructions-list li {
            margin-bottom: 8px;
        }

        .security-note {
            background-color: #fffbeb;
            border-left: 3px solid #f59e0b;
            padding: 14px 16px;
            margin: 20px 0;
            font-size: 12px;
            color: #b45309;
            border-radius: 6px;
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
            <h1 class="email-title">Konfirmasi Pembuatan Tiket</h1>
            <div class="email-subtitle">Tiket support Anda telah berhasil direkam</div>
        </div>

        <!-- Body -->
        <div class="email-body">
            <!-- Greeting -->
            <div class="greeting">
                Kepada {{ $ticket->client_name }},<br><br>
                Terima kasih telah menghubungi tim support kami. Tiket Anda telah berhasil dibuat dan akan segera diproses.
            </div>

            <!-- Ticket Information -->
            <div class="ticket-section">
                <div class="ticket-label">Nomor Referensi Tiket</div>
                <div class="ticket-number">{{ $ticket->ticket_code }}</div>
                <div class="ticket-date">Dibuat: {{ $ticket->created_at->translatedFormat('d F Y, H:i') }}</div>
            </div>

            <!-- Status Information -->
            <div class="status-info">
                <div class="status-title">Status Tiket</div>
                <div class="status-text">
                    Tiket Anda saat ini dalam status <strong>"{{ ucfirst($ticket->status) }}"</strong> dan sedang dalam antrian untuk ditinjau oleh tim technical support kami. Anda akan menerima update via email ketika ada perkembangan.
                </div>
            </div>

            <!-- Action Section -->
            <div class="action-section">
                <div class="action-title">Lacak Perkembangan Tiket</div>
                <a href="{{ route('user.tickets.track') }}" class="track-button">Lacak Status Tiket</a>
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <div class="instructions-title">Informasi Penting</div>
                <ul class="instructions-list">
                    <li>Simpan nomor tiket di atas untuk referensi</li>
                    <li>Gunakan nomor tiket untuk melacak status</li>
                    <li>Update akan dikirimkan ke email ini</li>
                    <li>Perkiraan waktu respon: 1-2 hari kerja</li>
                </ul>
            </div>

            <!-- Security Note -->
            <div class="security-note">
                <strong>Catatan Keamanan:</strong> Untuk melindungi privasi Anda, jangan membagikan nomor tiket ini kepada pihak lain.
            </div>

            <!-- Closing -->
            <div class="closing">
                <div class="closing-text">
                    Terima kasih atas kesabaran Anda.
                </div>
                <div class="signature">
                    Tim Support {{ config('app.name', 'Helpdesk System') }}
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-contact">
                <div class="contact-label">Untuk pertanyaan lebih lanjut</div>
                <div class="contact-info">support@helpdesk.com</div>
            </div>
            <div class="copyright">
                &copy; {{ date('Y') }} {{ config('app.name', 'Helpdesk System') }}. Email ini dikirim secara otomatis.
            </div>
        </div>
    </div>
</body>
</html>
