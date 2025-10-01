@extends('layouts.app')

@section('content')
<div class="p-6">
  <h2 class="text-xl font-bold mb-4">Live CCTV</h2>
  <div class="rounded-2xl overflow-hidden shadow-2xl bg-black aspect-video">
    <video id="video" controls autoplay muted loop class="w-full h-full"></video>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
  const video = document.getElementById('video');
  const src = @json($cctv->hls_path);
  if (Hls.isSupported()) {
    const hls = new Hls({lowLatencyMode:true});
    hls.loadSource('/'+src);
    hls.attachMedia(video);
  } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
    video.src = '/'+src;
  } else {
    video.innerHTML = '<div class="text-white p-6">Browser tidak mendukung HLS.</div>';
  }
</script>
@endsection

