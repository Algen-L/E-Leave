@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="login-container">
    <div class="header">
        <div class="logo-container">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </div>
        <h1 id="authTitle">Reset Password</h1>
        <p id="authSubtitle">Recover your account access</p>
    </div>

    <!-- Step 1: Request Reset Token -->
    <div id="fpSection" class="form-section active">
        <div id="fpStep1">
            <div class="form-group">
                <label>Gmail Address</label>
                <input type="email" id="fp_email" class="form-control" placeholder="example@gmail.com" required autofocus>
                <div style="margin-top: 10px; color: var(--text-muted); font-size: 0.75rem; font-style: italic; text-align: left;">
                    Enter your registered Gmail address and we'll send a reset token.
                </div>
            </div>
            <button type="button" class="btn" id="fpRequestBtn" onclick="requestResetToken()">Send Reset Token</button>
            <div style="margin-top: 12px; color: var(--text-muted); font-size: 0.65rem; font-style: italic; text-align: center;">
                <i class="fas fa-info-circle"></i> Token expires in 5 minutes. (Maximum 3 requests per hour).
            </div>
        </div>

        <!-- Step 2: Verify Token -->
        <div id="fpStep2" style="display: none;">
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 20px; text-align: center;">
                Enter the 6-digit token sent to your email to verify your identity.
            </p>
            <div class="form-group">
                <label>Verification Token</label>
                <div class="token-boxes">
                    <input type="text" class="token-box" maxlength="1" data-index="0" inputmode="numeric" pattern="[0-9]" placeholder="-">
                    <input type="text" class="token-box" maxlength="1" data-index="1" inputmode="numeric" pattern="[0-9]" placeholder="-">
                    <input type="text" class="token-box" maxlength="1" data-index="2" inputmode="numeric" pattern="[0-9]" placeholder="-">
                    <input type="text" class="token-box" maxlength="1" data-index="3" inputmode="numeric" pattern="[0-9]" placeholder="-">
                    <input type="text" class="token-box" maxlength="1" data-index="4" inputmode="numeric" pattern="[0-9]" placeholder="-">
                    <input type="text" class="token-box" maxlength="1" data-index="5" inputmode="numeric" pattern="[0-9]" placeholder="-">
                </div>
                <input type="hidden" id="fp_token">
            </div>
            <button type="button" class="btn" id="fpVerifyBtn" onclick="verifyResetToken()">Verify Token</button>
            <div style="margin-top: 15px; text-align: center;">
                <span class="toggle-link" id="fpResendBtn" onclick="resendResetToken()" style="font-size: 0.75rem; cursor: pointer;">
                    <i class="fas fa-sync-alt"></i> Resend Token
                </span>
            </div>
        </div>

        <!-- Step 3: Set New Password -->
        <div id="fpStep3" style="display: none;">
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 20px; text-align: center;">
                Token verified! Now set your new password.
            </p>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" id="fp_new_password" class="form-control" placeholder="••••••••" minlength="6" required>
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" id="fp_confirm_password" class="form-control" placeholder="••••••••" minlength="6" required>
            </div>
            <button type="button" class="btn" id="fpResetBtn" onclick="resetPassword()">Update Password</button>
        </div>

        <div class="footer-text">
            <a href="{{ route('login') }}" class="toggle-link">Back to Login</a>
        </div>
    </div>

    <div class="auth-footer">
        <div class="dev-info">Department of Education - Schools Division Office of San Pedro City</div>
        <div class="dev-info">Developed by Algen Loveres & Cedrick Bacaresas</div>
    </div>
</div>

<input type="hidden" id="reset_token_email">
@endsection

