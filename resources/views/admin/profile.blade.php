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
                    <img src="{{ storage_url($user->profile_picture) }}" alt="{{ $user->full_name }}">
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
            <button type="button" class="hero-btn hero-btn-outline" onclick="toggleSection('esignatureSection')" title="E-Signature">
                <i class="fas fa-file-signature"></i>
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

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-file-signature"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">E-Signature</div>
                            <div class="info-value">
                                @if($user->esignature)
                                    <img src="{{ storage_url($user->esignature) }}" alt="E-Signature" style="max-height: 50px; border: 1px solid #ddd; padding: 2px;">
                                @else
                                    <span class="text-muted">Not uploaded</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- E-Signature Section (hidden by default) -->
    <div id="esignatureSection" style="display: none;">
        <div class="profile-card">
            <div class="profile-card-header">
                <div class="profile-section-title">
                    <i class="fas fa-file-signature"></i>
                    Electronic Signature
                </div>
            </div>
            <div class="profile-card-body">
                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" id="signatureForm">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="esignature_mode" id="esignatureMode" value="upload">
                    <input type="hidden" name="esignature_data" id="esignatureData">
                    
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">Current Signature</label>
                            <div class="signature-preview" style="border: 2px dashed #cbd5e1; padding: 20px; text-align: center; border-radius: 12px; margin-bottom: 20px; background: #f8fafc;">
                                @if($user->esignature)
                                    <img src="{{ storage_url($user->esignature) }}" alt="E-Signature" style="max-height: 100px; max-width: 100%;">
                                @else
                                    <p class="text-gray-400">No signature uploaded yet.</p>
                                @endif
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Update Signature</label>

                            <!-- Tabs -->
                            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                                <button type="button" class="btn-tab active" id="tabUpload" onclick="switchSigMode('upload')" 
                                    style="padding: 8px 16px; border-radius: 6px; border: 1px solid #e2e8f0; background: #eef2ff; color: #6366f1; cursor: pointer;">
                                    <i class="fas fa-upload"></i> Upload PNG
                                </button>
                                <button type="button" class="btn-tab" id="tabDraw" onclick="switchSigMode('draw')"
                                    style="padding: 8px 16px; border-radius: 6px; border: 1px solid #e2e8f0; background: white; cursor: pointer;">
                                    <i class="fas fa-pen-nib"></i> Draw Signature
                                </button>
                            </div>

                            <!-- Upload Area -->
                            <div id="uploadArea">
                                <input type="file" name="esignature" class="form-control" accept="image/png">
                                <small style="display: block; margin-top: 8px; color: #64748b;">
                                    Please upload a clear image of your signature (<strong>PNG only</strong> with transparent background recommended).
                                </small>
                            </div>

                            <!-- Draw Area -->
                            <div id="drawArea" style="display: none;">
                                <div style="border: 2px solid #e2e8f0; border-radius: 8px; background: #fff; overflow: hidden; position: relative; width: 300px; height: 300px; margin: 0 auto;">
                                     <canvas id="sigCanvas" width="300" height="300" style="display: block; cursor: crosshair; touch-action: none;"></canvas>
                                </div>
                                <div style="margin-top: 10px; display: flex; justify-content: center; align-items: center; gap: 20px;">
                                    <small class="text-gray-500">Sign within the box</small>
                                    <button type="button" onclick="clearSigCanvas()" style="color: #dc2626; background: transparent; border: none; cursor: pointer; font-weight: 600;">
                                        <i class="fas fa-eraser"></i> Clear
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <button type="button" onclick="submitSignature()" class="btn-primary">
                                <i class="fas fa-save"></i> Save Signature
                            </button>
                        </div>
                    </div>
                </form>
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

                <div class="modal-form-group">
                    <label class="form-label">E-Signature (PNG)</label>
                    <input type="file" class="form-control" name="esignature" accept="image/png">
                    <small class="form-question">Upload a clear PNG signature with transparent background.</small>
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
        
        if (id === 'esignatureSection') {
             // Init canvas if not already
             setTimeout(initCanvas, 100);
        }
    } else {
        section.style.display = 'none';
    }
}

// ---- Signature Logic ----
let canvas, ctx;
let isDrawing = false;
let hasSignature = false;

