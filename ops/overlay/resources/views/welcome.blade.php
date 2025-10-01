<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ATCS PEMANTAUAN CCTV</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        .text-3d { text-shadow: 0 10px 25px rgba(0,0,0,0.35), 0 2px 4px rgba(0,0,0,0.25); }
        .glass { backdrop-filter: blur(10px); background: rgba(15,23,42,0.45); }
    </style>
    <link rel="icon" href="/images/logo-pertamina.png">
    <meta name="color-scheme" content="dark light">
</head>
<body class="min-h-screen bg-slate-900 text-white">
    <div class="absolute inset-0 -z-10 bg-cover bg-center" style="background-image:url('/images/kilang.png'); filter: brightness(0.55);"></div>

    <header class="absolute top-0 left-0 right-0 p-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="/images/logo-pertamina.png" alt="Pertamina" class="h-12 w-12 drop-shadow-xl">
            <div class="text-sm opacity-80">ATCS PEMANTAUAN CCTV</div>
        </div>
        <nav class="flex items-center gap-3">
            <a href="/login" class="px-4 py-2 rounded-lg border border-white/20 hover:border-white/40 transition glass">Login</a>
            <a href="/register" class="px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-semibold transition shadow-lg">Register</a>
        </nav>
    </header>

    <main class="min-h-screen flex items-center justify-center p-6">
        <div class="text-center max-w-5xl mx-auto">
            <div class="inline-flex items-center justify-center rounded-3xl p-8 md:p-12 glass ring-1 ring-white/10 shadow-2xl">
                <div>
                    <div class="mb-8 flex justify-center">
                        <img src="/images/logo-pertamina.png" alt="Pertamina" class="h-20 w-20 drop-shadow-2xl">
                    </div>
                    <h1 class="text-3xl md:text-5xl font-extrabold tracking-wide text-3d leading-tight">
                        <span class="block">PEMANTAUAN CCTV AREA</span>
                        <span class="block">PT KILANG PERTAMINA INTERNASIONAL</span>
                        <span class="block">REFINERY UNIT VI BALONGAN</span>
                    </h1>
                    <p class="mt-6 opacity-80">© Kilang Pertamina Internasional</p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>

