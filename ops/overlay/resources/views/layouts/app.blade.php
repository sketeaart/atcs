<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name', 'ATCS') }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <meta name="color-scheme" content="dark light">
</head>
<body class="min-h-screen bg-slate-900 text-white">
  <div class="flex min-h-screen">
    <aside class="w-64 bg-slate-800/60 backdrop-blur border-r border-white/10 hidden md:block">
      <div class="p-4 font-bold">ATCS</div>
      <nav class="flex flex-col">
        <a class="px-4 py-2 hover:bg-white/5" href="/dashboard">Dashboard</a>
        <a class="px-4 py-2 hover:bg-white/5" href="/maps">Maps</a>
        <a class="px-4 py-2 hover:bg-white/5" href="/location">Location</a>
        <a class="px-4 py-2 hover:bg-white/5" href="/contact">Contact</a>
        <a class="px-4 py-2 hover:bg-white/5" href="/notifications">Notification</a>
        <a class="px-4 py-2 hover:bg-white/5" href="/messages">Message</a>
      </nav>
    </aside>
    <main class="flex-1">
      @yield('content')
    </main>
  </div>
  <footer class="text-center text-sm opacity-75 py-3">© Kilang Pertamina Internasional</footer>
</body>
</html>

