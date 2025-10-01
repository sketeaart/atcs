@extends('layouts.app')

@section('content')
<div class="p-6">
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="rounded-xl bg-slate-800/60 text-white p-6 shadow-2xl">Total Gedung: <span id="total-buildings">0</span></div>
    <div class="rounded-xl bg-slate-800/60 text-white p-6 shadow-2xl">Total Room: <span id="total-rooms">0</span></div>
    <div class="rounded-xl bg-slate-800/60 text-white p-6 shadow-2xl">CCTV Online/Offline/Maint: <span id="cctv-stats">0/0/0</span></div>
  </div>
  <div class="mt-6 rounded-2xl bg-slate-800/60 h-72 shadow-2xl"></div>
  <div class="mt-6 rounded-2xl bg-slate-800/60 h-72 shadow-2xl"></div>
  <div class="mt-6 text-sm opacity-75">© Kilang Pertamina Internasional</div>
  <div class="mt-4"><a href="{{ route('export.excel', ['entity' => 'buildings']) }}" class="px-3 py-2 bg-emerald-500 rounded-lg text-slate-900">Export to Excel</a></div>
  <script>
    window.addEventListener('load', () => {
      fetch('/api/summary').then(r=>r.json()).then(d=>{
        document.getElementById('total-buildings').textContent = d.total_buildings;
        document.getElementById('total-rooms').textContent = d.total_rooms;
        document.getElementById('cctv-stats').textContent = `${d.cctv_online}/${d.cctv_offline}/${d.cctv_maintenance}`;
      }).catch(()=>{});
    });
  </script>
</div>
@endsection

