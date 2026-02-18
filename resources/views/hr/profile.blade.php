@extends('layouts.sdo')

@section('title', 'HR Profile')
@section('page-title', 'My Profile')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-profile.css') }}">
@endpush

@section('content')
<!-- Profile Banner -->
<div class="profile-banner">
    <div class="profile-banner-content">
        <div class="profile-banner-avatar">
            @if($user->profile_picture)
                <img src="{{ storage_url($user->profile_picture) }}" alt="{{ $user->full_name }}">
            @else
                <div class="banner-avatar-placeholder">
                    {{ strtoupper(substr($user->full_name, 0, 1)) }}
                </div>
            @endif
        </div>
        <div class="profile-banner-info">
            <h1 class="banner-name">{{ $user->full_name }}</h1>
            <div class="banner-meta">
                <span><i class="fas fa-id-badge"></i> {{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                <span class="banner-divider">&bull;</span>
                <span><i class="fas fa-building"></i> {{ $user->office_station ?: 'No office set' }}</span>
            </div>
        </div>
        <div class="profile-banner-actions">
            <button type="button" class="banner-btn banner-btn-white" onclick="toggleSection('editProfileSection')">
                <i class="fas fa-edit"></i> Edit Profile
            </button>
            <button type="button" class="banner-btn banner-btn-outline" onclick="toggleSection('changePasswordSection')">
                <i class="fas fa-lock"></i> Change Password
            </button>
        </div>
    </div>
    <div class="profile-banner-decoration"></div>
</div>

<!-- Account Information (Read-only) -->
<div class="profile-section">
    <div class="section-header">
        <h2><i class="fas fa-user-circle"></i> Account Information</h2>
    </div>
    <div class="section-body">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-user"></i></div>
                <div class="info-content">
                    <div class="info-label">Full Name</div>
                    <div class="info-value">{{ $user->full_name }}</div>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-envelope"></i></div>
                <div class="info-content">
                    <div class="info-label">Gmail Address</div>
                    <div class="info-value">{{ $user->gmail ?: 'Not set' }}</div>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-briefcase"></i></div>
                <div class="info-content">
                    <div class="info-label">Position</div>
                    <div class="info-value">{{ $user->position ?: 'Not set' }}</div>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-building"></i></div>
                <div class="info-content">
                    <div class="info-label">Office / Station</div>
                    <div class="info-value">{{ $user->office_station ?: 'Not assigned' }}</div>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="info-content">
                    <div class="info-label">Member Since</div>
                    <div class="info-value">{{ $user->created_at->format('F d, Y') }}</div>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-shield-alt"></i></div>
                <div class="info-content">
                    <div class="info-label">Account Status</div>
                    <div class="info-value" style="color: {{ $user->is_active ? '#22c55e' : '#dc2626' }}; font-weight: 700;">
                        <i class="fas fa-circle" style="font-size: 0.5rem; vertical-align: middle; margin-right: 4px;"></i>
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Section (Hidden by default) -->
<div class="profile-section" id="editProfileSection" style="display: none;">
    <div class="section-header">
        <h2><i class="fas fa-edit"></i> Edit Profile</h2>
    </div>
    <div class="section-body">
        <form action="{{ route('hr.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-row-custom">
                <div class="form-group-custom">
                    <label class="field-label">FULL NAME</label>
                    <input type="text" class="field-input" name="full_name" value="{{ $user->full_name }}" required>
                </div>
                <div class="form-group-custom">
                    <label class="field-label">POSITION</label>
                    <input type="text" class="field-input" name="position" value="{{ $user->position }}">
                </div>
            </div>
            
            <div class="form-row-custom">
                <div class="form-group-custom">
                    <label class="field-label">OFFICE / STATION</label>
                    <input type="text" class="field-input" name="office_station" value="{{ $user->office_station }}">
                </div>
                <div class="form-group-custom">
                    <label class="field-label">PROFILE PICTURE</label>
                    <input type="file" class="field-input" name="profile_picture" accept="image/*">
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="toggleSection('editProfileSection')">Cancel</button>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Change Password Section (Hidden by default) -->
<div class="profile-section" id="changePasswordSection" style="display: none;">
    <div class="section-header">
        <h2><i class="fas fa-lock"></i> Change Password</h2>
    </div>
    <div class="section-body">
        <form action="{{ route('hr.profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-row-custom">
                <div class="form-group-custom">
                    <label class="field-label">NEW PASSWORD</label>
                    <input type="password" class="field-input" name="password" placeholder="Enter new password" minlength="6" required>
                    <span class="field-hint">Minimum 6 characters</span>
                </div>
                <div class="form-group-custom">
                    <label class="field-label">CONFIRM NEW PASSWORD</label>
                    <input type="password" class="field-input" name="password_confirmation" placeholder="Confirm new password" minlength="6" required>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="toggleSection('changePasswordSection')">Cancel</button>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleSection(id) {
    const section = document.getElementById(id);
    if (section.style.display === 'none') {
        document.getElementById('editProfileSection').style.display = 'none';
        document.getElementById('changePasswordSection').style.display = 'none';
        section.style.display = 'block';
        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        section.style.display = 'none';
    }
}
</script>
@endpush