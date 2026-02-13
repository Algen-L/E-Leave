@extends('layouts.auth')

@section('title', 'Reset Password')

@push('styles')
<style>
    .reset-card {
        background: #1e293b;
        border-radius: 16px;
        padding: 40px;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        position: relative;
        z-index: 10;
    }
    
    .reset-logo {
        text-align: center;
        margin-bottom: 24px;
    }
    
    .reset-logo img {
        width: 80px;
        height: 80px;
        object-fit: contain;
    }
    
    .reset-title {
        text-align: center;
        margin-bottom: 8px;
    }
    
    .reset-title h1 {
        color: #f8fafc;
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
    }
    
    .reset-subtitle {
        text-align: center;
        color: #94a3b8;
        font-size: 0.9rem;
        margin-bottom: 24px;
    }
    
    .step-description {
        color: #f8fafc;
        font-size: 0.9rem;
        margin-bottom: 24px;
        line-height: 1.5;
    }
    
    .form-label-custom {
        display: block;
        color: #94a3b8;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
    }
    
    .form-input-custom {
        width: 100%;
        background: #334155;
        border: none;
        border-radius: 8px;
        padding: 14px 16px;
        color: #f8fafc;
        font-size: 0.95rem;
        margin-bottom: 20px;
        transition: all 0.2s;
    }
    
    .form-input-custom::placeholder {
        color: #64748b;
    }
    
    .form-input-custom:focus {
        outline: none;
        box-shadow: 0 0 0 2px #0d9488;
        background: #3d4f66;
    }
    
    .btn-reset {
        width: 100%;
        background: linear-gradient(135deg, #0f4c5c 0%, #0d9488 100%);
        border: none;
        border-radius: 8px;
        padding: 14px;
        color: white;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        margin-bottom: 20px;
    }
    
    .btn-reset:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 148, 136, 0.4);
    }
    
    .btn-reset:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }
    
    .back-link {
        display: block;
        text-align: center;
        color: #94a3b8;
        font-size: 0.9rem;
        text-decoration: underline;
        background: none;
        border: none;
        cursor: pointer;
        width: 100%;
    }
    
    .back-link:hover {
        color: #0d9488;
    }
    
    /* Token Input Boxes */
    .token-container {
        margin-bottom: 24px;
    }
    
    .token-boxes {
        display: flex;
        justify-content: center;
        gap: 8px;
    }
    
    .token-box {
        width: 48px;
        height: 56px;
        background: #334155;
        border: 2px solid #475569;
        border-radius: 10px;
        text-align: center;
        font-size: 1.5rem;
        font-weight: 600;
        color: #f8fafc;
        transition: all 0.2s;
        caret-color: #0d9488;
    }
    
    .token-box::placeholder {
        color: #64748b;
        font-weight: 400;
    }
    
    .token-box:focus {
        outline: none;
        border-color: #0d9488;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.2);
        background: #3d4f66;
    }
    
    .token-box.filled {
        border-color: #0d9488;
    }
    
    .token-box.error {
        border-color: #ef4444;
        animation: shake 0.3s ease-in-out;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    .step-indicator {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-bottom: 24px;
    }
    
    .step-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #475569;
        transition: all 0.3s;
    }
    
    .step-dot.active {
        background: #0d9488;
        width: 24px;
        border-radius: 4px;
    }
    
    .step-dot.completed {
        background: #10b981;
    }
</style>
@endpush

