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
<header style="background:linear-gradient(135deg,#C0392B 0%,#922B21 100%);padding:0 16px;height:60px;display:flex;align-items:center;position:sticky;top:0;z-index:100;box-shadow:0 1px 0 rgba(0,0,0,0.12);">
    <div style="display:flex;align-items:center;width:100%;gap:10px;">

        {{-- Logos --}}
        <div style="display:flex;align-items:center;flex-shrink:0;">
            <div style="width:36px;height:36px;border-radius:50%;border:2px solid rgba(255,255,255,0.7);background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;overflow:hidden;">
                <span style="font-size:18px;line-height:1;">🏫</span>
            </div>
            <div style="width:36px;height:36px;border-radius:50%;border:2px solid rgba(255,255,255,0.7);background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;overflow:hidden;margin-left:-8px;">
                <span style="font-size:18px;line-height:1;">⭐</span>
            </div>
        </div>

        {{-- Brand --}}
        <div style="flex:1;min-width:0;">
            <div style="font-size:10px;color:rgba(255,255,255,0.85);font-weight:400;line-height:1.2;">SMP Negeri 1 Selemadeg</div>
            <div style="font-size:17px;font-weight:800;color:white;line-height:1.2;letter-spacing:-0.3px;">DigiTest SELSA</div>
        </div>

        {{-- User info --}}
        @auth
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
            <div style="text-align:right;">
                <div style="font-size:11px;font-weight:600;color:white;line-height:1.2;max-width:110px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    {{ auth()->user()->name }}
                </div>
                <div style="font-size:10px;color:rgba(255,255,255,0.75);line-height:1.2;">
                    {{ ucfirst(auth()->user()->role) }}
                    @if(auth()->user()->isSiswa() && auth()->user()->student?->classRoom)
                        · {{ auth()->user()->student->classRoom->name }}
                    @endif
                </div>
            </div>
            <div style="width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,0.25);border:1.5px solid rgba(255,255,255,0.5);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:white;flex-shrink:0;">
                {{ auth()->user()->initials() }}
            </div>
            <form method="POST" action="{{ route('logout') }}" style="flex-shrink:0;">
                @csrf
                <button type="submit" style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.35);color:white;font-size:11px;font-weight:600;padding:4px 10px;border-radius:6px;cursor:pointer;font-family:inherit;">
                    Keluar
                </button>
            </form>
        </div>
        @endauth
    </div>
</header>

{{-- MAIN CONTENT --}}
<div style="background:#F2F2F7;min-height:calc(100vh - 60px);max-width:480px;margin:0 auto;">
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