@push('scripts')
<script>
    // Global reset email state
    let resetEmail = '';

    // Handle Token Box Inputs
    document.addEventListener('DOMContentLoaded', function() {
        const tokenBoxes = document.querySelectorAll('.token-box');
        
        tokenBoxes.forEach((box, index) => {
            box.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length === 1 && index < tokenBoxes.length - 1) {
                    tokenBoxes[index + 1].focus();
                }
                updateTokenInput();
            });
            
            box.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    tokenBoxes[index - 1].focus();
                }
            });

            box.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '');
                if (pastedData.length === 6) {
                    tokenBoxes.forEach((b, i) => b.value = pastedData[i] || '');
                    tokenBoxes[5].focus();
                    updateTokenInput();
                }
            });
        });

        function updateTokenInput() {
            let code = '';
            tokenBoxes.forEach(box => code += box.value);
            document.getElementById('fp_token').value = code;
        }
    });

    function requestResetToken() {
        const emailInput = document.getElementById('fp_email');
        const email = emailInput.value.trim();
        const btn = document.getElementById('fpRequestBtn');
        const originalText = btn.innerHTML;

        if (!email) {
            showToast('Please enter your registered Gmail address.', 'warning');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

        fetch('{{ route("password.email") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showToast(data.message, 'success');
                resetEmail = email;
                document.getElementById('reset_token_email').value = email;
                document.getElementById('fpStep1').style.display = 'none';
                document.getElementById('fpStep2').style.display = 'block';
                document.getElementById('fpStep2').classList.add('animate__animated', 'animate__fadeIn');
            } else {
                showToast(data.message, 'error');
            }
            btn.disabled = false;
            btn.innerHTML = originalText;
        })
        .catch(error => {
            showToast('An error occurred. Please try again.', 'error');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    function verifyResetToken() {
        const email = document.getElementById('reset_token_email').value;
        const token = document.getElementById('fp_token').value.trim();
        const btn = document.getElementById('fpVerifyBtn');
        const originalText = btn.innerHTML;

        if (token.length !== 6) {
            showToast("Please enter the 6-digit verification token.", 'warning');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';

        fetch('{{ route("password.reset") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: email,
                token: token,
                verify_only: true
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success' || data.status === 'token_valid') {
                showToast(data.message || 'Token verified successfully', 'success');
                document.getElementById('fpStep2').style.display = 'none';
                document.getElementById('fpStep3').style.display = 'block';
                document.getElementById('fpStep3').classList.add('animate__animated', 'animate__fadeIn');
            } else {
                showToast(data.message || 'Invalid token', 'error');
            }
            btn.disabled = false;
            btn.innerHTML = originalText;
        })
        .catch(e => {
            showToast("An error occurred. Please try again.", 'error');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    function resendResetToken() {
        const email = document.getElementById('reset_token_email').value;
        const btn = document.getElementById('fpResendBtn');
        const originalText = btn.innerHTML;

        btn.style.pointerEvents = 'none';
        btn.style.opacity = '0.7';
        btn.innerHTML = '<i class="fas fa-hourglass-half"></i> Resending...';

        fetch('{{ route("password.email") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email: email })
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                showToast(data.message, 'success');
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(e => showToast("An error occurred. Please try again.", 'error'))
        .finally(() => {
            setTimeout(() => {
                btn.style.pointerEvents = 'auto';
                btn.style.opacity = '1';
                btn.innerHTML = originalText;
            }, 2000);
        });
    }

    function resetPassword() {
        const email = document.getElementById('reset_token_email').value;
        const token = document.getElementById('fp_token').value.trim();
        const password = document.getElementById('fp_new_password').value.trim();
        const confirm = document.getElementById('fp_confirm_password').value.trim();
        const btn = document.getElementById('fpResetBtn');

        if (!password || !confirm) {
            showToast("Please fill in both password fields.", 'error');
            return;
        }

        if (password !== confirm) {
            showToast("Passwords do not match.", 'error');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';

        fetch('{{ route("password.reset") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: email,
                token: token,
                password: password,
                password_confirmation: confirm
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                showToast(data.message, 'success');
                setTimeout(() => {
                    if (window.triggerExitMorph) window.triggerExitMorph('{{ route("login") }}');
                    else window.location.href = '{{ route("login") }}';
                }, 2000);
            } else {
                showToast(data.message || 'Failed to update password', 'error');
                btn.disabled = false;
                btn.innerHTML = 'Update Password';
            }
        })
        .catch(e => {
            showToast("An error occurred.", 'error');
            btn.disabled = false;
            btn.innerHTML = 'Update Password';
        });
    }
</script>
@endpush
