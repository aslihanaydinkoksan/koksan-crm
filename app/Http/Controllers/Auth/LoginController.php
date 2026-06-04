<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Login formunu gösterir.
     */
    public function showLoginForm()
    {
        // Eğer kullanıcı zaten giriş yapmışsa, içeri yönlendir
        if (Auth::check()) {
            return redirect('/customers');
        }

        return view('auth.login');
    }

    /**
     * Kullanıcı giriş işlemini yapar.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Güvenlik: Session ID'yi yenile (Session Fixation koruması)
            $request->session()->regenerate();

            return redirect()->intended('/customers');
        }

        return back()->withErrors([
            'email' => 'Sağladığınız bilgiler kayıtlarımızla eşleşmiyor.',
        ])->onlyInput('email');
    }

    /**
     * Kullanıcı çıkış işlemini yapar.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Oturumu geçersiz kıl ve token'ı yenile
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
