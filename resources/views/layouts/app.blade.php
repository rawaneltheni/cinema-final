<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Cinema Reservation')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-black text-neutral-100 antialiased">
        <div class="fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(229,9,20,0.34),_transparent_30%),linear-gradient(135deg,_#000_0%,_#141414_42%,_#050505_100%)]"></div>
            <div class="absolute inset-x-0 top-0 h-32 bg-gradient-to-b from-[#E50914]/25 to-transparent"></div>
            <div class="absolute left-1/2 top-0 h-full w-px bg-red-600/20"></div>
            <div class="absolute -right-28 top-16 h-72 w-72 rounded-full bg-[#E50914]/30 blur-3xl"></div>
            <div class="absolute bottom-10 left-8 h-56 w-56 rounded-full bg-red-950/40 blur-3xl"></div>
        </div>

        <main class="mx-auto flex min-h-screen w-full max-w-7xl flex-col px-5 py-6 sm:px-8">
            @yield('content')
        </main>
    </body>
</html>
