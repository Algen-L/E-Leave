<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HRController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/

// Serve storage files (profile pics, e-signatures) via /media/ - avoids conflict with storage directory
$storageHandler = function (string $path) {
    $path = trim($path, '/');
    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }

    $fullPath = Storage::disk('public')->path($path);
    $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';

    return response()->file($fullPath, [
        'Content-Type' => $mimeType,
    ]);
};

$pathPrefix = trim(parse_url(config('app.url'), PHP_URL_PATH), '/');
if ($pathPrefix) {
    Route::get("{$pathPrefix}/media/{path}", $storageHandler)->where('path', '.*')->name('storage.serve');
}
Route::get('media/{path}', $storageHandler)->where('path', '.*')->name('storage.serve.root');

// Welcome page redirects to login
Route::get('/', function () {
    return redirect('index.php/login');
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login')->name('login.submit');
    Route::post('/logout', 'logout')->name('logout');

    // Registration
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register/request-code', 'requestRegistrationCode')->name('register.request-code');
    Route::post('/register/verify-code', 'verifyRegistrationCode')->name('register.verify-code');

    // Password Reset
    Route::get('/forgot-password', 'showForgotPassword')->name('password.request');
    Route::post('/forgot-password', 'requestPasswordReset')->name('password.email');
    Route::post('/reset-password', 'resetPassword')->name('password.reset');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,super_admin,head_hr,hr'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/api', [AdminController::class, 'dashboardApi'])->name('dashboard.api');

    Route::get('/manage-users', [AdminController::class, 'manageUsers'])->name('manage-users');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::patch('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');

    Route::get('/register-user', [AdminController::class, 'showRegisterUser'])->name('register-user');
    Route::post('/register-user', [AdminController::class, 'storeUser'])->name('register-user.store');

    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');

    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');

    Route::post('/notifications/send', [AdminController::class, 'sendNotification'])->name('notifications.send');

    Route::get('/activity-logs', [AdminController::class, 'activityLogs'])->name('activity-logs');

    Route::get('/password-reset-management', [AdminController::class, 'passwordResetManagement'])->name('password-reset-management');

    // Authentication Reset Management
    Route::get('/auth-reset-management', [AdminController::class, 'authResetManagement'])->name('auth-reset-management');
    Route::get('/auth-reset/{id}/details', [AdminController::class, 'authResetDetails'])->name('auth-reset.details');
    Route::post('/auth-reset/{id}/reset-counters', [AdminController::class, 'authResetCounters'])->name('auth-reset.reset-counters');
    Route::post('/auth-reset/{id}/block', [AdminController::class, 'authResetBlock'])->name('auth-reset.block');
    Route::post('/auth-reset/{id}/unblock', [AdminController::class, 'authResetUnblock'])->name('auth-reset.unblock');
    Route::post('/auth-reset/send-reset', [AdminController::class, 'sendPasswordReset'])->name('auth-reset.send-reset');

    // Signatories (Super Admin Only)
    Route::get('/signatories', [AdminController::class, 'signatories'])->name('signatories');
    Route::post('/signatories', [AdminController::class, 'updateSignatories'])->name('signatories.update');
});

/*
|--------------------------------------------------------------------------
| Head HR Routes (Leave Credit Management) - Also accessible by Super Admin
|--------------------------------------------------------------------------
*/
Route::prefix('head-hr')->name('head-hr.')->middleware(['auth', 'role:head_hr,super_admin'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\HeadHRController::class, 'dashboard'])->name('dashboard');
    Route::get('/leave-policies', [App\Http\Controllers\HeadHRController::class, 'policies'])->name('leave-policies');
    Route::post('/leave-policies', [App\Http\Controllers\HeadHRController::class, 'updatePolicy'])->name('leave-policies.update');
    Route::post('/leave-types', [App\Http\Controllers\HeadHRController::class, 'storeLeaveType'])->name('leave-types.store');
    Route::delete('/leave-types/{id}', [App\Http\Controllers\HeadHRController::class, 'destroyLeaveType'])->name('leave-types.destroy');
    Route::get('/audit-logs', [App\Http\Controllers\HeadHRController::class, 'auditLogs'])->name('audit-logs');
    Route::post('/requests/{id}', [App\Http\Controllers\HeadHRController::class, 'handleRequest'])->name('requests.handle');
});

