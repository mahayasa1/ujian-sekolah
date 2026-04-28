{{-- resources/views/components/layouts/exam.blade.php --}}
{{-- Layout khusus ujian: fullscreen tanpa sidebar, tanpa navigasi --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="theme-color" content="#C0392B">
    <title>{{ $title ?? 'Ujian' }} - DigiTest SELSA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #F3F4F6;
            min-height: 100vh;
            /* PENTING: tidak ada sidebar, tidak ada padding kiri */
        }
    </style>
</head>
<body>
    {{ $slot }}
    @livewireScripts
</body>
</html>