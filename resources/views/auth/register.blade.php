{{-- resources/views/auth/register.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="theme-color" content="#FFFFFF">
    <title>Daftar - DigiTest SELSA</title>
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

    <div style="text-align:center;margin-bottom:28px;">
        <div style="font-size:26px;font-weight:800;color:#1C1C1E;letter-spacing:-0.5px;margin-bottom:4px;">Buat Akun</div>
        <div style="font-size:13px;color:#8E8E93;line-height:1.4;">Lengkapi data di bawah untuk<br>membuat akun baru</div>
    </div>

    @if(session('status'))
    <div style="background:#D4EDDA;border:0.5px solid #C3E6CB;color:#155724;padding:12px 16px;border-radius:10px;font-size:14px;margin-bottom:12px;">
        {{ session('status') }}
    </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        {{-- Name field --}}
        <div style="background:#F2F2F7;border:0.5px solid #E5E5EA;border-radius:10px;margin-bottom:10px;overflow:hidden;{{ $errors->has('name') ? 'border-color:#C0392B;' : '' }}">
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="Nama lengkap"
                style="width:100%;border:none;outline:none;background:transparent;padding:14px 16px;font-size:16px;color:#1C1C1E;font-family:inherit;"
            >
        </div>
        @error('name')
            <div style="color:#C0392B;font-size:12px;margin-bottom:8px;padding-left:4px;">{{ $message }}</div>
        @enderror

        {{-- Email field --}}
        <div style="background:#F2F2F7;border:0.5px solid #E5E5EA;border-radius:10px;margin-bottom:10px;overflow:hidden;{{ $errors->has('email') ? 'border-color:#C0392B;' : '' }}">
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                required
                autocomplete="email"
                placeholder="email@gmail.com"
                style="width:100%;border:none;outline:none;background:transparent;padding:14px 16px;font-size:16px;color:#1C1C1E;font-family:inherit;"
            >
        </div>
        @error('email')
            <div style="color:#C0392B;font-size:12px;margin-bottom:8px;padding-left:4px;">{{ $message }}</div>
        @enderror

        {{-- Password field --}}
        <div style="background:#F2F2F7;border:0.5px solid #E5E5EA;border-radius:10px;margin-bottom:10px;overflow:hidden;{{ $errors->has('password') ? 'border-color:#C0392B;' : '' }}">
            <input
                id="password"
                name="password"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Password"
                style="width:100%;border:none;outline:none;background:transparent;padding:14px 16px;font-size:16px;color:#1C1C1E;font-family:inherit;"
            >
        </div>
        @error('password')
            <div style="color:#C0392B;font-size:12px;margin-bottom:8px;padding-left:4px;">{{ $message }}</div>
        @enderror

        {{-- Confirm password field --}}
        <div style="background:#F2F2F7;border:0.5px solid #E5E5EA;border-radius:10px;margin-bottom:10px;overflow:hidden;">
            <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Konfirmasi password"
                style="width:100%;border:none;outline:none;background:transparent;padding:14px 16px;font-size:16px;color:#1C1C1E;font-family:inherit;"
            >
        </div>

        {{-- Show Password checkbox --}}
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:24px;">
            <input
                type="checkbox"
                id="show_pwd"
                onchange="document.getElementById('password').type = this.checked ? 'text' : 'password'; document.getElementById('password_confirmation').type = this.checked ? 'text' : 'password';"
                style="width:14px;height:14px;accent-color:#C0392B;cursor:pointer;"
            >
            <label for="show_pwd" style="font-size:13px;color:#8E8E93;cursor:pointer;user-select:none;">Show Password</label>
        </div>

        <button
            type="submit"
            style="width:100%;background:#C0392B;color:white;border:none;border-radius:10px;padding:15px;font-size:16px;font-weight:700;letter-spacing:0.5px;cursor:pointer;font-family:inherit;-webkit-appearance:none;transition:background 0.15s;"
            onmouseover="this.style.background='#A93226'"
            onmouseout="this.style.background='#C0392B'"
        >
            DAFTAR
        </button>

    </form>

    @if (Route::has('login'))
    <div style="text-align:center;margin-top:18px;font-size:13px;color:#8E8E93;">
        Sudah punya akun?
        <a href="{{ route('login') }}" style="color:#C0392B;font-weight:600;text-decoration:none;">Masuk</a>
    </div>
    @endif

</div>

@livewireScripts
</body>
</html>
