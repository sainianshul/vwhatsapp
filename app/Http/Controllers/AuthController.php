<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect('dashboard');
        }
        return view('auth.login');
    }

    public function customLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // Update last_login_at
            $user = Auth::user();
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip()
            ]);

            return redirect()->intended('dashboard')
                ->withSuccess('Signed in successfully');
        }

        return redirect("login")->with('error', 'Login details are not valid');
    }



    public function dashboard()
    {
        if (Auth::check()) {
            $stats = [
                'accounts' => \App\Models\WhatsAppAccount::where('user_id', auth()->id())->count(),
                'connected' => \App\Models\WhatsAppAccount::where('user_id', auth()->id())->where('status', 'connected')->count(),
                'messages_sent' => \App\Models\WhatsAppMessage::where('user_id', auth()->id())->where('status', 'sent')->count(),
                'campaigns' => \App\Models\BulkCampaign::where('user_id', auth()->id())->count(),
                'users' => \App\Models\User::count(),
            ];
            return view('dashboard', compact('stats'));
        }

        return redirect("login")->with('error', 'Opps! You do not have access');
    }

    public function signOut(Request $request)
    {
        Session::flush();
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('login');
    }
}
