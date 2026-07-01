<?php

namespace App\Services;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\PasswordReset;
use App\Models\RegistrationRequestLog;
use App\Models\ResetRequestLog;
use App\Models\SecurityTracking;
use App\Models\Notification;
use App\Mail\VerificationEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * Attempt to log in a user
     */
    public function login(array $credentials)
    {
        $user = User::where('gmail', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new \Exception('Invalid email or password.');
        }

        if (!$user->is_active) {
            throw new \Exception('Your account is not active. Please contact an administrator.');
        }

        Auth::login($user);
        ActivityLog::logAction($user->id, 'Login', 'User logged in successfully');

        return $user;
    }

    /**
     * Request a registration code
     */
    public function requestRegistrationCode(array $data)
    {
        if (RegistrationRequestLog::isRateLimitExceeded($data['gmail'])) {
            throw new \Exception('Maximum registration code requests reached for this hour. Please try again in an hour.');
        }

        $code = sprintf("%06d", mt_rand(100000, 999999));
        $fullName = trim($data['first_name'] . ' ' . ($data['middle_name'] ? $data['middle_name'] . ' ' : '') . $data['last_name']);

        $data['username'] = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $data['first_name'])) . mt_rand(1000, 9999);
        $data['area_of_specialization'] = null;

        Session::put('reg_data', array_merge($data, [
            'code' => $code,
            'attempts' => 0,
            'expires' => time() + (10 * 60)
        ]));

        RegistrationRequestLog::log($data['gmail']);
        Mail::to($data['gmail'])->send(new VerificationEmail($code, 'Registration Verification'));

        return true;
    }

    /**
     * Verify registration code and create user
     */
    public function verifyRegistrationAndCreate(string $code)
    {
        $regData = Session::get('reg_data');

        if (!$regData)
            throw new \Exception('Session expired. Please start over.');
        if (time() > $regData['expires']) {
            Session::forget('reg_data');
            throw new \Exception('Verification code expired. Please start over.');
        }

        if ($regData['code'] !== $code) {
            $regData['attempts']++;
            Session::put('reg_data', $regData);

            if ($regData['attempts'] >= 5) {
                Session::forget('reg_data');
                throw new \Exception('Too many failed attempts. Your registration session has been cleared. Please start over.');
            }

            $remaining = 5 - $regData['attempts'];
            throw new \Exception("Invalid verification code. $remaining attempts remaining.");
        }

        $user = User::create([
            'username' => $regData['username'],
            'password' => $regData['password'],
            'first_name' => $regData['first_name'],
            'middle_name' => $regData['middle_name'],
            'last_name' => $regData['last_name'],
            'office_station' => $regData['office_station'],
            'position' => $regData['position'],
            'area_of_specialization' => $regData['area_of_specialization'] ?? null,
            'age' => $regData['age'] ?? null,
            'sex' => $regData['sex'] ?? null,
            'employee_number' => $regData['employee_number'],
            'gmail' => $regData['gmail'],
            'is_active' => true,
            'role' => 'user',
        ]);

        // Attach default user role
        $userRole = \App\Models\Role::where('name', 'user')->first();
        if ($userRole) {
            $user->roles()->attach($userRole->id);
        }

        // Notify Head HR
        $headHRs = User::where('role', 'head_hr')->get();
        foreach ($headHRs as $hr) {
            Notification::send($user->id, $hr->id, "A new account has been created and verified: {$user->full_name} ({$regData['gmail']})");
        }

        ActivityLog::logAction($user->id, 'Profile Created', "New account registered and verified via Gmail: {$regData['gmail']}");
        Session::forget('reg_data');

        return $user;
    }

    /**
     * Request password reset
     */
    public function requestPasswordReset(string $email)
    {
        $tracking = SecurityTracking::getOrCreate($email);
        $tracking->incrementPageVisits();

        if ($tracking->isBlocked())
            throw new \Exception('Your account has been temporarily blocked. Please contact support.');

        $user = User::where('gmail', $email)->first();
        if (!$user)
            throw new \Exception('No account found with this email.');
        if ($user->role === 'super_admin')
            throw new \Exception('Password reset is not available for Super Admin accounts.');

        $tracking->incrementOtpRequests();
        if ($tracking->otp_requests >= 3) {
            $tracking->block();
            throw new \Exception('Maximum OTP requests exceeded. Your account has been temporarily blocked.');
        }

        $activeToken = PasswordReset::getActiveToken($user->username);
        if ($activeToken) {
            ResetRequestLog::logResend($email);
            $tracking->incrementResends();
            if ($tracking->resends >= 3)
                throw new \Exception('Maximum resend attempts reached. Please wait for the token to expire and try again.');

            Mail::to($email)->send(new VerificationEmail($activeToken->token, 'Password Reset'));
            return 'Your active verification token has been re-sent to your Gmail.';
        }

        $passwordReset = PasswordReset::generateToken($user->username);
        ResetRequestLog::log($email);
        Mail::to($email)->send(new VerificationEmail($passwordReset->token, 'Password Reset'));

        return 'Verification token sent to your Gmail. Note: The token expires in 5 minutes.';
    }

    /**
     * Reset password
     */
    public function resetPassword(array $data)
    {
        $user = User::where('gmail', $data['email'])->first();
        if (!$user)
            throw new \Exception('No account found with this email.');

        $tracking = SecurityTracking::getOrCreate($data['email']);
        $tracking->incrementOtpInputs();

        if ($tracking->otp_inputs >= 5) {
            $tracking->block();
            throw new \Exception('Maximum verification attempts exceeded. Your account has been temporarily blocked.');
        }

        $passwordReset = PasswordReset::verifyToken($user->username, $data['token']);
        if (!$passwordReset)
            throw new \Exception('Invalid or expired verification token.');

        if (isset($data['verify_only']) && $data['verify_only'])
            return true;

        $user->update(['password' => $data['password']]);
        PasswordReset::deleteForUser($user->username);
        ActivityLog::logAction($user->id, 'Password Reset', 'Password was reset via email verification');

        return true;
    }
}
