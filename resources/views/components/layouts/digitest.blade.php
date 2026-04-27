{{-- resources/views/components/layouts/digitest.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="theme-color" content="#C0392B">
    <title>{{ $title ?? 'DigiTest SELSA' }} - SMP Negeri 1 Selemadeg</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif; background: #F2F2F7; min-height: 100vh; }
    </style>
</head>
<body>

{{-- HEADER --}}
<header style="
    background: linear-gradient(135deg, #C0392B 0%, #7B1A10 60%, #5C0E08 100%);
    padding: 0 16px;
    height: 70px;
    display: flex;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    overflow: visible;
">
    <div style="display:flex;align-items:center;width:100%;gap:12px;height:100%;">

        {{-- Logo Kiri (Tabanan) — lebih besar, overflow ke atas --}}
        <div style="
            flex-shrink: 0;
            position: relative;
            width: 72px;
            height: 86px;
            margin-top: -16px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
        ">
            <img
                src="/images/logo_2.png"
                alt="Logo Kabupaten Tabanan"
                style="
                    width: 72px;
                    height: 72px;
                    object-fit: contain;
                    filter: drop-shadow(0 2px 6px rgba(0,0,0,0.4));
                "
            >
        </div>

        {{-- Brand — teks tengah --}}
        <div style="flex:1;min-width:0;text-align:center;">
            <div style="
                font-size: 13px;
                color: rgba(255,255,255,0.92);
                font-weight: 500;
                line-height: 1.3;
                letter-spacing: 0.2px;
                margin-bottom: 2px;
            ">SMP Negeri 1 Selemadeg</div>
            <div style="
                font-size: 22px;
                font-weight: 800;
                color: #FFFFFF;
                line-height: 1.1;
                letter-spacing: -0.3px;
                text-shadow: 0 1px 3px rgba(0,0,0,0.3);
            ">DigiTest SELSA</div>
        </div>

        {{-- Logo Kanan (SMPN1) — lebih kecil, bulat --}}
        <div style="flex-shrink:0;">
            <img
                src="/images/logo_1.png"
                alt="Logo SMP Negeri 1 Selemadeg"
                style="
                    width: 50px;
                    height: 50px;
                    border-radius: 50%;
                    object-fit: cover;
                    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
                "
            >
        </div>

        {{-- User info + logout (compact, hanya muncul saat auth) --}}
        @auth
        <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;border-left:1px solid rgba(255,255,255,0.2);padding-left:10px;">
            <div style="text-align:right;display:none;" class="user-info-desktop">
                <div style="font-size:11px;font-weight:600;color:white;line-height:1.2;max-width:90px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    {{ auth()->user()->name }}
                </div>
                <div style="font-size:10px;color:rgba(255,255,255,0.75);line-height:1.2;">
                    {{ ucfirst(auth()->user()->role) }}
                    @if(auth()->user()->isSiswa() && auth()->user()->student?->classRoom)
                        · {{ auth()->user()->student->classRoom->name }}
                    @endif
                </div>
            </div>
            <div style="
                width: 30px;
                height: 30px;
                border-radius: 50%;
                background: rgba(255,255,255,0.2);
                border: 1.5px solid rgba(255,255,255,0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 11px;
                font-weight: 700;
                color: white;
                flex-shrink: 0;
            ">{{ auth()->user()->initials() }}</div>
            <form method="POST" action="{{ route('logout') }}" style="flex-shrink:0;">
                @csrf
                <button type="submit" style="
                    background: rgba(255,255,255,0.15);
                    border: 1px solid rgba(255,255,255,0.35);
                    color: white;
                    font-size: 10px;
                    font-weight: 600;
                    padding: 4px 8px;
                    border-radius: 6px;
                    cursor: pointer;
                    font-family: inherit;
                    white-space: nowrap;
                ">Keluar</button>
            </form>
        </div>
        @endauth
    </div>
</header>

{{-- MAIN CONTENT --}}
<div style="background:#F2F2F7;min-height:calc(100vh - 70px);max-width:480px;margin:0 auto;">
    <main style="padding:16px;">

        @if(session('success'))
        <div style="background:#D4EDDA;border:0.5px solid #C3E6CB;color:#155724;padding:12px 16px;border-radius:10px;font-size:14px;margin-bottom:12px;font-weight:500;">
            ✅ {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div style="background:#F8D7DA;border:0.5px solid #F5C6CB;color:#721C24;padding:12px 16px;border-radius:10px;font-size:14px;margin-bottom:12px;font-weight:500;">
            ❌ {{ session('error') }}
        </div>
        @endif

        {{ $slot }}
    </main>
</div>

@livewireScripts
</body>
</html>