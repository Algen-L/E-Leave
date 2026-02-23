<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Office;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\PasswordReset;
use App\Models\RegistrationRequestLog;
use App\Models\ResetRequestLog;
use App\Models\SecurityTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
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
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('gmail', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['login' => 'Invalid email or password.'])->withInput();
        }

        if (!$user->is_active) {
            return back()->withErrors(['login' => 'Your account is not active. Please contact an administrator.'])->withInput();
        }

        Auth::login($user);

        // Log the action
        ActivityLog::logAction($user->id, 'Login', 'User logged in successfully');

        return $this->redirectBasedOnRole($user);
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
    public function requestRegistrationCode(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'gmail' => 'required|email|unique:users,gmail',
            'office_station' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'employee_number' => 'required|string|regex:/^[0-9]{7}$/|unique:users,employee_number',
        ]);

        // Check rate limit
        if (RegistrationRequestLog::isRateLimitExceeded($request->gmail)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Maximum registration code requests reached for this hour. Please try again in an hour.'
            ]);
        }

        // Generate code
        $code = sprintf("%06d", mt_rand(100000, 999999));

        $fullName = trim($request->first_name . ' ' . ($request->middle_name ? $request->middle_name . ' ' : '') . $request->last_name);

        // Store in session
        Session::put('reg_data', [
            'username' => $request->username,
            'password' => $request->password,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'office_station' => $request->office_station ?? '',
            'position' => $request->position ?? '',
            'employee_number' => $request->employee_number ?? '',
            'area_of_specialization' => $request->area_of_specialization ?? '',
            'age' => (int) ($request->age ?? 0),
            'sex' => $request->sex ?? '',
            'gmail' => $request->gmail,
            'code' => $code,
            'attempts' => 0,
            'expires' => time() + (10 * 60) // 10 minutes
        ]);

        // Log the request
        RegistrationRequestLog::log($request->gmail);

        // Send email
        try {
            $this->sendVerificationEmail($request->gmail, $fullName, $code, 'Registration Verification');

            return response()->json([
                'status' => 'success',
                'message' => "Verification code sent to {$request->gmail}."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send verification email. Please try again.'
            ]);
        }
    }

    /**
     * Verify registration code
     */
    public function verifyRegistrationCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $regData = Session::get('reg_data');

        if (!$regData) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session expired. Please start over.'
            ]);
        }

        if (time() > $regData['expires']) {
            Session::forget('reg_data');
            return response()->json([
                'status' => 'error',
                'message' => 'Verification code expired. Please start over.'
            ]);
        }

        if ($regData['code'] !== $request->code) {
            $regData['attempts']++;
            Session::put('reg_data', $regData);

            if ($regData['attempts'] >= 5) {
                Session::forget('reg_data');
                return response()->json([
                    'status' => 'attempts_exceeded',
                    'message' => 'Too many failed attempts. Your registration session has been cleared. Please start over.'
                ]);
            }

            $remaining = 5 - $regData['attempts'];
            return response()->json([
                'status' => 'error',
                'message' => "Invalid verification code. $remaining attempts remaining."
            ]);
        }

        // Create user
        try {
            $firstName = $regData['first_name'] ?? '';
            $middleName = $regData['middle_name'] ?? '';
            $lastName = $regData['last_name'] ?? '';
            // Construct full name if full_name key is missing (legacy session data check not needed as we updated store)
            // But we should pass individual fields to User::create
            // The User model boot method will handle generating 'full_name' and 'name'

            $user = User::create([
                'username' => $regData['username'],
                'password' => $regData['password'],
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
                'office_station' => $regData['office_station'],
                'position' => $regData['position'],
                'employee_number' => $regData['employee_number'],
                'gmail' => $regData['gmail'],
                'is_active' => true,
                'role' => 'user',
            ]);

            $fullNameCreated = $user->full_name;

            // Notify Head HR
            $headHRs = User::where('role', 'head_hr')->get();
            foreach ($headHRs as $hr) {
                Notification::send($user->id, $hr->id, "A new account has been created and verified: {$fullNameCreated} ({$regData['gmail']})");
            }

            // Log the registration
            ActivityLog::logAction($user->id, 'Profile Created', "New account registered and verified via Gmail: {$regData['gmail']}");

            Session::forget('reg_data');

            return response()->json([
                'status' => 'success',
                'message' => 'Registration successful! Your account is now active. You can now log in.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed. Please try again.'
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
        $request->validate([
            'email' => 'required|email',
        ]);

        // Get or create security tracking record
        $tracking = SecurityTracking::getOrCreate($request->email);
        $tracking->incrementPageVisits();

        // Check if user is blocked
        if ($tracking->isBlocked()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your account has been temporarily blocked. Please contact support.'
            ]);
        }

        $user = User::where('gmail', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'No account found with this email.'
            ]);
        }

        if ($user->role === 'super_admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Password reset is not available for Super Admin accounts.'
            ]);
        }

        // Track every OTP request (new or resend)
        $tracking->incrementOtpRequests();

        // Check combined OTP request + resend rate limit (max 3 per hour)
        if ($tracking->otp_requests >= 3) {
            $tracking->block();
            return response()->json([
                'status' => 'error',
                'message' => 'Maximum OTP requests exceeded. Your account has been temporarily blocked.'
            ]);
        }

        // Check for active token
        $activeToken = PasswordReset::getActiveToken($user->username);

        if ($activeToken) {
            // Resend existing token
            ResetRequestLog::logResend($request->email);
            $tracking->incrementResends();

            // Check resend limit (max 3)
            if ($tracking->resends >= 3) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Maximum resend attempts reached. Please wait for the token to expire and try again.'
                ]);
            }

            try {
                $this->sendVerificationEmail($request->email, $user->full_name, $activeToken->token, 'Password Reset');

                return response()->json([
                    'status' => 'success',
                    'message' => 'Your active verification token has been re-sent to your Gmail.'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to send email. Please try again.'
                ]);
            }
        }

        // Generate new token
        $passwordReset = PasswordReset::generateToken($user->username);
        ResetRequestLog::log($request->email);

        try {
            $this->sendVerificationEmail($request->email, $user->full_name, $passwordReset->token, 'Password Reset');

            return response()->json([
                'status' => 'success',
                'message' => 'Verification token sent to your Gmail. Note: The token expires in 5 minutes.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send email. Please try again.'
            ]);
        }
    }

    /**
     * Verify reset token and set new password
     */
    public function resetPassword(Request $request)
    {
        // Check if this is a verify-only request
        $verifyOnly = $request->boolean('verify_only', false);

        if ($verifyOnly) {
            $request->validate([
                'email' => 'required|email',
                'token' => 'required|string|size:6',
            ]);
        } else {
            $request->validate([
                'email' => 'required|email',
                'token' => 'required|string|size:6',
                'password' => 'required|string|min:6|confirmed',
            ]);
        }

        $user = User::where('gmail', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'No account found with this email.'
            ]);
        }

        // Track OTP input attempt
        $tracking = SecurityTracking::getOrCreate($request->email);
        $tracking->incrementOtpInputs();

        // Check OTP input limit (max 5 attempts)
        if ($tracking->otp_inputs >= 5) {
            $tracking->block();
            return response()->json([
                'status' => 'error',
                'message' => 'Maximum verification attempts exceeded. Your account has been temporarily blocked.'
            ]);
        }

        $passwordReset = PasswordReset::verifyToken($user->username, $request->token);

        if (!$passwordReset) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired verification token.'
            ]);
        }

        // If verify only, just confirm token is valid
        if ($verifyOnly) {
            return response()->json([
                'status' => 'token_valid',
                'message' => 'Token verified successfully.'
            ]);
        }

        // Update password
        $user->update(['password' => $request->password]);

        // Delete the reset token
        PasswordReset::deleteForUser($user->username);

        // Log the action
        ActivityLog::logAction($user->id, 'Password Reset', 'Password was reset via email verification');

        return response()->json([
            'status' => 'success',
            'message' => 'Your password has been reset successfully. You can now log in.'
        ]);
    }

    /**
     * Redirect user based on role
     */
    protected function redirectBasedOnRole(User $user)
    {
        switch ($user->role) {
            case 'super_admin':
            case 'admin':
            case 'head_hr':
                return redirect()->route('admin.dashboard');
            case 'hr':
                return redirect()->route('hr.dashboard');
            default:
                return redirect()->route('user.home');
        }
    }

    /**
     * Send verification email
     */
    protected function sendVerificationEmail(string $to, string $name, string $code, string $type)
    {
        $subject = "{$type} - Verification Code";

        // Define logo paths
        $depEdLogoPath = public_path('images/logo.png');

        // Determine context message based on type
        $actionText = 'complete your request';
        if (stripos($type, 'Registration') !== false) {
            $actionText = 'complete your registration';
        } elseif (stripos($type, 'Password') !== false) {
            $actionText = 'reset your password';
        }

        // Using Laravel's Mail facade - configure mail settings in .env
        Mail::send([], [], function ($message) use ($to, $subject, $code, $depEdLogoPath, $actionText) {
            $message->to($to)
                ->subject($subject);

            // Embed images
            $depEdCid = $message->embed($depEdLogoPath);

            $body = "<!DOCTYPE html>
            <html>
            <head>
                <style>
                    .email-container {
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                        max-width: 600px;
                        margin: 0 auto;
                        background-color: #f4f7f9;
                        padding: 20px;
                    }
                    .card {
                        background-color: #ffffff;
                        border-radius: 16px;
                        overflow: hidden;
                        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
                    }
                    .header-gradient {
                        background: linear-gradient(to left, #9ecdf8 0%, #005a8a 100%);
                        padding: 40px 10px;
                        text-align: center;
                    }
                    /* Balanced Horizontal Table Layout */
                    .header-table {
                        width: 100%;
                        border-collapse: collapse;
                        table-layout: fixed;
                    }
                    .header-cell {
                        vertical-align: middle;
                        text-align: center;
                        padding: 0;
                    }
                    /* Fixed Heights for Symmetry */
                    .logo-img {
                        height: 75px; 
                        width: auto;
                        display: block;
                        margin: 0 auto;
                    }
                    .header-title-cell {
                        width: 70%; /* Center column */
                    }
                    .logo-side-cell {
                        width: 15%; /* Symmetrical sides */
                    }
                    .header-title h2 {
                        margin: 0;
                        color: #ffffff;
                        font-family: 'Segoe UI', Arial, sans-serif;
                        font-size: 20px;
                        font-weight: 900;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                        line-height: 1.25;
                    }
                    @media screen and (max-width: 480px) {
                        .logo-img {
                            height: 55px; /* Scale down but keep symmetry */
                        }
                        .header-title h2 {
                            font-size: 15px;
                        }
                    }
                    .content {
                        padding: 40px 20px;
                        text-align: center;
                        color: #334155;
                    }
                    .code-label {
                        font-size: 16px;
                        color: #64748b;
                        margin-bottom: 10px;
                    }
                    .code-box {
                        display: inline-block;
                        padding: 15px 30px;
                        background-color: #f1f5f9;
                        border: 2px dashed #1b6ca8;
                        border-radius: 12px;
                        font-size: 36px;
                        font-weight: 800;
                        letter-spacing: 6px;
                        color: #003366;
                        margin: 25px 0;
                    }
                    .footer {
                        padding: 25px;
                        background-color: #f8fafc;
                        text-align: center;
                        font-size: 12px;
                        color: #94a3b8;
                        border-top: 1px solid #e2e8f0;
                    }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='card'>
                        <div class='header-gradient'>
                            <table class='header-table'>
                                <tr>
                                    <!-- Left Column: DepEd Logo -->
                                    <td class='header-cell logo-side-cell'>
                                        <img src='{$depEdCid}' class='logo-img' alt='DepEd Logo'>
                                    </td>
                                    <!-- Center Column: Title -->
                                    <td class='header-cell header-title-cell'>
                                        <div class='header-title'>
                                            <h2>SCHOOLS DIVISION OFFICE<br>LEAVE APPLICATION SYSTEM</h2>
                                        </div>
                                    </td>
                                    <!-- Right Column: Empty for balance -->
                                    <td class='header-cell logo-side-cell'>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class='content'>
                            <p style='font-size: 18px; font-weight: 500;'>Verification Code Request</p>
                            <p class='code-label'>Please use the following 6-digit code to {$actionText}:</p>
                            <div class='code-box'>{$code}</div>
                            <p style='margin-top: 20px;'>If you didn't request this code, you can safely ignore this email.</p>
                        </div>
                        <div class='footer'>
                            &copy; " . date('Y') . " San Pedro Division Office - Learning & Development Unit<br>
                            This is an automated message, please do not reply.
                        </div>
                    </div>
                </div>
            </body>
            </html>";

            $message->html($body);
        });
    }
}
