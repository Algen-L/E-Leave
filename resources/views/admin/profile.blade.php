@extends('layouts.sdo')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}?v={{ time() }}">
    <style>
        .profile-container {
            animation: fadeIn 0.4s ease-out;
        }

        .profile-card {
            opacity: 0;
            animation: backInDown 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        .profile-card:nth-child(1) { animation-delay: 0.1s; }
        .profile-card:nth-child(2) { animation-delay: 0.2s; }
        .profile-card:nth-child(3) { animation-delay: 0.3s; }
        .profile-card:nth-child(4) { animation-delay: 0.4s; }

        @keyframes backInDown {
            0% {
                transform: translateY(-100px) scale(0.7);
                opacity: 0;
            }
            80% {
                transform: translateY(0px) scale(0.7);
                opacity: 0.7;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
@endpush

@section('content')
    <div class="profile-container">
        <!-- 1. Core Identification -->
        <div class="profile-card">
            <div class="profile-card-header">
                <div class="profile-section-title">
                    <i class="fas fa-id-card"></i>
                    Core Identification
                </div>
            </div>
            <div class="profile-card-body">
                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" id="profileUpdateForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="profile-form">
                        <div class="form-item-row">
                            <label class="form-item-label">Primary Full Name</label>
                            <div class="input-group-premium">
                                <div class="input-icon"><i class="fas fa-user"></i></div>
                                <input type="text" class="input-premium" name="full_name" value="{{ $user->full_name }}" required>
                            </div>
                        </div>

                        <div class="form-item-row">
                            <label class="form-item-label">Office / Assignment</label>
                            <div class="input-group-premium">
                                <div class="input-icon"><i class="fas fa-building"></i></div>
                                <input type="text" class="input-premium" name="office_station" value="{{ $user->office_station }}">
                            </div>
                        </div>

                        <div class="form-item-row">
                            <label class="form-item-label">Official Position</label>
                            <div class="input-group-premium">
                                <div class="input-icon"><i class="fas fa-briefcase"></i></div>
                                <input type="text" class="input-premium" name="position" value="{{ $user->position }}">
                            </div>
                        </div>

                        </div>

                    <div class="profile-footer">
                        <a href="{{ route('admin.dashboard') }}" class="btn-back">
                            <i class="fas fa-arrow-left"></i>
                            Return to Dashboard
                        </a>
                        <button type="submit" class="btn-sync">
                            <i class="fas fa-cloud-upload-alt"></i>
                            Synchronize Security Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 2. Administrative Privileges -->
        <div class="profile-card accent-blue">
            <div class="profile-card-header">
                <div class="profile-section-title">
                    <i class="fas fa-user-shield"></i>
                    Administrative Privileges
                </div>
            </div>
            <div class="profile-card-body">
                <div class="privilege-box">
                    <div class="privilege-icon-container">
                        <i class="fas fa-key"></i>
                    </div>
                    <div class="privilege-text">
                        <span class="role-level">Role Level: {{ strtoupper($user->role) }}</span>
                        <p class="role-desc">
                            Your account is authorized with <strong>Highest Level</strong> administrative permissions. 
                            You have full control over system configuration, user management, and security protocols.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
        </div>

        <!-- 3. Security & Authentication -->
        <div class="profile-card accent-purple">
            <div class="profile-card-header">
                <div class="profile-section-title">
                    <i class="fas fa-lock"></i>
                    Security & Authentication
                </div>
            </div>
            <div class="profile-card-body">
                <form action="{{ route('admin.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="profile-form">
                        <div class="form-item-row">
                            <label class="form-item-label">Current Password <span class="required-asterisk">*</span></label>
                            <div class="input-group-premium">
                                <div class="input-icon"><i class="fas fa-key"></i></div>
                                <input type="password" class="input-premium" name="current_password" required>
                            </div>
                        </div>

                        <div class="form-item-row">
                            <label class="form-item-label">New Password <span class="required-asterisk">*</span></label>
                            <div class="input-group-premium">
                                <div class="input-icon"><i class="fas fa-lock"></i></div>
                                <input type="password" class="input-premium" name="password" required minlength="6" placeholder="Minimum 6 characters">
                            </div>
                        </div>

                        <div class="form-item-row">
                            <label class="form-item-label">Confirm Password <span class="required-asterisk">*</span></label>
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

        <!-- 4. E-Signature Section -->
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

        <!-- 4. Send System Notification -->
        @if(in_array($user->role, ['super_admin', 'admin', 'head_hr']))
            <div class="profile-card accent-orange">
                <div class="profile-card-header">
                    <div class="profile-section-title">
                        <i class="fas fa-bullhorn"></i>
                        Send System Notification
                    </div>
                </div>
                <div class="profile-card-body">
                    <form action="{{ route('admin.notifications.send') }}" method="POST">
                        @csrf
                        <label class="form-label-premium">Recipient <span>*</span></label>
                        <div class="input-group-premium" style="margin-bottom: 20px;">
                            <div class="input-icon"><i class="fas fa-users"></i></div>
                            <select class="input-premium" name="recipient_id" required>
                                <option value="all">All Users</option>
                                @foreach($allUsers as $u)
                                    @if($u->id !== $user->id)
                                        <option value="{{ $u->id }}">{{ $u->full_name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <label class="form-label-premium">Message <span>*</span></label>
                        <textarea class="textarea-premium" name="message" maxlength="500" placeholder="Type your notification message here..." required></textarea>

                        <button type="submit" class="btn-send">
                            <i class="fas fa-paper-plane"></i>
                            Send
                        </button>
                    </form>

                    <div class="system-protocol-extra">
                        <div class="protocol-header">
                            <i class="fas fa-info-circle"></i>
                            System Broadcast Protocol
                        </div>
                        <ul class="protocol-list">
                            <li>
                                <i class="fas fa-clock"></i>
                                <span><strong>Delivery Timing</strong>: Notifications are delivered in real-time to active sessions and stored for offline users.</span>
                            </li>
                            <li>
                                <i class="fas fa-exclamation-triangle"></i>
                                <span><strong>Content Policy</strong>: Use for official administrative announcements only. Avoid redundant or sensitive data.</span>
                            </li>
                            <li>
                                <i class="fas fa-history"></i>
                                <span><strong>Audit Trail</strong>: All outgoing broadcasts are logged with timestamp and author ID for security compliance.</span>
                            </li>
                            <li>
                                <i class="fas fa-server"></i>
                                <span><strong>Priority Level</strong>: System notifications bypass individual user "Do Not Disturb" settings.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif
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

                canvas.addEventListener('mousedown', startDraw);
                canvas.addEventListener('mousemove', draw);
                canvas.addEventListener('mouseup', endDraw);
                canvas.addEventListener('mouseout', endDraw);

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
                if(document.getElementById('drawArea').style.display === 'block') {
                    initCanvas();
                }
            });
        </script>
    @endpush
@endsection