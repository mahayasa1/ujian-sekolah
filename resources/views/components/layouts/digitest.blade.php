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

        .header {
            background: linear-gradient(135deg, #C0392B 0%, #7B1A10 60%, #5C0E08 100%);
            height: 70px;
            display: flex;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        
        .header-inner {
            display: flex;
            align-items: center;
            width: 100%;
            gap: 8px;
            padding: 0 12px;
        }
        
        /* LOGO */
        .logo-left img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }
        
        .logo-right img {
            width: 48px;
            height: 48px;
            object-fit: cover;
        }
        
        /* BRAND */
        .brand {
            flex: 1;
            text-align: center;
            min-width: 0;
        }
        
        .brand-top {
            font-size: 11px;
            color: rgba(255,255,255,0.9);
            font-weight: 500;
        }
        
        .brand-main {
            font-size: 18px;
            font-weight: 800;
            color: white;
            line-height: 1.1;
        }
        
        /* USER */
        .user-area button {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.35);
            color: white;
            font-size: 10px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
        }
        
        /* ===== RESPONSIVE ===== */
        
        /* Tablet */
        @media (min-width: 640px) {
            .logo-left img {
                width: 60px;
                height: 60px;
            }
        
            .logo-right img {
                width: 58px;
                height: 58px;
            }
        
            .brand-main {
                font-size: 20px;
            }
        
            .brand-top {
                font-size: 12px;
            }
        }
        
        /* Desktop */
        @media (min-width: 1024px) {
            .header {
                height: 80px;
            }
        
            .logo-left img {
                width: 70px;
                height: 70px;
            }
        
            .logo-right img {
                width: 68px;
                height: 68px;
            }
        
            .brand-main {
                font-size: 22px;
            }
        
            .brand-top {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>

{{-- HEADER --}}
<header class="header">
    <div class="header-inner">

        {{-- Logo Kiri --}}
        <div class="logo-left">
            <img src="/images/logo_2.png" alt="Logo Kabupaten Tabanan">
        </div>

        {{-- Brand --}}
        <div class="brand">
            <div class="brand-top">SMP Negeri 1 Selemadeg</div>
            <div class="brand-main">DigiTest SELSA</div>
        </div>

        {{-- Logo Kanan --}}
        <div class="logo-right">
            <img src="/images/logo_1.png" alt="Logo SMP Negeri 1 Selemadeg">
        </div>

        {{-- User --}}
        @auth
        <div class="user-area">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Keluar</button>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>