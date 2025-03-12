<?php
 
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
 
class LoginController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $employeeNumber = $request->integer('employee-number');
        $rememberSession = $request->input('remember-me');
        $email = $request->input('email');
        
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
 
        $user = User::whereEmail($email)->first();

        $success = false;
        if ($user != null) {
            $employee = $user->employee()->first();
            
            if ($employee != null && $employee->id === $employeeNumber) 
                $success = Auth::attempt($credentials, $rememberSession);
        }

        if ($success) {
            $request->session()->regenerate();
            
            return redirect()
                ->route("dashboard")
                ->withSuccess("Login successful.");
        }
 
        return back()
            ->exceptInput('password')
            ->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
    
        $request->session()->invalidate();
    
        $request->session()->regenerateToken();
    
        return redirect('/login');
    }
}