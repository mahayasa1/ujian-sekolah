{{-- resources/views/layouts/digitest.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'DigiTest SELSA' }} - SMP Negeri 1 Selemadeg</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; }
        body { margin: 0; background: #F3F4F6; }
    </style>
</head>
<body>

{{-- HEADER --}}
<header class="digi-header" style="height:64px; display:flex; align-items:center; padding:0 1.5rem; position:sticky; top:0; z-index:50;">
    <div class="digi-header-brand" style="flex:1;">
        {{-- Logo sekolah (placeholder) --}}
        <div style="width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-size:1.25rem;">🏫</div>
        <div style="width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-size:1.25rem;margin-left:-12px;">⭐</div>
        <div style="margin-left:0.5rem;">
            <div style="font-size:0.65rem;color:rgba(255,255,255,0.8);">SMP Negeri 1 Selemadeg</div>
            <div style="font-size:1.1rem;font-weight:800;color:white;letter-spacing:1px;">DigiTest SELSA</div>
            <div style="font-size:0.6rem;color:rgba(255,255,255,0.7);">Precision in Assessment, Simplicity in Execution</div>
        </div>
    </div>

    {{-- User info --}}
    @auth
    <div style="display:flex;align-items:center;gap:0.75rem;">
        <div style="text-align:right;">
            <div style="font-size:0.8rem;font-weight:600;color:white;">{{ auth()->user()->name }}</div>
            <div style="font-size:0.7rem;color:rgba(255,255,255,0.7);">
                {{ ucfirst(auth()->user()->role) }}
                @if(auth()->user()->isSiswa() && auth()->user()->student?->classRoom)
                    — {{ auth()->user()->student->classRoom->name }}
                @endif
            </div>
        </div>
        <div style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-weight:700;color:white;font-size:0.85rem;">
            {{ auth()->user()->initials() }}
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.3);color:white;padding:0.35rem 0.85rem;border-radius:0.4rem;font-size:0.75rem;font-weight:600;cursor:pointer;">
                Keluar
            </button>
        </form>
    </div>
    @endauth
</header>

{{-- MAIN CONTENT --}}
<div style="display:flex;min-height:calc(100vh - 64px);">

    {{-- SIDEBAR (shown only for non-exam pages) --}}
    @if(!($hideSidebar ?? false))
    <aside class="digi-sidebar" style="width:220px;padding:1rem;flex-shrink:0;">
        @auth
        @if(auth()->user()->isAdmin())
            @include('layouts.partials.admin-nav')
        @elseif(auth()->user()->isGuru())
            @include('layouts.partials.teacher-nav')
        @else
            @include('layouts.partials.student-nav')
        @endif
        @endauth
    </aside>
    @endif

    {{-- PAGE CONTENT --}}
    <main style="flex:1;padding:1.5rem;overflow-x:hidden;">
        @if(session('success'))
        <div style="background:#D5F5E3;border:1px solid #27AE60;color:#1E8449;padding:0.75rem 1.25rem;border-radius:0.5rem;margin-bottom:1rem;font-size:0.875rem;font-weight:500;">
            ✅ {{ session('success') }}
        </div>
        @endif
        {{ $slot }}
    </main>
</div>

@livewireScripts
</body>
</html>
