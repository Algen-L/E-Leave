@extends('layouts.sdo')

@section('title', 'Register User')
@section('page-title', 'Register New User')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/register-user.css') }}">
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
@endpush

@section('content')
<div class="register-container">
    <!-- Avatar Side Panel -->
    <div class="avatar-panel">
        <div class="avatar-card">
            <div class="avatar-wrapper">
                <div class="avatar-placeholder">
                    <i class="fas fa-user"></i>
                </div>
                <label class="avatar-upload-btn" for="profile_picture">
                    <i class="fas fa-camera"></i>
                </label>
            </div>
            <h3>Profile Photo</h3>
            <p>Upload a profile picture for this user account</p>
        </div>
    </div>
    
    <!-- Form Panel -->
    <div class="form-panel">
        <form action="{{ route('admin.register-user.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" id="profile_picture" name="profile_picture" accept="image/*" style="display: none;">
            
            <!-- Account Information -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h4 class="section-title">Account Information</h4>
                </div>
                
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Username <span class="required">*</span></label>
                        <input type="text" class="form-control @error('username') error @enderror" name="username" value="{{ old('username') }}" placeholder="Enter username" required>
                        @error('username')
                            <div class="input-feedback error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Password <span class="required">*</span></label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control @error('password') error @enderror" name="password" id="password" placeholder="Enter password" minlength="6" required>
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="input-feedback error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Role <span class="required">*</span></label>
                        <select class="form-select @error('role') error @enderror" name="role" required>
                            <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="hr" {{ old('role') === 'hr' ? 'selected' : '' }}>HR</option>
                            <option value="head_hr" {{ old('role') === 'head_hr' ? 'selected' : '' }}>Head HR</option>
                            <option value="immediate_head" {{ old('role') === 'immediate_head' ? 'selected' : '' }}>Immediate Head</option>
                            @if(auth()->user()->role === 'super_admin')
                                <optgroup label="High Level Roles">
                                    <option value="asds" {{ old('role') === 'asds' ? 'selected' : '' }}>ASDS</option>
                                    <option value="sds" {{ old('role') === 'sds' ? 'selected' : '' }}>SDS</option>
                                    <option value="sgod_chief" {{ old('role') === 'sgod_chief' ? 'selected' : '' }}>SGOD Chief</option>
                                    <option value="cid_chief" {{ old('role') === 'cid_chief' ? 'selected' : '' }}>CID Chief</option>
                                    <option value="ao" {{ old('role') === 'ao' ? 'selected' : '' }}>AO</option>
                                </optgroup>
                            @endif
                        </select>
                        @error('role')
                            <div class="input-feedback error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Personal Information -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h4 class="section-title">Personal Information</h4>
                </div>
                
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">First Name <span class="required">*</span></label>
                        <input type="text" class="form-control @error('first_name') error @enderror" name="first_name" value="{{ old('first_name') }}" placeholder="Enter first name" required>
                        @error('first_name')
                            <div class="input-feedback error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Middle Name</label>
                        <input type="text" class="form-control @error('middle_name') error @enderror" name="middle_name" value="{{ old('middle_name') }}" placeholder="Enter middle name">
                        @error('middle_name')
                            <div class="input-feedback error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Last Name <span class="required">*</span></label>
                        <input type="text" class="form-control @error('last_name') error @enderror" name="last_name" value="{{ old('last_name') }}" placeholder="Enter last name" required>
                        @error('last_name')
                            <div class="input-feedback error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email (Gmail)</label>
                        <input type="email" class="form-control @error('gmail') error @enderror" name="gmail" value="{{ old('gmail') }}" placeholder="Enter email address">
                        @error('gmail')
                            <div class="input-feedback error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Work Information -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h4 class="section-title">Work Information</h4>
                </div>
                
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Office/Station</label>
                        <select class="form-select" id="office_station" name="office_station">
                            <option value="">Select Office</option>
                            @foreach($offices as $category => $officeList)
                                <optgroup label="{{ $category }}">
                                    @foreach($officeList as $office)
                                        <option value="{{ $office->name }}" {{ old('office_station') === $office->name ? 'selected' : '' }}>
                                            {{ $office->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Position</label>
                        <input type="text" class="form-control" name="position" value="{{ old('position') }}" placeholder="Enter position/title">
                    </div>
                </div>
                
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <p>The password must be at least 6 characters. Users can change their password after logging in.</p>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('admin.manage-users') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Cancel
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-user-plus"></i>
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new TomSelect('#office_station', {
        allowEmptyOption: true,
        placeholder: 'Select Office/Station'
    });
});

function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>
@endpush