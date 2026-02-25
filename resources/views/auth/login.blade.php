@extends('layouts.auth')

@section('title', 'Login')

@push('styles')
    <style>
        .login-card {
            background: #1e293b;
            border-radius: 16px;
            padding: 30px;
            width: 100%;
            max-width: 380px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 10;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-logo img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .login-title {
            text-align: center;
            margin-bottom: 4px;
        }

        .login-title h1 {
            color: #f8fafc;
            font-size: 1.4rem;
            font-weight: 700;
            font-style: italic;
            margin: 0;
        }

        .login-subtitle {
            text-align: center;
            color: #94a3b8;
            font-size: 0.85rem;
            margin-bottom: 24px;
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
            padding: 12px 14px;
            color: #f8fafc;
            font-size: 0.9rem;
            margin-bottom: 16px;
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

        .btn-signin {
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
        }

        .btn-signin:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.4);
        }

        .forgot-link {
            display: block;
            text-align: right;
            color: #0d9488;
            font-size: 0.8rem;
            text-decoration: none;
            margin-bottom: 20px;
        }

        .forgot-link:hover {
            text-decoration: underline;
            color: #14b8a6;
        }

        .register-text {
            text-align: center;
            color: #94a3b8;
            font-size: 0.85rem;
            margin-bottom: 0px;
        }

        .register-text a {
            color: #f8fafc;
            font-weight: 600;
            text-decoration: underline;
        }

        .register-text a:hover {
            color: #0d9488;
        }

        .alert-box {
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 0.85rem;
        }

        .alert-box.success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10b981;
        }

        .alert-box.error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
        }
    </style>
@endpush

@section('content')
    <div class="login-card">
        <div class="login-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" onerror="this.src='https://via.placeholder.com/80'">
        </div>

        <div class="login-title">
            <h1>E-Leave Application System</h1>
        </div>
        <p class="login-subtitle">Login to your account</p>

        @if(session('success'))
            <div class="alert-box success">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if($errors->has('login'))
            <div class="alert-box error">
                <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first('login') }}
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <label class="form-label-custom">DepED Address</label>
            <input type="email" class="form-input-custom" name="email" placeholder="Enter your email"
                value="{{ old('email') }}" required>

            <label class="form-label-custom">Password</label>
            <input type="password" class="form-input-custom" name="password" placeholder="Enter your password" required>

            <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>

            <button type="submit" class="btn-signin">Sign In</button>
        </form>

        <p class="register-text">
            Don't have an account? <a href="{{ route('register') }}">Register here</a>
        </p>
    </div>
@endsection