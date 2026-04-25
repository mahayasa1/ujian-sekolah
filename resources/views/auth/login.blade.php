{{-- resources/views/pages/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - DigiTest SELSA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    @livewireStyles
</head>
<body style="font-family:'Plus Jakarta Sans',sans-serif;margin:0;">

<div class="digi-login-wrap">
    <div class="digi-login-card">
        {{-- Header --}}
        <div class="digi-login-header">
            <div class="digi-login-logo">
                <div style="width:52px;height:52px;border-radius:50%;background:rgba(255,255,255,0.25);display:flex;align-items:center;justify-content:center;font-size:1.75rem;">🏫</div>
                <div style="width:52px;height:52px;border-radius:50%;background:rgba(255,255,255,0.25);display:flex;align-items:center;justify-content:center;font-size:1.75rem;">⭐</div>
            </div>
            <h1 style="margin:0;font-size:1.5rem;font-weight:800;letter-spacing:1px;">DigiTest SELSA</h1>
            <p style="margin:0.25rem 0 0;font-size:0.75rem;opacity:0.8;">Precision in Assessment, Simplicity in Execution</p>
            <p style="margin:0.35rem 0 0;font-size:0.7rem;opacity:0.65;">SMP Negeri 1 Selemadeg</p>
        </div>

        {{-- Form --}}
        <div class="digi-login-body">
            <x-auth-session-status class="text-center" :status="session('status')" style="margin-bottom:1rem;" />

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <div style="margin-bottom:1rem;">
                    <label class="digi-label" for="email">Email / Username</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        class="digi-input"
                        placeholder="email@sekolah.sch.id"
                    >
                    @error('email')
                        <p style="color:#C0392B;font-size:0.75rem;margin-top:0.35rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom:1.25rem;">
                    <label class="digi-label" for="password">Password</label>
                    <div style="position:relative;">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            class="digi-input"
                            placeholder="••••••••"
                            style="padding-right:2.5rem;"
                        >
                        <button type="button" onclick="togglePwd()" style="position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9CA3AF;">
                            <svg id="eye-icon" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <p style="color:#C0392B;font-size:0.75rem;margin-top:0.35rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
                    <label style="display:flex;align-items:center;gap:0.4rem;font-size:0.8rem;color:#4B5563;cursor:pointer;">
                        <input type="checkbox" name="remember" style="accent-color:#C0392B;">
                        Ingat saya
                    </label>
                    @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" style="font-size:0.8rem;color:#C0392B;text-decoration:none;font-weight:500;">Lupa password?</a>
                    @endif
                </div>

                <button type="submit" class="btn-digi-primary" style="width:100%;justify-content:center;padding:0.85rem;font-size:0.95rem;">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="margin-right:0.5rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    MASUK
                </button>
            </form>

            <div style="text-align:center;margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid #E5E7EB;">
                <p style="font-size:0.75rem;color:#9CA3AF;margin:0;">
                    Sistem Ujian Digital &copy; {{ date('Y') }} SMP Negeri 1 Selemadeg
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function togglePwd() {
    const input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
@livewireScripts
</body>
</html>
