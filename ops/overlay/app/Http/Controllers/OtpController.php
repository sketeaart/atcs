<?php

namespace App\Http\Controllers;

use App\Mail\OtpCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    public function show()
    {
        return view('auth.otp');
    }

    public function send(Request $request)
    {
        $user = $request->user();
        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->forceFill([
            'otp_code' => $code,
            'otp_expires_at' => now()->addMinutes(10),
            'otp_attempts' => 0,
        ])->save();
        Mail::to($user->email)->send(new OtpCodeMail($code));
        return back()->with('status', 'OTP dikirim ke email.');
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);
        $user = $request->user();
        if (!$user->otp_code || now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['code' => 'OTP kedaluwarsa. Kirim ulang.']);
        }
        if (hash_equals($user->otp_code, $request->string('code'))) {
            $user->forceFill(['otp_verified_at' => now()])->save();
            return redirect()->intended('/dashboard');
        }
        $user->increment('otp_attempts');
        return back()->withErrors(['code' => 'Kode OTP salah.']);
    }
}

