<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balasan Baru untuk Tiket Support</title>
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

        .reply-section {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 25px;
            margin: 25px 0;
            border-radius: 12px;
        }

        .reply-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .admin-avatar {
            width: 48px;
            height: 48px;
            background: #5b5ef4;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 18px;
            box-shadow: 0 2px 8px rgba(91, 94, 244, 0.25);
        }

        .admin-info {
            flex: 1;
        }

        .admin-name {
            font-weight: 700;
            color: #1e293b;
            font-size: 16px;
        }

        .reply-date {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 4px;
        }

        .reply-message {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            margin-top: 15px;
        }

        .reply-message p {
            color: #334155;
            font-size: 14px;
            line-height: 1.7;
            white-space: pre-line;
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
            margin-bottom: 8px;
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
            background-color: #fffbeb;
            border-left: 3px solid #f59e0b;
            border-radius: 6px;
        }

        .info-title {
            font-weight: 700;
            color: #b45309;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .info-text {
            font-size: 13px;
            color: #78350f;
            line-height: 1.6;
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

            .reply-header {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="company-name">{{ config('app.name', 'Helpdesk System') }}</div>
            <h1 class="email-title">Balasan dari Tim Support</h1>
            <div class="email-subtitle">Anda mendapatkan balasan baru untuk tiket Anda</div>
        </div>

        <!-- Body -->
        <div class="email-body">
            <!-- Greeting -->
            <div class="greeting">
                Kepada {{ $ticket->client_name }},<br><br>
                Tim support kami telah memberikan balasan untuk tiket Anda. Silakan lihat balasan di bawah ini.
            </div>

            <!-- Ticket Information -->
            <div class="ticket-section">
                <div class="ticket-label">Nomor Referensi Tiket</div>
                <div class="ticket-number">{{ $ticket->ticket_code }}</div>
                <div class="ticket-title">Judul: {{ $ticket->title }}</div>
            </div>

            <!-- Reply Section -->
            <div class="reply-section">
                <div class="reply-header">
                    {{-- Avatar menggunakan getInitial() --}}
                    <div class="admin-avatar">
                        {{ $adminInitial ?? strtoupper(substr($adminName, 0, 2)) }}
                    </div>
                    <div class="admin-info">
                        <div class="admin-name">{{ $adminName }}</div>
                        <div class="reply-date">{{ $reply->created_at->translatedFormat('d F Y, H:i') }}</div>
                    </div>
                </div>
                <div class="reply-message">
                    <p>{{ $reply->message }}</p>
                </div>
            </div>

            <!-- Status Information -->
            <div class="status-info">
                <div class="status-title">Status Tiket: {{ ucfirst($ticket->status) }}</div>
                <div class="status-text">
                    @if($ticket->status === 'in_progress')
                        Tiket Anda sedang dalam penanganan. Tim support akan terus memberikan update.
                    @elseif($ticket->status === 'closed')
                        Tiket telah ditutup. Jika masih ada kendala, silakan buka tiket baru.
                    @else
                        Tim support akan segera merespons balasan Anda.
                    @endif
                </div>
            </div>

            <!-- Action Section -->
            <div class="action-section">
                <div class="action-title">Lihat Detail Tiket</div>
                <a href="{{ route('user.home') }}" class="track-button">Lihat Percakapan Lengkap</a>
            </div>

            <!-- Info Box -->
            <div class="info-box">
                <div class="info-title">Informasi Penting</div>
                <div class="info-text">
                    • Anda dapat membalas email ini untuk menambahkan komentar<br>
                    • Gunakan nomor tiket untuk referensi jika menghubungi kembali<br>
                    • Cek status tiket kapan saja melalui link di atas
                </div>
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
