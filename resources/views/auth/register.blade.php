@extends('layouts.auth')

@section('title', 'Register')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .register-card {
            background: #1e293b;
            border-radius: 16px;
            padding: 30px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 10;
        }

        .register-logo {
            text-align: center;
            margin-bottom: 12px;
        }

        .register-logo img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .register-title {
            text-align: center;
            margin-bottom: 2px;
        }

        .register-title h1 {
            color: #0d9488;
            font-size: 1.25rem;
            font-weight: 700;
            font-style: italic;
            margin: 0;
        }

        .register-subtitle {
            text-align: center;
            color: #94a3b8;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }

        .form-label-custom {
            display: block;
            color: #94a3b8;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
        }

        .form-input-custom {
            width: 100%;
            background: #334155;
            border: none;
            border-radius: 8px;
            padding: 10px 14px;
            color: #f8fafc;
            font-size: 0.9rem;
            margin-bottom: 12px;
            transition: all 0.2s;
            box-sizing: border-box;
        }

        .form-input-custom::placeholder {
            color: #64748b;
        }

        .form-input-custom:focus {
            outline: none;
            box-shadow: 0 0 0 2px #0d9488;
            background: #3d4f66;
        }

        .form-row {
            display: flex;
            gap: 12px;
        }

        .form-col {
            flex: 1;
        }

        .btn-register {
            width: 100%;
            background: linear-gradient(135deg, #0f4c5c 0%, #0d9488 100%);
            border: none;
            border-radius: 8px;
            padding: 12px;
            color: white;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 16px;
            margin-top: 4px;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.4);
        }

        .btn-register:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .login-text {
            text-align: center;
            color: #94a3b8;
            font-size: 0.85rem;
            margin-bottom: 0px;
        }

        .login-text a {
            color: #f8fafc;
            font-weight: 600;
            text-decoration: none;
        }

        .login-text a:hover {
            text-decoration: underline;
        }

        /* Tom Select styles */
        .ts-wrapper.form-select-custom .ts-control {
            background: #334155;
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            color: #f8fafc;
            font-size: 0.95rem;
            min-height: 48px;
        }

        .ts-wrapper.form-select-custom .ts-control input {
            color: #f8fafc;
        }

        .ts-wrapper.form-select-custom .ts-control::placeholder {
            color: #64748b;
        }

        .ts-wrapper.form-select-custom.focus .ts-control {
            box-shadow: 0 0 0 2px #0d9488;
            background: #3d4f66;
        }

        .ts-wrapper.form-select-custom .ts-dropdown {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 8px;
        }

        .ts-wrapper.form-select-custom .ts-dropdown .option {
            color: #f8fafc;
            padding: 10px 16px;
        }

        .ts-wrapper.form-select-custom .ts-dropdown .option:hover,
        .ts-wrapper.form-select-custom .ts-dropdown .option.active {
            background: #334155;
            color: #0d9488;
        }

        .ts-wrapper.form-select-custom .ts-dropdown .optgroup-header {
            color: #0d9488;
            font-weight: 600;
            padding: 10px 16px 5px;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        /* Verification step styles */
        .code-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 24px;
        }

        .code-digit {
            width: 48px;
            height: 56px;
            background: #334155;
            border: none;
            border-radius: 8px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
            color: #f8fafc;
            transition: all 0.2s;
        }

        .code-digit:focus {
            outline: none;
            box-shadow: 0 0 0 2px #0d9488;
            background: #3d4f66;
        }

        .step-description {
            color: #f8fafc;
            text-align: center;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        .btn-back {
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: underline;
        }

        .btn-back:hover {
            color: #0d9488;
        }
    </style>
@endpush

@section('content')
    <div class="register-card">
        <div class="register-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" onerror="this.src='https://via.placeholder.com/80'">
        </div>

        <div class="register-title">
            <h1>Create Account</h1>
        </div>
        <p class="register-subtitle">Fill in your details</p>

        <!-- Step 1: Registration Form -->
        <div id="registrationStep">
            <form id="registerForm">
                @csrf

                <div class="form-row">
                    <div class="form-col">
                        <label class="form-label-custom">First Name</label>
                        <input type="text" class="form-input-custom" id="first_name" name="first_name" placeholder="John"
                            required>
                    </div>
                    <div class="form-col">
                        <label class="form-label-custom">Middle Name</label>
                        <input type="text" class="form-input-custom" id="middle_name" name="middle_name"
                            placeholder="Optional">
                    </div>
                </div>

                <label class="form-label-custom">Last Name</label>
                <input type="text" class="form-input-custom" id="last_name" name="last_name" placeholder="Doe" required>

                <div class="form-row">
                    <div class="form-col">
                        <label class="form-label-custom">Username</label>
                        <input type="text" class="form-input-custom" id="username" name="username" placeholder="admin"
                            required>
                    </div>
                    <div class="form-col">
                        <label class="form-label-custom">Password</label>
                        <input type="password" class="form-input-custom" id="password" name="password"
                            placeholder="••••••••" minlength="6" required>
                    </div>
                </div>

                <label class="form-label-custom">Office / Station</label>
                <select class="form-select-custom" id="office_station" name="office_station">
                    <option value="">Select your office...</option>
                    @foreach($offices as $category => $officeList)
                        <optgroup label="{{ $category }}">
                            @foreach($officeList as $office)
                                <option value="{{ $office->name }}">{{ $office->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>

                <div class="form-row" style="margin-top: 16px;">
                    <div class="form-col">
                        <label class="form-label-custom">Employee No.</label>
                        <input type="text" class="form-input-custom" id="employee_number" name="employee_number"
                            placeholder="1234567" pattern="\d{7}" maxlength="7" title="Must be exactly 7 digits" required>
                    </div>
                    <div class="form-col">
                        <label class="form-label-custom">Gmail Address</label>
                        <input type="email" class="form-input-custom" id="gmail" name="gmail"
                            placeholder="example@gmail.com" required>
                    </div>
                </div>

                <button type="submit" class="btn-register" id="requestCodeBtn">Next: Verify Email</button>
            </form>

            <p class="login-text">
                Already have an account? <a href="{{ route('login') }}">Back to Login</a>
            </p>
        </div>

        <!-- Step 2: Verification -->
        <div id="verificationStep" style="display: none;">
            <p class="step-description">Enter the 6-digit code sent to your email</p>

            <form id="verifyForm">
                @csrf
                <div class="code-container">
                    <input type="text" class="code-digit" maxlength="1" data-index="0">
                    <input type="text" class="code-digit" maxlength="1" data-index="1">
                    <input type="text" class="code-digit" maxlength="1" data-index="2">
                    <input type="text" class="code-digit" maxlength="1" data-index="3">
                    <input type="text" class="code-digit" maxlength="1" data-index="4">
                    <input type="text" class="code-digit" maxlength="1" data-index="5">
                </div>
                <input type="hidden" id="codeInput" name="code">

                <button type="submit" class="btn-register" id="verifyBtn">Verify & Register</button>
            </form>

            <div class="text-center">
                <button type="button" class="btn-back" id="backBtn">
                    <i class="fas fa-arrow-left me-1"></i> Go Back
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const registerForm = document.getElementById('registerForm');
            const verifyForm = document.getElementById('verifyForm');
            const registrationStep = document.getElementById('registrationStep');
            const verificationStep = document.getElementById('verificationStep');
            const codeDigits = document.querySelectorAll('.code-digit');
            const codeInput = document.getElementById('codeInput');
            const backBtn = document.getElementById('backBtn');

            // Initialize Tom Select for office dropdown
            if (document.getElementById('office_station')) {
                new TomSelect('#office_station', {
                    allowEmptyOption: true,
                    placeholder: 'Select your office...'
                });
            }

            // Handle code digit inputs
            codeDigits.forEach((digit, index) => {
                digit.addEventListener('input', function () {
                    if (this.value.length === 1 && index < codeDigits.length - 1) {
                        codeDigits[index + 1].focus();
                    }
                    updateCodeInput();
                });

                digit.addEventListener('keydown', function (e) {
                    if (e.key === 'Backspace' && !this.value && index > 0) {
                        codeDigits[index - 1].focus();
                    }
                });
            });

            function updateCodeInput() {
                let code = '';
                codeDigits.forEach(digit => code += digit.value);
                codeInput.value = code;
            }

            // Request registration code
            registerForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                const btn = document.getElementById('requestCodeBtn');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';

                try {
                    const formData = new FormData(registerForm);
                    const response = await fetch('{{ route("register.request-code") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        showToast(data.message, 'success');
                        registrationStep.style.display = 'none';
                        verificationStep.style.display = 'block';
                        codeDigits[0].focus();
                    } else {
                        showToast(data.message, 'error');
                    }
                } catch (error) {
                    showToast('An error occurred. Please try again.', 'error');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = 'Next: Verify Email';
                }
            });

            // Verify code
            verifyForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                const btn = document.getElementById('verifyBtn');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verifying...';

                try {
                    const response = await fetch('{{ route("register.verify-code") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ code: codeInput.value })
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        showToast(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = '{{ route("login") }}';
                        }, 2000);
                    } else {
                        showToast(data.message, 'error');
                        if (data.status === 'attempts_exceeded') {
                            registrationStep.style.display = 'block';
                            verificationStep.style.display = 'none';
                            registerForm.reset();
                        }
                    }
                } catch (error) {
                    showToast('An error occurred. Please try again.', 'error');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = 'Verify & Register';
                }
            });

            // Back button
            backBtn.addEventListener('click', function () {
                registrationStep.style.display = 'block';
                verificationStep.style.display = 'none';
                codeDigits.forEach(digit => digit.value = '');
            });
        });
    </script>
@endpush