<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Mail\AdminOtpMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $admin = Admin::where('email', $request->email)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            $otp = Str::random(6);
            $admin->update(['otp' => $otp]);

            Mail::to($admin->email)->send(new AdminOtpMail($otp));

            return redirect()->route('admin.otp');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function showOtpForm()
    {
        return view('admin.auth.otp');
    }

    public function verifyOtp(Request $request)
    {
        $admin = Admin::where('email', session('admin_email'))
                     ->where('otp', $request->otp)
                     ->first();

        if ($admin) {
            Auth::guard('admin')->login($admin);
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['otp' => 'Invalid OTP']);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