function initCanvas() {
    canvas = document.getElementById('sigCanvas');
    if(!canvas) return;
    
    // Only init once
    if (canvas.getAttribute('data-init') === 'true') return;

    ctx = canvas.getContext('2d');
    
    // Set explicit size to match CSS size * DPR for sharpness
    const dpr = window.devicePixelRatio || 1;
    const rect = canvas.getBoundingClientRect();

    // If section is hidden, rect might be 0. We set a default or just rely on CSS width
    // But getBoundingClientRect returns 0 if hidden...
    // We already ensure section is visible before initCanvas is called
    
    if (rect.width > 0) {
        canvas.width = rect.width * dpr;
        canvas.height = rect.height * dpr;
        ctx.scale(dpr, dpr);
    } else {
        // Fallback
        canvas.width = 300 * dpr;
        canvas.height = 300 * dpr;
        ctx.scale(dpr, dpr);
    }
    
    ctx.lineWidth = 2;
    ctx.lineJoin = 'round';
    ctx.lineCap = 'round';
    ctx.strokeStyle = '#000000';
    
    // Events
    canvas.addEventListener('mousedown', startDraw);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', endDraw);
    canvas.addEventListener('mouseout', endDraw);
    
    // Touch events
    canvas.addEventListener('touchstart', (e) => {
        e.preventDefault();
        const touch = e.touches[0];
        const mouseEvent = new MouseEvent('mousedown', {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        canvas.dispatchEvent(mouseEvent);
    });

    canvas.addEventListener('touchmove', (e) => {
        e.preventDefault();
        const touch = e.touches[0];
        const mouseEvent = new MouseEvent('mousemove', {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        canvas.dispatchEvent(mouseEvent);
    });

    canvas.addEventListener('touchend', (e) => {
        e.preventDefault();
        const mouseEvent = new MouseEvent('mouseup', {});
        canvas.dispatchEvent(mouseEvent);
    });

    canvas.setAttribute('data-init', 'true');
}

function startDraw(e) {
    isDrawing = true;
    const rect = canvas.getBoundingClientRect();
    ctx.beginPath();
    ctx.moveTo((e.clientX - rect.left) * (canvas.width / rect.width), (e.clientY - rect.top) * (canvas.height / rect.height));
}

function draw(e) {
    if (!isDrawing) return;
    hasSignature = true;
    const rect = canvas.getBoundingClientRect();
    ctx.lineTo((e.clientX - rect.left) * (canvas.width / rect.width), (e.clientY - rect.top) * (canvas.height / rect.height));
    ctx.stroke();
}

function endDraw() {
    isDrawing = false;
    ctx.beginPath();
}

function clearSigCanvas() {
    if(!ctx || !canvas) return;
    ctx.clearRect(0, 0, canvas.width, canvas.height); // clear scaled canvas
    hasSignature = false;
}

function switchSigMode(mode) {
    const uploadArea = document.getElementById('uploadArea');
    const drawArea = document.getElementById('drawArea');
    const tabUpload = document.getElementById('tabUpload');
    const tabDraw = document.getElementById('tabDraw');
    const modeInput = document.getElementById('esignatureMode');

    if (mode === 'upload') {
        uploadArea.style.display = 'block';
        drawArea.style.display = 'none';
        
        tabUpload.classList.add('active');
        tabUpload.style.background = '#eef2ff';
        tabUpload.style.color = '#6366f1';
        
        tabDraw.classList.remove('active');
        tabDraw.style.background = 'white';
        tabDraw.style.color = '#334155';
        
        modeInput.value = 'upload';
    } else {
        uploadArea.style.display = 'none';
        drawArea.style.display = 'block';
        
        tabUpload.classList.remove('active');
        tabUpload.style.background = 'white';
        tabUpload.style.color = '#334155';
        
        tabDraw.classList.add('active');
        tabDraw.style.background = '#eef2ff';
        tabDraw.style.color = '#6366f1';
        
        modeInput.value = 'draw';
        
        // Timeout to ensure display block has rendered before initCanvas
        setTimeout(initCanvas, 50);
    }
}

function submitSignature() {
    const form = document.getElementById('signatureForm');
    const mode = document.getElementById('esignatureMode').value;
    
    if (mode === 'draw') {
        if (!hasSignature) {
            alert('Please draw your signature first.');
            return;
        }
        // Get base64
        const dataURL = canvas.toDataURL('image/png');
        document.getElementById('esignatureData').value = dataURL;
    }
    
    form.submit();
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