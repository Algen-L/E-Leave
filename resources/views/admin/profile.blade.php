@extends('layouts.sdo')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@section('content')
<div class="profile-container">
    <!-- Profile Hero Section -->
    <div class="profile-hero">
        <div class="profile-hero-content">
            <div class="profile-avatar-large">
                @if($user->profile_picture)
                    <img src="{{ asset($user->profile_picture) }}" alt="{{ $user->full_name }}">
                @else
                    <div class="profile-avatar-placeholder">
                        {{ strtoupper(substr($user->full_name, 0, 2)) }}
                    </div>
                @endif
            </div>
            
            <div class="profile-hero-info">
                <h1 class="profile-name">
                    {{ $user->full_name }}
                    @if($user->is_active)
                        <span class="verified-badge"><i class="fas fa-check"></i></span>
                    @endif
                </h1>
                <div class="profile-position">{{ $user->position ?: 'No position set' }}</div>
                <div class="profile-department">
                    <i class="fas fa-building"></i>
                    {{ $user->office_station ?: 'No office assigned' }}
                </div>
            </div>
        </div>
        <div class="profile-hero-actions">
            <button type="button" class="hero-btn hero-btn-outline" onclick="toggleSection('accountInfoSection')" title="Account Info">
                <i class="fas fa-user-circle"></i>
            </button>
            <button type="button" class="hero-btn hero-btn-primary" onclick="openModal('editProfileModal')" title="Edit Profile">
                <i class="fas fa-edit"></i>
            </button>
            <button type="button" class="hero-btn hero-btn-outline" onclick="openModal('changePasswordModal')" title="Change Password">
                <i class="fas fa-lock"></i>
            </button>
            @if(in_array($user->role, ['super_admin', 'admin', 'head_hr']))
            <button type="button" class="hero-btn hero-btn-outline" onclick="toggleSection('broadcastSection')" title="Broadcast Notification">
                <i class="fas fa-bullhorn"></i>
            </button>
            @endif
        </div>
    </div>
    
    <!-- Account Information Section (hidden by default) -->
    <div id="accountInfoSection" style="display: none;">
        <!-- Personal Information Card -->
        <div class="profile-card mb-4">
            <div class="profile-card-header">
                <div class="profile-section-title">
                    <i class="fas fa-user"></i>
                    Personal Information
                </div>
            </div>
            <div class="profile-card-body">
                <div class="info-list">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-id-badge"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Username</div>
                            <div class="info-value">{{ $user->username }}</div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Email Address</div>
                            <div class="info-value">
                                @if($user->gmail)
                                    <a href="mailto:{{ $user->gmail }}">{{ $user->gmail }}</a>
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Office/Station</div>
                            <div class="info-value">{{ $user->office_station ?: 'Not assigned' }}</div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Position</div>
                            <div class="info-value">{{ $user->position ?: 'Not set' }}</div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Member Since</div>
                            <div class="info-value">{{ $user->created_at->format('F d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Broadcast Notification Section (hidden by default) -->
    @if(in_array($user->role, ['super_admin', 'admin', 'head_hr']))
    <div id="broadcastSection" style="display: none;">
        <div class="profile-card">
            <div class="profile-card-header">
                <div class="profile-section-title">
                    <i class="fas fa-bullhorn"></i>
                    Broadcast Notification
                </div>
            </div>
            <div class="profile-card-body">
                <form action="{{ route('admin.notifications.send') }}" method="POST">
                    @csrf
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Recipient</label>
                            <select class="form-select" name="recipient_id" required>
                                <option value="all">All Users</option>
                                @foreach($allUsers as $u)
                                    @if($u->id !== $user->id)
                                        <option value="{{ $u->id }}">{{ $u->full_name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group full-width">
                            <label class="form-label">Message</label>
                            <input type="text" class="form-control" name="message" maxlength="500" placeholder="Enter your notification message..." required>
                        </div>
                        
                        <div class="form-group full-width">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-paper-plane"></i>
                                Send Notification
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Edit Profile Modal -->
<div class="custom-modal-overlay" id="editProfileModal">
    <div class="custom-modal">
        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="custom-modal-header">
                <h5 class="custom-modal-title"><i class="fas fa-edit"></i> Edit Profile</h5>
                <button type="button" class="custom-modal-close" onclick="closeModal('editProfileModal')">&times;</button>
            </div>
            
            <div class="custom-modal-body">
                <div class="modal-form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="full_name" value="{{ $user->full_name }}" required>
                </div>
                
                <div class="modal-form-group">
                    <label class="form-label">Position</label>
                    <input type="text" class="form-control" name="position" value="{{ $user->position }}">
                </div>
                
                <div class="modal-form-group">
                    <label class="form-label">Office/Station</label>
                    <input type="text" class="form-control" name="office_station" value="{{ $user->office_station }}">
                </div>
                
                <div class="modal-form-group">
                    <label class="form-label">Profile Picture</label>
                    <input type="file" class="form-control" name="profile_picture" accept="image/*">
                </div>
            </div>
            
            <div class="custom-modal-footer">
                <button type="button" class="modal-btn modal-btn-secondary" onclick="closeModal('editProfileModal')">Cancel</button>
                <button type="submit" class="modal-btn modal-btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Change Password Modal -->
<div class="custom-modal-overlay" id="changePasswordModal">
    <div class="custom-modal">
        <form action="{{ route('admin.profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="custom-modal-header">
                <h5 class="custom-modal-title"><i class="fas fa-lock"></i> Change Password</h5>
                <button type="button" class="custom-modal-close" onclick="closeModal('changePasswordModal')">&times;</button>
            </div>
            
            <div class="custom-modal-body">
                <div class="modal-form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" class="form-control" name="password" minlength="6" required>
                    <small class="form-hint">Minimum 6 characters</small>
                </div>
                
                <div class="modal-form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" name="password_confirmation" minlength="6" required>
                </div>
            </div>
            
            <div class="custom-modal-footer">
                <button type="button" class="modal-btn modal-btn-secondary" onclick="closeModal('changePasswordModal')">Cancel</button>
                <button type="submit" class="modal-btn modal-btn-primary">
                    <i class="fas fa-save"></i> Update Password
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleSection(id) {
    const section = document.getElementById(id);
    if (section.style.display === 'none') {
        section.style.display = 'block';
        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        section.style.display = 'none';
    }
}

function openModal(id) {
    const modal = document.getElementById(id);
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    const modal = document.getElementById(id);
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

// Close modal on overlay click
document.querySelectorAll('.custom-modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.custom-modal-overlay.active').forEach(modal => {
            modal.classList.remove('active');
        });
        document.body.style.overflow = '';
    }
});
</script>
@endpush
@endsection