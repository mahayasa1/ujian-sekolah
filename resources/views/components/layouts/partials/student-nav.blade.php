@php
$currentRoute = request()->routeIs('student.*') ? request()->route()->getName() : '';
@endphp

<div style="margin-bottom:0.5rem;padding:0.5rem 0.75rem;font-size:0.7rem;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.08em;">
    Menu Siswa
</div>

<a href="{{ route('student.dashboard') }}" class="digi-nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>

<a href="{{ route('student.results') }}" class="digi-nav-item {{ request()->routeIs('student.results') ? 'active' : '' }}">
    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    Hasil Ujian
</a>

<a href="{{ route('profile.edit') }}" class="digi-nav-item">
    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    Profil
</a>