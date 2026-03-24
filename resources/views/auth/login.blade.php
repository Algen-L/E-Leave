@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="login-container">
    <div class="header">
        <div class="logo-container">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </div>
        <h1>E-Leave Application System</h1>
        <p>San Pedro Division Office</p>
    </div>

    <!-- Login Form -->
    <div id="login-section" class="form-section active">
        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <!-- Session Status / Errors -->
            @if (session('success'))
                <div class="alert-success">
                    <i class="fas fa-check-circle"></i>{{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    @if ($errors->has('login'))
                        {{ $errors->first('login') }}
                    @else
                        Invalid credentials provided
                    @endif
                </div>
            @endif

            <div class="form-group">
                <label for="email">Gmail Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="example@deped.gov.ph" value="{{ old('email') }}" required autofocus autocomplete="email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn">Sign In</button>

            <div class="forgot-password-container">
                <a href="{{ route('password.request') }}" class="toggle-link">Forgot Password?</a>
            </div>
        </form>

        <div class="footer-text">
            Don't have an account? <a href="{{ route('register') }}" class="toggle-link">Register here</a>
        </div>
    </div>
    
    <div class="auth-footer">
        <div class="dev-info">Department of Education - Schools Division Office of San Pedro City</div>
        <div class="dev-info">Developed by Algen Loveres & Cedrick Bacaresas</div>
    </div>
</div>
@endsection
