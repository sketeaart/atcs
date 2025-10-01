@extends('layouts.app')

@section('content')
<div class="p-6">
  <div class="flex items-center gap-3 mb-4">
    <input id="search" placeholder="Cari gedung..." class="px-3 py-2 rounded-lg bg-slate-800/60 text-white w-64" />
    <div class="flex items-center gap-2">
      <label class="flex items-center gap-2 text-white"><input type="checkbox" id="f-online" checked> 🟢 Online</label>
      <label class="flex items-center gap-2 text-white"><input type="checkbox" id="f-offline" checked> 🔴 Offline</label>
      <label class="flex items-center gap-2 text-white"><input type="checkbox" id="f-maint" checked> 🟡 Maintenance</label>
    </div>
  </div>
  <div id="map" class="rounded-2xl overflow-hidden shadow-2xl" style="height: 72vh;"></div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  const map = L.map('map').setView([-6.4119, 108.4215], 15); // RU VI Balongan approx
  const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
  const sat = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', { subdomains:['mt0','mt1','mt2','mt3'] });
  L.control.layers({ 'OpenStreetMap': osm, 'Satellite': sat }, {}).addTo(map);

  let markers = [];
  function colorFor(status){ return status==='online'?'#22c55e':(status==='maintenance'?'#eab308':'#ef4444'); }
  function drawMarker(item){
    const icon = L.divIcon({className:'', html:`<div style=\"transform:translate(-50%,-100%);\"><div style=\"width:18px;height:18px;border-radius:9999px;background:${colorFor(item.status)};box-shadow:0 6px 18px rgba(0,0,0,.35)\"></div></div>`});
    const m = L.marker([item.lat||-6.4119, item.lng||108.4215], {icon}).addTo(map);
    m.bindPopup(`<div class='text-sm'>${item.name||'Room'}<br/><button onclick=\"window.location='/stream/${item.id}'\" class='mt-2 px-3 py-1 rounded bg-emerald-500 text-slate-900'>Live CCTV</button></div>`);
    markers.push(m);
  }
  fetch('/api/rooms-with-cctv').then(r=>r.json()).then(list=>{ list.forEach(drawMarker); }).catch(()=>{});

  document.getElementById('search').addEventListener('input', e=>{
    const q=e.target.value.toLowerCase();
    markers.forEach(m=>{ const c=m.getPopup().getContent().toLowerCase(); if(q && !c.includes(q)) m.removeFrom(map); else if(!m._map) m.addTo(map); });
  });
</script>
@endsection

