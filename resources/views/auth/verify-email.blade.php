{{-- resources/views/auth/verify-email.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="theme-color" content="#FFFFFF">
    <title>Verifikasi Email - DigiTest SELSA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    @livewireStyles
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #FFFFFF;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            -webkit-font-smoothing: antialiased;
        }
    </style>
</head>
<body>

<div style="width:100%;max-width:360px;">

    <div style="display:flex;align-items:center;justify-content:center;gap:16px;margin-bottom:20px;">
        <img src="/images/logo_2.png" style="width:72px;height:72px;">
        <img src="/images/logo_1.png"  style="width:72px;height:72px;">
    </div>

    <div style="text-align:center;margin-bottom:24px;">
        <div style="font-size:26px;font-weight:800;color:#1C1C1E;letter-spacing:-0.5px;margin-bottom:4px;">Verifikasi Email</div>
        <div style="font-size:13px;color:#8E8E93;line-height:1.5;">
            Silakan verifikasi alamat email kamu dengan mengklik link yang baru saja kami kirim ke email kamu.
        </div>
    </div>

    @if (session('status') == 'verification-link-sent')
    <div style="background:#D4EDDA;border:0.5px solid #C3E6CB;color:#155724;padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:16px;text-align:center;">
        Link verifikasi baru sudah dikirim ke alamat email yang kamu daftarkan.
    </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" style="margin-bottom:10px;">
        @csrf
        <button
            type="submit"
            style="width:100%;background:#C0392B;color:white;border:none;border-radius:10px;padding:15px;font-size:16px;font-weight:700;letter-spacing:0.5px;cursor:pointer;font-family:inherit;-webkit-appearance:none;transition:background 0.15s;"
            onmouseover="this.style.background='#A93226'"
            onmouseout="this.style.background='#C0392B'"
        >
            KIRIM ULANG EMAIL VERIFIKASI
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button
            type="submit"
            style="width:100%;background:transparent;color:#8E8E93;border:0.5px solid #E5E5EA;border-radius:10px;padding:14px;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;-webkit-appearance:none;"
        >
            LOGOUT
        </button>
    </form>

</div>

@livewireScripts
</body>
</html>
