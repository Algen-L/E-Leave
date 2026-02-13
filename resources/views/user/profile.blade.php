@extends('layouts.sdo')

@section('title', 'My Profile')
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
                <img src="{{ asset($user->profile_picture) }}" alt="{{ $user->full_name }}">
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
    </div>
    <div class="profile-banner-actions">
        <button type="button" class="banner-btn banner-btn-outline" onclick="toggleSection('accountInfoSection')">
            <i class="fas fa-user-circle"></i> Account Info
        </button>
        <button type="button" class="banner-btn banner-btn-white" onclick="toggleSection('editProfileSection')">
            <i class="fas fa-edit"></i> Edit Profile
        </button>
        <button type="button" class="banner-btn banner-btn-outline" onclick="toggleSection('changePasswordSection')">
            <i class="fas fa-lock"></i> Change Password
        </button>
    </div>
    <div class="profile-banner-decoration"></div>
</div>

<!-- Account Information (Read-only, hidden by default) -->
<div class="profile-section" id="accountInfoSection" style="display: none;">
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
        <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
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
                    <input type="text" class="field-input field-readonly" value="{{ $user->office_station }}" readonly>
                </div>
                <div class="form-group-custom">
                    <label class="field-label">SALARY</label>
                    <input type="text" class="field-input" name="salary" value="{{ old('salary', $user->salary) }}" placeholder="Enter monthly salary">
                </div>
            </div>

            <!-- Approver Selection Section -->
            <div class="mt-4 mb-4 pt-4 border-t border-gray-200">
                <h3 class="text-lg font-bold text-gray-700 mb-3 block uppercase text-xs font-bold tracking-wider text-slate-500">Choose your Recommending and Final Approver</h3>
                <div class="form-row-custom">
                    <div class="form-group-custom">
                        <label class="field-label">RECOMMENDING APPROVER</label>
                        <select class="field-input" name="recommending_officer_id">
                            <option value="">Select Recommender</option>
                            @foreach($recommendingOfficers as $officer)
                                <option value="{{ $officer->id }}" {{ (old('recommending_officer_id', $user->recommending_officer_id) == $officer->id) ? 'selected' : '' }}>
                                    {{ $officer->full_name }} ({{ strtoupper($officer->role) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group-custom">
                        <label class="field-label">FINAL APPROVER</label>
                        <select class="field-input" name="approving_officer_id">
                            <option value="">Select Final Approver</option>
                            @foreach($finalApprovers as $officer)
                                <option value="{{ $officer->id }}" {{ (old('approving_officer_id', $user->approving_officer_id) == $officer->id) ? 'selected' : '' }}>
                                    {{ $officer->full_name }} ({{ strtoupper($officer->role) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group-custom">
                <label class="field-label">PROFILE PICTURE</label>
                <input type="file" class="field-input" name="profile_picture" accept="image/*">
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
        <form id="passwordForm">
            
            <div class="form-row-custom">
                <div class="form-group-custom">
                    <label class="field-label">NEW PASSWORD</label>
                    <input type="password" class="field-input" name="password" id="newPassword" placeholder="Enter new password" minlength="6" required>
                    <span class="field-hint">Minimum 6 characters</span>
                </div>
                <div class="form-group-custom">
                    <label class="field-label">CONFIRM NEW PASSWORD</label>
                    <input type="password" class="field-input" name="password_confirmation" id="confirmPassword" placeholder="Confirm new password" minlength="6" required>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="toggleSection('changePasswordSection')">Cancel</button>
                <button type="button" class="btn-save" onclick="initiatePasswordChange()">
                    <i class="fas fa-save"></i> Update Password
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal for Verification -->
<div class="custom-modal-overlay" id="verificationModal">
    <div class="custom-modal">
        <div class="custom-modal-header">
            <h3><i class="fas fa-shield-alt"></i> Security Verification</h3>
            <button type="button" class="custom-modal-close" onclick="closeModal('verificationModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="custom-modal-body">
            <p class="modal-info-text">
                To secure your account, we've sent a verification code to <strong>{{ $user->gmail }}</strong>. 
                Please enter it below to confirm your password change.
            </p>
            <div class="form-group-custom">
                <label class="field-label" style="text-align: center;">VERIFICATION CODE</label>
                <div class="verification-input-wrapper">
                    <input type="text" class="field-input verification-input" id="verificationCode" placeholder="Enter 6-digit code" maxlength="6">
                </div>
            </div>
        </div>
        <div class="custom-modal-footer">
            <button type="button" class="btn-cancel" onclick="closeModal('verificationModal')">Cancel</button>
            <button type="button" class="btn-save" onclick="submitPasswordChange()">
                <i class="fas fa-check-circle"></i> Verify & Update
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function initiatePasswordChange() {
    const password = document.getElementById('newPassword').value;
    const confirm = document.getElementById('confirmPassword').value;

    if (!password || !confirm) {
        showToast('Please fill in both password fields.', 'warning');
        return;
    }

    if (password.length < 6) {
        showToast('Password must be at least 6 characters.', 'warning');
        return;
    }

    if (password !== confirm) {
        showToast('Passwords do not match.', 'error');
        return;
    }

    // Request Token
    const btn = document.querySelector('#passwordForm .btn-save');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending Code...';
    btn.disabled = true;

    fetch('{{ route("user.profile.password.request-token") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        if (data.status === 'success') {
            document.getElementById('verificationModal').style.display = 'flex';
            document.getElementById('verificationCode').focus();
            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'error');
    });
}

function submitPasswordChange() {
    const code = document.getElementById('verificationCode').value;
    const password = document.getElementById('newPassword').value;
    const password_confirmation = document.getElementById('confirmPassword').value;

    if (!code) {
        showToast('Please enter the verification code.', 'warning');
        return;
    }

    const btn = document.querySelector('#verificationModal .btn-save');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
    btn.disabled = true;

    fetch('{{ route("user.profile.password.update") }}', {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            password: password,
            password_confirmation: password_confirmation,
            token: code
        })
    })
    .then(response => response.json())
    .then(data => {
        btn.innerHTML = originalText;
        btn.disabled = false;

        if (data.status === 'success') {
            document.getElementById('verificationModal').style.display = 'none';
            showToast(data.message, 'success');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'error');
    });
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

function toggleSection(id) {
    const section = document.getElementById(id);
    if (section.style.display === 'none') {
        // Hide other sections first
        document.getElementById('accountInfoSection').style.display = 'none';
        document.getElementById('editProfileSection').style.display = 'none';
        document.getElementById('changePasswordSection').style.display = 'none';
        // Show this one
        section.style.display = 'block';
        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        section.style.display = 'none';
    }
}
</script>
@endpush
