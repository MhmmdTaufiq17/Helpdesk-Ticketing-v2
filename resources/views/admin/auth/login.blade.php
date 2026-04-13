<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — Admin Helpdesk</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        :root {
            --ink:        #0f0f12;
            --ink-2:      #3a3a4a;
            --ink-3:      #8a8a9a;
            --surface:    #ffffff;
            --surface-2:  #f5f5f7;
            --surface-3:  #ebebef;
            --accent:     #5b5ef4;
            --accent-2:   #7b7ef7;
            --accent-soft:#eeeeff;
            --red:        #ef4444;
            --red-soft:   #fee2e2;
            --radius:     14px;
            --radius-sm:  9px;
            --shadow:     0 4px 16px rgba(0,0,0,.09);
            --shadow-lg:  0 12px 40px rgba(0,0,0,.12), 0 4px 12px rgba(0,0,0,.06);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Sora', sans-serif;
            background: var(--surface-2);
            color: var(--ink);
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            -webkit-font-smoothing: antialiased;
            position: relative;
            overflow: hidden;
        }

        /* Background decoration */
        body::before {
            content: '';
            position: fixed;
            top: -200px; right: -200px;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(91,94,244,.12) 0%, transparent 70%);
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -200px; left: -200px;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(91,94,244,.08) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Grid pattern */
        .bg-grid {
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(91,94,244,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(91,94,244,.04) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        /* Card */
        .login-wrap {
            position: relative; z-index: 1;
            width: 100%;
            max-width: 420px;
            padding: 20px;
            animation: fadeUp .4s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .login-card {
            background: var(--surface);
            border-radius: var(--radius);
            border: 1px solid var(--surface-3);
            box-shadow: var(--shadow-lg);
            padding: 40px 36px;
        }

        /* Logo */
        .login-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
        }
        .login-logo-icon {
            width: 44px; height: 44px;
            background: var(--ink);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 14px rgba(15,15,18,.25);
        }
        .login-logo-icon svg { width: 22px; height: 22px; color: #fff; }
        .login-logo-text {
            font-size: 18px; font-weight: 800;
            color: var(--ink); letter-spacing: -.5px;
        }
        .login-logo-sub {
            font-size: 11px; font-weight: 600;
            color: var(--ink-3);
            letter-spacing: .3px;
        }

        /* Heading */
        .login-title {
            font-size: 21px; font-weight: 800;
            color: var(--ink); letter-spacing: -.5px;
            margin: 0 0 6px;
        }
        .login-subtitle {
            font-size: 13px; color: var(--ink-3);
            margin: 0 0 28px;
        }

        /* Form */
        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block;
            font-size: 12.5px; font-weight: 600; color: var(--ink-2);
            margin-bottom: 7px;
        }
        .form-label .req { color: var(--red); margin-left: 2px; }

        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            color: var(--ink-3);
            pointer-events: none;
        }
        .input-icon svg { width: 16px; height: 16px; display: block; }

        .form-control {
            width: 100%;
            border: 1.5px solid var(--surface-3);
            border-radius: var(--radius-sm);
            padding: 10px 12px 10px 40px;
            font-family: 'Sora', sans-serif;
            font-size: 13.5px; color: var(--ink);
            background: var(--surface);
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(91,94,244,.1);
        }
        .form-control::placeholder { color: var(--ink-3); }
        .form-control.is-invalid {
            border-color: var(--red);
        }
        .form-control.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(239,68,68,.1);
        }

        /* Password toggle */
        .input-toggle {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: var(--ink-3); padding: 2px;
            transition: color .15s;
        }
        .input-toggle:hover { color: var(--ink-2); }
        .input-toggle svg { width: 16px; height: 16px; display: block; }

        .form-control.has-toggle { padding-right: 40px; }

        .invalid-feedback {
            font-size: 11.5px; color: var(--red);
            margin-top: 5px; display: block;
        }

        /* Remember + Forgot */
        .form-extras {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 20px;
        }
        .remember-label {
            display: flex; align-items: center; gap: 7px;
            font-size: 12.5px; color: var(--ink-2);
            cursor: pointer; user-select: none;
        }
        .remember-label input[type="checkbox"] {
            width: 15px; height: 15px;
            border: 1.5px solid var(--surface-3);
            border-radius: 4px;
            cursor: pointer;
            accent-color: var(--accent);
        }
        .forgot-link {
            font-size: 12.5px; font-weight: 600;
            color: var(--accent); text-decoration: none;
            transition: opacity .15s;
        }
        .forgot-link:hover { opacity: .75; }

        /* Submit button */
        .btn-login {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%;
            background: var(--ink);
            color: #fff;
            border: none; border-radius: var(--radius-sm);
            padding: 12px 20px;
            font-family: 'Sora', sans-serif;
            font-size: 14px; font-weight: 700;
            cursor: pointer;
            transition: all .2s;
            letter-spacing: -.1px;
        }
        .btn-login:hover {
            background: #1e1e2a;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(15,15,18,.2);
        }
        .btn-login:active { transform: translateY(0); }
        .btn-login svg { width: 16px; height: 16px; }

        /* Divider */
        .login-divider {
            display: flex; align-items: center; gap: 12px;
            margin: 22px 0;
        }
        .login-divider-line {
            flex: 1; height: 1px; background: var(--surface-3);
        }
        .login-divider-text {
            font-size: 11.5px; color: var(--ink-3); font-weight: 500;
            white-space: nowrap;
        }

        /* Footer info */
        .login-footer {
            margin-top: 24px;
            text-align: center;
        }
        .login-footer-text {
            font-size: 11.5px; color: var(--ink-3);
        }
        .login-footer-text strong { color: var(--ink-2); font-weight: 600; }

        /* Security badge */
        .security-badge {
            display: flex; align-items: center; gap: 6px;
            justify-content: center;
            font-size: 11px; color: var(--ink-3);
            margin-top: 18px;
        }
        .security-badge svg { width: 12px; height: 12px; color: #22c55e; }

        @media (max-width: 480px) {
            .login-card { padding: 28px 22px; }
        }
    </style>
</head>
<body>
<div class="bg-grid"></div>

<div class="login-wrap">
    <div class="login-card">

        {{-- Logo --}}
        <div class="login-logo">
            <div class="login-logo-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
            <div>
                <div class="login-logo-text">Helpdesk</div>
                <div class="login-logo-sub">ADMIN PANEL</div>
            </div>
        </div>

        {{-- Heading --}}
        <h1 class="login-title">Selamat datang kembali</h1>
        <p class="login-subtitle">Masuk ke panel admin untuk mengelola tiket.</p>

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.login') }}" id="loginForm">
            @csrf

            {{-- Email --}}
            <div class="form-group">
                <label class="form-label" for="email">
                    Email <span class="req">*</span>
                </label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                        value="{{ old('email') }}"
                        placeholder="admin@helpdesk.com"
                        autofocus
                        autocomplete="email"
                        required
                    >
                </div>
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label class="form-label" for="password">
                    Password <span class="req">*</span>
                </label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </span>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="form-control has-toggle {{ $errors->has('password') ? 'is-invalid' : '' }}"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                    >
                    <button type="button" class="input-toggle" id="togglePassword" aria-label="Tampilkan password">
                        <svg id="eyeIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Remember + Forgot --}}
            <div class="form-extras">
                <label class="remember-label">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Ingat saya
                </label>
                <a href="#" class="forgot-link">Lupa password?</a>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-login">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Masuk ke Panel Admin
            </button>

        </form>

        {{-- Security badge --}}
        <div class="security-badge">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Koneksi terenkripsi & aman
        </div>

    </div>

    {{-- Footer --}}
    <div class="login-footer">
        <p class="login-footer-text">
            &copy; {{ date('Y') }} <strong>Helpdesk Admin</strong> — Hak cipta dilindungi.
        </p>
    </div>
</div>

<script>
    // Toggle password visibility
    var toggleBtn = document.getElementById('togglePassword');
    var passInput = document.getElementById('password');
    var eyeIcon   = document.getElementById('eyeIcon');

    var eyeOpen = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;

    var eyeClosed = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`;

    toggleBtn.addEventListener('click', function() {
        var isPass = passInput.type === 'password';
        passInput.type = isPass ? 'text' : 'password';
        eyeIcon.innerHTML = isPass ? eyeClosed : eyeOpen;
    });

    // SweetAlert untuk menampilkan pesan
    document.addEventListener('DOMContentLoaded', function() {
        // Pesan session timeout
        @if(session('session_timeout'))
            Toast.fire({
                icon: 'warning',
                title: 'Sesi Berakhir',
                text: 'Anda telah logout karena tidak ada aktivitas selama 5 menit.',
                timer: 5000
            });
        @endif

        // Pesan error login
        @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: '{{ session('error') }}',
                timer: 4000
            });
        @endif

        // Pesan error validasi form
        @if($errors->any())
            Toast.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: '{{ $errors->first() }}',
                timer: 4000
            });
        @endif

        // Pesan success (jika ada)
        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                timer: 3000
            });
        @endif
    });

    // NProgress
    document.querySelector('form').addEventListener('submit', function() {
        NProgress.start();
    });
</script>
</body>
</html>
