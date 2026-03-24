@extends('layouts.sdo')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}?v={{ time() }}">
@endpush

@section('content')
    <div class="profile-container">
        <!-- 1. Core Identification -->
        <div class="profile-card animate__animated animate__backInDown animate__fast" style="animation-delay: 0.1s;">
            <div class="profile-card-header">
                <div class="profile-section-title">
                    <i class="fas fa-id-card"></i>
                    Core Identification
                </div>
            </div>
            <div class="profile-card-body">
                <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data" id="profileUpdateForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="profile-form">
                        <!-- Profile Picture Upload -->
                        <div class="form-item-row" style="margin-bottom: 20px; align-items: flex-start;">
                            <label class="form-item-label">Profile Image</label>
                            <div style="flex: 1; display: flex; align-items: center; gap: 20px;">
                                <div class="pic-preview">
                                    @if($user->profile_picture)
                                        <img src="{{ storage_url($user->profile_picture) }}" alt="{{ $user->full_name }}" style="width: 80px; height: 80px; border-radius: 12px; object-fit: cover; border: 2px solid #e2e8f0;">
                                    @else
                                        <div class="pic-placeholder" style="width: 80px; height: 80px; border-radius: 12px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 700; color: #94a3b8; border: 2px dashed #cbd5e1;">
                                            {{ strtoupper(substr($user->full_name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div style="flex: 1;">
                                    <input type="file" name="profile_picture" class="input-premium" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; font-size: 0.8rem;" accept="image/*">
                                    <small style="display: block; margin-top: 6px; color: #64748b; font-size: 0.72rem;">Upload a professional headshot (JPG, PNG).</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-item-row">
                            <label class="form-item-label">Full Name</label>
                            <div class="input-group-premium">
                                <div class="input-icon"><i class="fas fa-user"></i></div>
                                <input type="text" class="input-premium" name="full_name" value="{{ $user->full_name }}" required>
                            </div>
                        </div>

                        <div class="form-item-row">
                            <label class="form-item-label">Official Position</label>
                            <div class="input-group-premium">
                                <div class="input-icon"><i class="fas fa-briefcase"></i></div>
                                <input type="text" class="input-premium" name="position" value="{{ $user->position }}">
                            </div>
                        </div>

                        <div class="form-item-row">
                            <label class="form-item-label">Salary</label>
                            <div class="input-group-premium">
                                <div class="input-icon"><i class="fas fa-money-bill-wave"></i></div>
                                <input type="text" class="input-premium" name="salary" value="{{ old('salary', $user->salary) }}" placeholder="Enter salary">
                            </div>
                        </div>

                        <div class="form-item-row">
                            <label class="form-item-label">Employee Number</label>
                            <div class="input-group-premium">
                                <div class="input-icon"><i class="fas fa-id-badge"></i></div>
                                <input type="text" class="input-premium" name="employee_number" value="{{ old('employee_number', $user->employee_number) }}" placeholder="7-digit No." pattern="\d{7}" maxlength="7">
                            </div>
                        </div>

                    </div>

                    <div class="profile-footer">
                        <a href="{{ route('user.home') }}" class="btn-back animate__animated animate__backInDown animate__fast" style="animation-delay: 0.55s;">
                            <i class="fas fa-arrow-left"></i>
                            Return to Dashboard
                        </a>
                        <button type="submit" class="btn-sync animate__animated animate__backInDown animate__fast" style="animation-delay: 0.5s;">
                            <i class="fas fa-cloud-upload-alt"></i>
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 2. Approver Configuration -->
        <div class="profile-card accent-blue animate__animated animate__backInDown animate__fast" style="animation-delay: 0.2s;">
            <div class="profile-card-header">
                <div class="profile-section-title">
                    <i class="fas fa-users-cog"></i>
                    Approver Configuration
                </div>
            </div>
            <div class="profile-card-body">
                <form action="{{ route('user.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="profile-form">
                        <div class="form-item-row">
                            <label class="form-item-label">Recommending Approver</label>
                            <div class="input-group-premium">
                                <div class="input-icon"><i class="fas fa-user-check"></i></div>
                                <select class="input-premium" name="recommending_officer_id">
                                    <option value="">Select Recommender</option>
                                    @foreach($recommendingOfficers as $officer)
                                        <option value="{{ $officer->id }}" {{ (old('recommending_officer_id', $user->recommending_officer_id) == $officer->id) ? 'selected' : '' }}>
                                            {{ $officer->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-item-row">
                            <label class="form-item-label">Final Approver</label>
                            <div class="input-group-premium">
                                <div class="input-icon"><i class="fas fa-signature"></i></div>
                                <select class="input-premium" name="approving_officer_id">
                                    <option value="">Select Final Approver</option>
                                    @foreach($finalApprovers as $officer)
                                        <option value="{{ $officer->id }}" {{ (old('approving_officer_id', $user->approving_officer_id) == $officer->id) ? 'selected' : '' }}>
                                            {{ $officer->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div style="margin-top: 10px; padding: 12px; background: #f0f9ff; border-radius: 10px; border: 1px solid #e0f2fe;">
                            <p style="font-size: 0.75rem; color: #0369a1; line-height: 1.4; margin: 0;">
                                <i class="fas fa-info-circle"></i> These officers will be automatically assigned to your leave requests for recommendations and final approval.
                            </p>
                        </div>
                    </div>
                    
                    <div class="profile-footer" style="justify-content: flex-end;">
                        <button type="submit" class="btn-sync">
                            <i class="fas fa-save"></i>
                            Save Config
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 3. Electronic Signature -->
        <div class="profile-card animate__animated animate__backInDown animate__fast" style="animation-delay: 0.3s;">
            <div class="profile-card-header">
                <div class="profile-section-title">
                    <i class="fas fa-file-signature"></i>
                    Electronic Signature
                </div>
            </div>
            <div class="profile-card-body">
                <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data" id="signatureForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="esignature_mode" id="esignatureMode" value="upload">
                    <input type="hidden" name="esignature_data" id="esignatureData">

                    <div class="form-item-row" style="margin-bottom: 20px;">
                        <label class="form-item-label">Current Signature</label>
                        <div class="signature-preview" style="border: 2px dashed #cbd5e1; padding: 20px; text-align: center; border-radius: 12px; background: #f8fafc; flex: 1;">
                            @if($user->esignature)
                                <img src="{{ storage_url($user->esignature) }}" alt="E-Signature" style="max-height: 100px; max-width: 100%;">
                            @else
                                <p class="text-muted" style="font-size: 0.85rem;">No signature uploaded yet.</p>
                            @endif
                        </div>
                    </div>

                    <div class="form-item-row">
                        <label class="form-item-label">Update Signature</label>
                        <div style="flex: 1;">
                            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                                <button type="button" class="btn-tab active" id="tabUpload" onclick="switchSigMode('upload')" style="padding: 8px 16px; border-radius: 6px; border: 1px solid #e2e8f0; background: #eef2ff; color: #6366f1; cursor: pointer; font-size: 0.8rem; font-weight: 700;">
                                    <i class="fas fa-upload"></i> UPLOAD PNG
                                </button>
                                <button type="button" class="btn-tab" id="tabDraw" onclick="switchSigMode('draw')" style="padding: 8px 16px; border-radius: 6px; border: 1px solid #e2e8f0; background: white; cursor: pointer; font-size: 0.8rem; font-weight: 700;">
                                    <i class="fas fa-pen-nib"></i> DRAW SIGNATURE
                                </button>
                            </div>

                            <div id="uploadArea">
                                <input type="file" name="esignature" class="input-premium" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px;" accept="image/png">
                                <small style="display: block; margin-top: 8px; color: #64748b; font-size: 0.75rem;">
                                    Please upload a clear image of your signature (<strong>PNG only</strong>).
                                </small>
                            </div>

                            <div id="drawArea" style="display: none;">
                                <div style="border: 2px solid #e2e8f0; border-radius: 8px; background: #fff; overflow: hidden; width: 100%; max-width: 400px; height: 200px;">
                                    <canvas id="sigCanvas" width="400" height="200" style="display: block; cursor: crosshair; touch-action: none; width: 100%; height: 100%;"></canvas>
                                </div>
                                <div style="margin-top: 10px; display: flex; align-items: center; gap: 20px;">
                                    <small style="color: #64748b; font-size: 0.75rem;">Sign within the box</small>
                                    <button type="button" onclick="clearSigCanvas()" style="color: #dc2626; background: transparent; border: none; cursor: pointer; font-weight: 600; font-size: 0.8rem;">
                                        <i class="fas fa-eraser"></i> Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="profile-footer" style="justify-content: flex-end;">
                        <button type="button" onclick="submitSignature()" class="btn-sync">
                            <i class="fas fa-save"></i>
                            Save Signature
                        </button>
                    </div>
                </form>
            </div>
        </div>

        </div>

        <!-- 4. Security & Authentication -->
        <div class="profile-card accent-purple animate__animated animate__backInDown animate__fast" style="animation-delay: 0.35s;">
            <div class="profile-card-header">
                <div class="profile-section-title">
                    <i class="fas fa-lock"></i>
                    Security & Authentication
                </div>
            </div>
            <div class="profile-card-body">
                <form action="{{ route('user.profile.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="profile-form">
                        <div class="form-item-row">
                            <label class="form-item-label">Current Password</label>
                            <div class="input-group-premium">
                                <div class="input-icon"><i class="fas fa-key"></i></div>
                                <input type="password" class="input-premium" name="current_password" required>
                            </div>
                        </div>
                        <div class="form-item-row">
                            <label class="form-item-label">New Password</label>
                            <div class="input-group-premium">
                                <div class="input-icon"><i class="fas fa-lock"></i></div>
                                <input type="password" class="input-premium" name="password" required minlength="6" placeholder="Minimum 6 characters">
                            </div>
                        </div>
                        <div class="form-item-row">
                            <label class="form-item-label">Confirm Password</label>
                            <div class="input-group-premium">
                                <div class="input-icon"><i class="fas fa-check-circle"></i></div>
                                <input type="password" class="input-premium" name="password_confirmation" required minlength="6" placeholder="Confirm new password">
                            </div>
                        </div>
                    </div>
                    <div class="profile-footer" style="justify-content: flex-end;">
                        <button type="submit" class="btn-sync">
                            <i class="fas fa-save"></i>
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 5. Account Status & Actions -->
        <div class="profile-card accent-orange animate__animated animate__backInDown animate__fast" style="animation-delay: 0.4s;">
            <div class="profile-card-header">
                <div class="profile-section-title">
                    <i class="fas fa-user-circle"></i>
                    Account Status
                </div>
            </div>
            <div class="profile-card-body">
                <div class="profile-form">
                    <div class="form-item-row">
                        <label class="form-item-label">Office / Assignment</label>
                        <div class="input-group-premium">
                            <div class="input-icon"><i class="fas fa-building"></i></div>
                            <input type="text" class="input-premium" value="{{ $user->office_station ?: 'Not set' }}" readonly style="background: #f1f5f9; cursor: not-allowed;">
                        </div>
                    </div>

                    <div class="form-item-row">
                        <label class="form-item-label">Member Since</label>
                        <div class="input-group-premium">
                            <div class="input-icon"><i class="fas fa-calendar-alt"></i></div>
                            <input type="text" class="input-premium" value="{{ $user->created_at->format('F d, Y') }}" readonly style="background: #f1f5f9; cursor: not-allowed;">
                        </div>
                    </div>

                    <div class="privilege-box" style="margin-top: 10px;">
                        <div class="privilege-icon-container" style="background: {{ $user->is_active ? '#ecfdf5' : '#fef2f2' }}; color: {{ $user->is_active ? '#059669' : '#dc2626' }};">
                            <i class="fas {{ $user->is_active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                        </div>
                        <div class="privilege-text">
                            <span class="role-level">Status: {{ $user->is_active ? 'ACTIVE' : 'INACTIVE' }}</span>
                            <p class="role-desc">Your account is {{ $user->is_active ? 'fully operational' : 'currently suspended' }}. You can file leave requests as long as your status remains active.</p>
                        </div>
                    </div>

                    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px dashed #e2e8f0; display: flex; gap: 12px;">
                        <a href="{{ route('user.profile.leave-card') }}" class="btn-sync" style="background: #475569; flex: 1; justify-content: center;" target="_blank">
                            <i class="fas fa-print"></i> Generate Leave Card
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // ---- Signature Logic ----
            let canvas, ctx;
            let isDrawing = false;
            let hasSignature = false;

            function initCanvas() {
                canvas = document.getElementById('sigCanvas');
                if (!canvas) return;
                if (canvas.getAttribute('data-init') === 'true') return;

                ctx = canvas.getContext('2d');
                const dpr = window.devicePixelRatio || 1;
                const rect = canvas.getBoundingClientRect();

                canvas.width = rect.width * dpr;
                canvas.height = rect.height * dpr;
                ctx.scale(dpr, dpr);

                ctx.lineWidth = 2;
                ctx.lineJoin = 'round';
                ctx.lineCap = 'round';
                ctx.strokeStyle = '#000000';

                // Desktop events
                canvas.addEventListener('mousedown', startDraw);
                canvas.addEventListener('mousemove', draw);
                canvas.addEventListener('mouseup', endDraw);
                canvas.addEventListener('mouseout', endDraw);

                // Touch events for mobile
                canvas.addEventListener('touchstart', function(e) {
                    const touch = e.touches[0];
                    startDraw({ clientX: touch.clientX, clientY: touch.clientY });
                }, { passive: false });

                canvas.addEventListener('touchmove', function(e) {
                    e.preventDefault();
                    const touch = e.touches[0];
                    draw({ clientX: touch.clientX, clientY: touch.clientY });
                }, { passive: false });

                canvas.addEventListener('touchend', endDraw);

                canvas.setAttribute('data-init', 'true');
            }

            function startDraw(e) {
                isDrawing = true;
                const rect = canvas.getBoundingClientRect();
                ctx.beginPath();
                ctx.moveTo((e.clientX - rect.left), (e.clientY - rect.top));
            }

            function draw(e) {
                if (!isDrawing) return;
                hasSignature = true;
                const rect = canvas.getBoundingClientRect();
                ctx.lineTo((e.clientX - rect.left), (e.clientY - rect.top));
                ctx.stroke();
            }

            function endDraw() {
                isDrawing = false;
                ctx.beginPath();
            }

            function clearSigCanvas() {
                if (!ctx || !canvas) return;
                ctx.clearRect(0, 0, canvas.width, canvas.height);
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
                    tabUpload.style.background = '#eef2ff';
                    tabUpload.style.color = '#6366f1';
                    tabDraw.style.background = 'white';
                    tabDraw.style.color = '#334155';
                    modeInput.value = 'upload';
                } else {
                    uploadArea.style.display = 'none';
                    drawArea.style.display = 'block';
                    tabUpload.style.background = 'white';
                    tabUpload.style.color = '#334155';
                    tabDraw.style.background = '#eef2ff';
                    tabDraw.style.color = '#6366f1';
                    modeInput.value = 'draw';
                    initCanvas();
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
                    const dataURL = canvas.toDataURL('image/png');
                    document.getElementById('esignatureData').value = dataURL;
                }
                form.submit();
            }

            // Init canvas on load if visible
            window.addEventListener('load', () => {
                const drawArea = document.getElementById('drawArea');
                if(drawArea && drawArea.style.display === 'block') {
                    initCanvas();
                }
            });
        </script>
    @endpush
@endsection