@section('content')
<div class="reset-card">
    <div class="reset-logo">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" onerror="this.src='https://via.placeholder.com/80'">
    </div>
    
    <div class="reset-title">
        <h1>Reset Password</h1>
    </div>
    <p class="reset-subtitle">Login to your account</p>

    <!-- Step 1: Request Reset Token -->
    <div id="step1" class="step-content">
        <p class="step-description">Enter your registered Gmail address to receive a reset token.</p>
        
        <form id="requestForm">
            @csrf
            <label class="form-label-custom">Gmail Address</label>
            <input type="email" class="form-input-custom" id="email" name="email" placeholder="example@gmail.com" required>
            
            <button type="submit" class="btn-reset" id="requestBtn">Send Reset Token</button>
        </form>
        
        <a href="{{ route('login') }}" class="back-link">Back to Login</a>
    </div>

    <!-- Step 2: Verify Token -->
    <div id="step2" class="step-content" style="display: none;">
        <p class="step-description">Enter the 6-digit token sent to your email.</p>
        
        <form id="verifyForm">
            @csrf
            <label class="form-label-custom">Verification Token</label>
            <div class="token-container">
                <div class="token-boxes">
                    <input type="text" class="token-box" maxlength="1" data-index="0" inputmode="numeric" pattern="[0-9]" placeholder="-">
                    <input type="text" class="token-box" maxlength="1" data-index="1" inputmode="numeric" pattern="[0-9]" placeholder="-">
                    <input type="text" class="token-box" maxlength="1" data-index="2" inputmode="numeric" pattern="[0-9]" placeholder="-">
                    <input type="text" class="token-box" maxlength="1" data-index="3" inputmode="numeric" pattern="[0-9]" placeholder="-">
                    <input type="text" class="token-box" maxlength="1" data-index="4" inputmode="numeric" pattern="[0-9]" placeholder="-">
                    <input type="text" class="token-box" maxlength="1" data-index="5" inputmode="numeric" pattern="[0-9]" placeholder="-">
                </div>
            </div>
            <input type="hidden" id="tokenInput" name="token">
            
            <button type="submit" class="btn-reset" id="verifyBtn">Verify Token</button>
        </form>
        
        <button type="button" class="back-link" id="backToStep1">Back to Login</button>
    </div>

    <!-- Step 3: Set New Password -->
    <div id="step3" class="step-content" style="display: none;">
        <p class="step-description">Set your new strong password.</p>
        
        <form id="resetForm">
            @csrf
            <input type="hidden" id="resetEmail" name="email">
            <input type="hidden" id="verifiedToken" name="token">
            
            <label class="form-label-custom">New Password</label>
            <input type="password" class="form-input-custom" id="password" name="password" placeholder="••••••••" minlength="6" required>
            
            <label class="form-label-custom">Confirm Password</label>
            <input type="password" class="form-input-custom" id="password_confirmation" name="password_confirmation" placeholder="••••••••" minlength="6" required>

            <button type="submit" class="btn-reset" id="resetBtn">Update Password</button>
        </form>
        
        <button type="button" class="back-link" id="backToStep2">Back to Login</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const step3 = document.getElementById('step3');
    
    const requestForm = document.getElementById('requestForm');
    const verifyForm = document.getElementById('verifyForm');
    const resetForm = document.getElementById('resetForm');
    
    const tokenBoxes = document.querySelectorAll('.token-box');
    const tokenInput = document.getElementById('tokenInput');
    const resetEmail = document.getElementById('resetEmail');
    const verifiedToken = document.getElementById('verifiedToken');
    
    let userEmail = '';
    
    // Token box input handling
    tokenBoxes.forEach((box, index) => {
        box.addEventListener('input', function(e) {
            // Only allow numbers
            this.value = this.value.replace(/[^0-9]/g, '');
            
            if (this.value.length === 1) {
                this.classList.add('filled');
                if (index < tokenBoxes.length - 1) {
                    tokenBoxes[index + 1].focus();
                }
            } else {
                this.classList.remove('filled');
            }
            updateTokenInput();
        });
        
        box.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace') {
                if (!this.value && index > 0) {
                    tokenBoxes[index - 1].focus();
                    tokenBoxes[index - 1].value = '';
                    tokenBoxes[index - 1].classList.remove('filled');
                }
            }
            // Allow paste
            if (e.key === 'v' && (e.ctrlKey || e.metaKey)) {
                return;
            }
        });
        
        box.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '');
            if (pastedData.length === 6) {
                tokenBoxes.forEach((b, i) => {
                    b.value = pastedData[i] || '';
                    if (b.value) b.classList.add('filled');
                });
                tokenBoxes[5].focus();
                updateTokenInput();
            }
        });
        
        box.addEventListener('focus', function() {
            this.select();
        });
    });
    
    function updateTokenInput() {
        let code = '';
        tokenBoxes.forEach(box => code += box.value);
        tokenInput.value = code;
    }
    
    function showStep(stepNum) {
        step1.style.display = stepNum === 1 ? 'block' : 'none';
        step2.style.display = stepNum === 2 ? 'block' : 'none';
        step3.style.display = stepNum === 3 ? 'block' : 'none';
    }
    
    function clearTokenInputs() {
        tokenBoxes.forEach(box => {
            box.value = '';
            box.classList.remove('filled', 'error');
        });
        tokenInput.value = '';
    }
    
    function showTokenError() {
        tokenBoxes.forEach(box => box.classList.add('error'));
        setTimeout(() => {
            tokenBoxes.forEach(box => box.classList.remove('error'));
        }, 500);
    }
    
    // Step 1: Request token
    requestForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('requestBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        
        try {
            userEmail = document.getElementById('email').value;
            const response = await fetch('{{ route("password.email") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email: userEmail })
            });
            
            const data = await response.json();
            
            if (data.status === 'success') {
                showToast(data.message, 'success');
                resetEmail.value = userEmail;
                showStep(2);
                setTimeout(() => tokenBoxes[0].focus(), 100);
            } else {
                showToast(data.message, 'error');
            }
        } catch (error) {
            showToast('An error occurred. Please try again.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Send Reset Token';
        }
    });
    
    // Step 2: Verify token
    verifyForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (tokenInput.value.length !== 6) {
            showToast('Please enter the complete 6-digit token', 'error');
            showTokenError();
            return;
        }
        
        const btn = document.getElementById('verifyBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
        
        try {
            // Verify token with backend
            const response = await fetch('{{ route("password.reset") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email: userEmail,
                    token: tokenInput.value,
                    verify_only: true
                })
            });
            
            const data = await response.json();
            
            if (data.status === 'success' || data.status === 'token_valid') {
                showToast('Token verified successfully', 'success');
                verifiedToken.value = tokenInput.value;
                showStep(3);
            } else {
                showToast(data.message || 'Invalid token', 'error');
                showTokenError();
            }
        } catch (error) {
            showToast('An error occurred. Please try again.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Verify Token';
        }
    });
    
    // Step 3: Reset password
    resetForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        
        if (password !== confirmPassword) {
            showToast('Passwords do not match', 'error');
            return;
        }
        
        if (password.length < 6) {
            showToast('Password must be at least 6 characters', 'error');
            return;
        }
        
        const btn = document.getElementById('resetBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
        
        try {
            const response = await fetch('{{ route("password.reset") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email: resetEmail.value,
                    token: verifiedToken.value,
                    password: password,
                    password_confirmation: confirmPassword
                })
            });
            
            const data = await response.json();
            
            if (data.status === 'success') {
                showToast('Password updated successfully! Redirecting...', 'success');
                setTimeout(() => {
                    window.location.href = '{{ route("login") }}';
                }, 2000);
            } else {
                showToast(data.message || 'Failed to update password', 'error');
            }
        } catch (error) {
            showToast('An error occurred. Please try again.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Update Password';
        }
    });
    
    // Back buttons
    document.getElementById('backToStep1').addEventListener('click', function() {
        clearTokenInputs();
        showStep(1);
    });
    
    document.getElementById('backToStep2').addEventListener('click', function() {
        showStep(1);
    });
});
</script>
@endpush
