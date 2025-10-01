@extends('layouts.app')

@section('content')
<div class="p-6 max-w-md mx-auto">
  <h2 class="text-xl font-bold mb-4">Verifikasi OTP</h2>
  @if(session('status'))
    <div class="mb-3 text-emerald-400">{{ session('status') }}</div>
  @endif
  <form method="POST" action="{{ route('otp.verify') }}" class="space-y-3">
    @csrf
    <input name="code" maxlength="6" class="w-full px-3 py-2 rounded bg-slate-800/60 text-white" placeholder="Masukkan 6 digit OTP" />
    @error('code')<div class="text-red-400 text-sm">{{ $message }}</div>@enderror
    <div class="flex items-center gap-2">
      <button class="px-4 py-2 rounded bg-emerald-500 text-slate-900 font-semibold">Verifikasi</button>
      <a href="{{ route('otp.send') }}" class="px-4 py-2 rounded bg-slate-700">Kirim Ulang</a>
    </div>
  </form>
</div>
@endsection

