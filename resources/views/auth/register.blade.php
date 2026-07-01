@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<div class="login-container register-mode" id="authContainer">
    <div class="header">
        <div class="logo-container">
            <img src="{{ asset('images/logo.png') }}" alt="SDO Logo">
        </div>
        <h1 id="authTitle">Create Account</h1>
        <p id="authSubtitle">Fill in your details to get started</p>
    </div>

    <!-- STEP 1: Personal Details -->
    <div id="regSection" class="form-section active">
        <form id="registerForm" class="ajax-auth-form">
            @csrf
            <div id="regStep1">
                <div class="form-grid grid-3">
                    <div class="form-group">
                        <label>First Name <span class="required-asterisk">*</span></label>
                        <input type="text" name="first_name" id="reg_first_name" class="form-control" placeholder="First Name" required>
                    </div>
                    <div class="form-group">
                        <label>Middle Name</label>
                        <input type="text" name="middle_name" id="reg_middle_name" class="form-control" placeholder="Middle Name">
                    </div>
                    <div class="form-group">
                        <label>Last Name <span class="required-asterisk">*</span></label>
                        <input type="text" name="last_name" id="reg_last_name" class="form-control" placeholder="Last Name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Employee Number <span class="required-asterisk">*</span></label>
                    <input type="text" name="employee_number" id="employee_number" class="form-control" placeholder="Employee Number" maxlength="7" inputmode="numeric" required>
                </div>

                <div class="form-group">
                    <label>Office / Station <span class="required-asterisk">*</span></label>
                    <select name="office_station" id="office_select" class="form-control" required>
                        <option value="">Select your office...</option>
                        @foreach($offices as $category => $items)
                            <optgroup label="{{ $category }}">
                                @foreach($items as $office)
                                    <option value="{{ $office->name }}">{{ $office->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="form-grid grid-3">
                    <div class="form-group">
                        <label>Position <span class="required-asterisk">*</span></label>
                        <input type="text" name="position" id="position" class="form-control" placeholder="Position" required>
                    </div>
                    <div class="form-group">
                        <label>Age <span class="required-asterisk">*</span></label>
                        <input type="number" name="age" id="reg_age" class="form-control" placeholder="Age" min="18" max="100" required>
                    </div>
                    <div class="form-group">
                        <label>Sex <span class="required-asterisk">*</span></label>
                        <select name="sex" id="reg_sex" class="form-control" required>
                            <option value="">Select...</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>

                <div class="credentials-group-header" style="margin: 8px 0 4px; border-top: 1px solid var(--border-color); padding-top: 8px;">
                    <h4 style="font-size: 0.75rem; color: var(--primary-blue); font-weight: 700; margin-bottom: 2px; text-transform: uppercase;">
                        <i class="fas fa-key" style="margin-right: 8px;"></i> Account Credentials
                    </h4>
                    <p style="font-size: 0.62rem; color: var(--text-muted); margin-bottom: 6px;">
                        The email and password you provide here will be strictly used for logging into your account.
                    </p>
                </div>

                <div class="form-group">
                    <label>Email Address <span class="required-asterisk">*</span></label>
                    <input type="email" name="gmail" id="reg_email" class="form-control" placeholder="example@deped.gov.ph" required>
                    <div style="margin-top: 8px; color: var(--text-muted); font-size: 0.7rem; font-style: italic;">
                        *A valid DepEd or active email address is required for account notifications and login.
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Password <span class="required-asterisk">*</span></label>
                        <input type="password" name="password" id="reg_password" class="form-control" placeholder="Password" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Confirm Password <span class="required-asterisk">*</span></label>
                        <input type="password" name="password_confirmation" id="reg_password_confirmation" class="form-control" placeholder="Confirm Password" required minlength="6">
                    </div>
                </div>





                <button type="submit" class="btn" id="registerBtn">Next: Verify Identity</button>
            </div>

            <!-- STEP 2: Email Verification -->
            <div id="regStep2" style="display: none;">
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 20px; text-align: center;">
                    We've sent a 6-digit verification code to <strong id="display_reg_email" style="color: var(--primary-blue);"></strong>. Please enter it below to complete your registration.
                </p>
                <div class="form-group">
                    <label>Verification Code <span class="required-asterisk">*</span></label>
                    <div class="token-boxes">
                        <input type="text" class="token-box reg-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" placeholder="-">
                        <input type="text" class="token-box reg-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" placeholder="-">
                        <input type="text" class="token-box reg-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" placeholder="-">
                        <input type="text" class="token-box reg-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" placeholder="-">
                        <input type="text" class="token-box reg-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" placeholder="-">
                        <input type="text" class="token-box reg-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" placeholder="-">
                    </div>
                    <input type="hidden" id="reg_verification_code">
                </div>
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="button" class="btn" style="flex: 1; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); color: var(--text-muted);" onclick="backToStep1()">Back</button>
                    <button type="button" class="btn" id="verifyRegBtn" onclick="verifyRegistrationCode()" style="flex: 2;">Verify & Register</button>
                </div>
            </div>
        </form>
        <div class="footer-text">
            Already have an account? <a href="{{ route('login') }}" class="toggle-link">Back to Login</a>
        </div>

        <div class="auth-footer">
            <div class="dev-info">Department of Education - Schools Division Office of San Pedro City</div>
            <div class="dev-info">ICT UNIT 2026</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize TomSelect for office dropdown
        const officeSelect = new TomSelect('#office_select', {
            create: false,
            placeholder: "Type to search office...",
            maxOptions: 50
        });

        // Handle Registration Form Submission (Step 1)
        const registerForm = document.getElementById('registerForm');
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('registerBtn');
            const originalText = btn.innerHTML;
            const email = document.getElementById('reg_email').value.trim();

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending code...';

            const formData = new FormData(registerForm);

            fetch('{{ route("register.request-code") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    document.getElementById('display_reg_email').innerText = email;
                    document.getElementById('regStep1').style.display = 'none';
                    document.getElementById('regStep2').style.display = 'block';
                    document.getElementById('regStep2').classList.add('animate__animated', 'animate__fadeIn');
                } else {
                    showToast(data.message || "Request failed.", 'error');
                }
            })
            .catch(error => showToast("An error occurred. Please try again.", 'error'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });

        // Handle Token Box Inputs
        const digits = document.querySelectorAll('.reg-digit');
        digits.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                input.value = input.value.replace(/[^0-9]/g, '');
                if (input.value.length === 1 && index < digits.length - 1) {
                    digits[index + 1].focus();
                }
                updateHiddenCode();
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    digits[index - 1].focus();
                }
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasteData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
                pasteData.split('').forEach((char, i) => {
                    if (digits[i]) digits[i].value = char;
                });
                if (pasteData.length > 0) digits[Math.min(pasteData.length, 5)].focus();
                updateHiddenCode();
            });
        });

        function updateHiddenCode() {
            let code = '';
            digits.forEach(d => code += d.value);
            document.getElementById('reg_verification_code').value = code;
        }
    });

    function backToStep1() {
        document.getElementById('regStep1').style.display = 'block';
        document.getElementById('regStep2').style.display = 'none';
        document.getElementById('regStep1').classList.add('animate__animated', 'animate__fadeIn');
    }

    function verifyRegistrationCode() {
        const code = document.getElementById('reg_verification_code').value;
        const btn = document.getElementById('verifyRegBtn');
        const originalText = btn.innerHTML;

        if (code.length !== 6) {
            showToast("Please enter the 6-digit verification code.", 'error');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';

        fetch('{{ route("register.verify-code") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ code: code })
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
                showToast(data.message || "Verification failed.", 'error');
                if (data.status === 'attempts_exceeded') {
                    backToStep1();
                    document.querySelectorAll('.reg-digit').forEach(d => d.value = '');
                    document.getElementById('reg_verification_code').value = '';
                }
            }
        })
        .catch(error => showToast("An error occurred. Please try again.", 'error'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }
</script>
@endpush
