<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Office;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Services\AuthService;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Show login page
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        $offices = Office::all()->groupBy('category');
        return view('auth.login', compact('offices'));
    }

    /**
     * Handle login
     */
    public function login(LoginRequest $request)
    {
        try {
            $user = $this->authService->login($request->validated());
            return $this->redirectBasedOnRole($user);
        } catch (\Exception $e) {
            return back()->withErrors(['login' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Handle logout
     */
    public function logout()
    {
        if (Auth::check()) {
            ActivityLog::logAction(Auth::id(), 'Logout', 'User logged out');
        }

        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Show registration form
     */
    public function showRegister()
    {
        $offices = Office::all()->groupBy('category');
        return view('auth.register', compact('offices'));
    }

    /**
     * Request registration code
     */
    public function requestRegistrationCode(RegisterRequest $request)
    {
        try {
            $this->authService->requestRegistrationCode($request->validated());
            return response()->json([
                'status' => 'success',
                'message' => "Verification code sent to {$request->gmail}."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Verify registration code
     */
    public function verifyRegistrationCode(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        try {
            $this->authService->verifyRegistrationAndCreate($request->code);
            return response()->json([
                'status' => 'success',
                'message' => 'Registration successful! Your account is now active. You can now log in.'
            ]);
        } catch (\Exception $e) {
            $status = str_contains($e->getMessage(), 'Too many failed attempts') ? 'attempts_exceeded' : 'error';
            return response()->json([
                'status' => $status,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Request password reset
     */
    public function requestPasswordReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        try {
            $message = $this->authService->requestPasswordReset($request->email);
            return response()->json([
                'status' => 'success',
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Verify reset token and set new password
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $this->authService->resetPassword($request->validated());

            if ($request->boolean('verify_only', false)) {
                return response()->json([
                    'status' => 'token_valid',
                    'message' => 'Token verified successfully.'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Your password has been reset successfully. You can now log in.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Redirect user based on role
     */
    protected function redirectBasedOnRole(User $user)
    {
        switch ($user->role) {
            case 'super_admin':
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'head_hr':
            case 'hr':
                return redirect()->route('hr.dashboard');
            case 'record_personnel':
                return redirect()->route('records.dashboard');
            default:
                return redirect()->route('user.home');
        }
    }
}