/*
|--------------------------------------------------------------------------
| HR Staff Routes (Leave Credit Input)
|--------------------------------------------------------------------------
*/
Route::prefix('hr-staff')->name('hr-staff.')->middleware(['auth', 'role:hr,head_hr'])->group(function () {
    // HR can access this, Head HR too if needed (or just restrict to HR)
    Route::get('/manage-credits', [App\Http\Controllers\HRController::class, 'manageCredits'])->name('manage-credits');
    Route::get('/manage-credits/{user}', [App\Http\Controllers\HRController::class, 'editCredits'])->name('manage-credits.edit');
    Route::post('/manage-credits/{user}', [App\Http\Controllers\HRController::class, 'updateCredits'])->name('manage-credits.update');
    Route::post('/manage-credits/{user}/add-cto', [App\Http\Controllers\HRController::class, 'addCtoCredit'])->name('manage-credits.add-cto');
    Route::post('/unlock-credits/{user}', [App\Http\Controllers\HRController::class, 'requestUnlock'])->name('manage-credits.unlock-request');
});

/*
|--------------------------------------------------------------------------
| HR Routes (Profile)
|--------------------------------------------------------------------------
*/
Route::prefix('hr')->name('hr.')->middleware(['auth', 'role:hr'])->group(function () {
    Route::get('/dashboard', [HRController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [HRController::class, 'profile'])->name('profile');
    Route::put('/profile', [HRController::class, 'updateProfile'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/
Route::prefix('user')->name('user.')->middleware('auth')->group(function () {
    Route::get('/home', [UserController::class, 'home'])->name('home');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

    // Password Update with Verification
    Route::post('/profile/password/request-token', [UserController::class, 'requestPasswordToken'])->name('profile.password.request-token');
    Route::put('/profile/password/update', [UserController::class, 'updatePassword'])->name('profile.password.update');

    // E-Leave System
    Route::get('/leave/apply', [App\Http\Controllers\LeaveController::class, 'showApplyForm'])->name('leave.apply');
    Route::post('/leave/apply', [App\Http\Controllers\LeaveController::class, 'submitApplication'])->name('leave.submit');
    Route::get('/leave/history', [App\Http\Controllers\LeaveController::class, 'myApplications'])->name('leave.history');
    Route::get('/leave/view/{id}', [App\Http\Controllers\LeaveController::class, 'show'])->name('leave.show');
    Route::get('/leave/form6/{id}', [App\Http\Controllers\LeaveController::class, 'generateForm6'])->name('leave.form6');

    // Approval Workflow
    Route::get('/leave/approvals', [App\Http\Controllers\LeaveApprovalController::class, 'index'])->name('leave.approvals');
    Route::get('/leave/approvals/{id}', [App\Http\Controllers\LeaveApprovalController::class, 'show'])->name('leave.approvals.show');
    Route::post('/leave/approvals/{id}/verify', [App\Http\Controllers\LeaveApprovalController::class, 'verify'])->name('leave.verify');
    Route::post('/leave/approvals/{id}/recommend', [App\Http\Controllers\LeaveApprovalController::class, 'recommend'])->name('leave.recommend');
    Route::post('/leave/approvals/{id}/approve', [App\Http\Controllers\LeaveApprovalController::class, 'approve'])->name('leave.approve');
    Route::post('/leave/approvals/{id}/reject', [App\Http\Controllers\LeaveApprovalController::class, 'reject'])->name('leave.reject');

    // Notifications
    Route::post('/notifications/read', [UserController::class, 'markNotificationRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [UserController::class, 'markAllNotificationsRead'])->name('notifications.read-all');
    Route::get('/notifications', [UserController::class, 'getNotifications'])->name('notifications');
});
