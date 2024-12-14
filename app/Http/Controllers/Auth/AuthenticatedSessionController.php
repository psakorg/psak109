<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    // Ambil user yang login
    $loggedInUser = $request->user();

    // Cek aktivasi terlebih dahulu
    if ($loggedInUser->is_activated == false) {
        Auth::logout(); // Logout user jika belum diaktivasi
        return redirect()->route('login')
            ->with('error', 'Akun Anda belum diaktifkan. Silakan hubungi admin untuk aktivasi akun Anda.');
    }

    // Jika sudah aktivasi, ambil id_pt
    $id_pt = $loggedInUser->pt;
    if (is_null($id_pt)) {
        return redirect()->route('login')
            ->withErrors(['error' => 'ID PT tidak ditemukan untuk user ini.']);
    }

    // Redirect berdasarkan peran user
    if ($loggedInUser->role == 'superadmin') {
        // return redirect()->route('superadmin.dashboard', ['id_pt' => $id_pt]);
        return redirect()->route('report-initial-recognition.index', ['id_pt' => $id_pt]);
    } elseif ($loggedInUser->role == 'admin') {
        // return redirect()->route('admin.dashboard', ['id_pt' => $id_pt]);
        return redirect()->route('report-initial-recognition.index', ['id_pt' => $id_pt]);
    }

    // return redirect()->route('dashboard', ['id_pt' => $id_pt]);
    return redirect()->route('report-initial-recognition.index', ['id_pt' => $id_pt]);
}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
