<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Perubahan Password</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f8f9fa;
            padding: 20px;
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
        }
        .email-title {
            font-size: 24px;
            font-weight: 600;
            margin: 12px 0 6px 0;
        }
        .email-body { padding: 35px 30px; }
        .greeting { font-size: 15px; color: #334155; margin-bottom: 24px; }
        .info-box {
            background: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .warning-box {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 16px;
            margin: 20px 0;
            border-radius: 8px;
            font-size: 13px;
            color: #b45309;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #5b5ef4 0%, #4a4de0 100%);
            color: white;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            margin: 20px 0;
            box-shadow: 0 2px 8px rgba(91, 94, 244, 0.25);
        }
        .button:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(91, 94, 244, 0.35); }
        .closing { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0; }
        .signature { margin-top: 16px; font-weight: 600; color: #1e293b; }
        .email-footer {
            background-color: #f8fafc;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <div class="company-name">{{ config('app.name', 'Helpdesk System') }}</div>
            <h1 class="email-title">Konfirmasi Perubahan Password</h1>
        </div>

        <div class="email-body">
            <div class="greeting">
                Kepada {{ $user->name }},<br><br>
                Kami menerima permintaan untuk mengubah password akun Anda.
            </div>

            <div class="warning-box">
                <strong>Perhatian:</strong> Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini. Password Anda tidak akan berubah.
            </div>

            <div style="text-align: center;">
                <a href="{{ route('admin.profile.confirm-password-reset', ['token' => $token]) }}" class="button">
                    Konfirmasi Perubahan Password
                </a>
            </div>

            <div class="closing">
                <div class="closing-text">
                    Jika tombol di atas tidak berfungsi, salin dan tempel link berikut ke browser Anda:<br>
                    <small style="color: #5b5ef4; word-break: break-all;">
                        {{ route('admin.profile.confirm-password-reset', ['token' => $token]) }}
                    </small>
                </div>
                <div class="signature">
                    Tim Support {{ config('app.name', 'Helpdesk System') }}
                </div>
            </div>
        </div>

        <div class="email-footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'Helpdesk System') }}. Email ini dikirim secara otomatis.
        </div>
    </div>
</body>
</html>
