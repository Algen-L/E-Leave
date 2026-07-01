@extends('layouts.sdo')

@section('title', 'HR Profile')
@section('page-title', 'Administrative Hub')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile-redesign.css') }}?v={{ time() }}">
@endpush

@section('content')
    <div class="profile-redesign-wrapper">
        <div class="profile-grid-layout">
            
            <!-- 1. Identity Card (Top Left) -->
            <div class="dash-card identity-card animate__animated animate__fadeIn">
                <div class="avatar-container">
                    @if($user->profile_picture)
                        <img src="{{ storage_url($user->profile_picture) }}" alt="{{ $user->full_name }}" class="avatar-img">
                    @else
                        <div class="avatar-placeholder">
                            {{ strtoupper(substr($user->full_name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <h3 class="profile-name">{{ $user->full_name }}</h3>
                <span class="profile-id">ID: {{ $user->employee_number ?: 'N/A' }}</span>
                
                <div class="info-list" style="margin-top: 24px;">
                    <div class="info-item">
                        <span class="info-label">Member Since</span>
                        <span class="info-value">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status</span>
                        <span class="status-badge {{ $user->is_active ? 'status-active' : 'status-inactive' }}">
                            {{ $user->is_active ? 'ACTIVE' : 'INACTIVE' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- 2. Employment Context Card (Top Middle) -->
            <div class="dash-card animate__animated animate__fadeIn" style="animation-delay: 0.1s;">
                <div class="dash-card-header">
                    <h4 class="dash-card-title"><i class="fas fa-id-card-alt"></i> General Information</h4>
                </div>
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">Current Position</span>
                        <span class="info-value">{{ $user->position ?: 'HR Personnel' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Monthly Salary</span>
                        <span class="info-value">{{ $user->salary ?: 'Not Set' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Office Assignment</span>
                        <span class="info-value">{{ $user->office_station ?: 'HR Division' }}</span>
                    </div>
                    <div class="info-item" style="margin-top: 10px; border-top: 1px dashed #e2e8f0; padding-top: 10px;">
                        <span class="info-label">Recommender</span>
                        <span class="info-value">
                            @php
                                $recommender = $recommendingOfficers->firstWhere('id', $user->recommending_officer_id);
                            @endphp
                            {{ $recommender ? $recommender->full_name : 'Not Set' }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Final Approver</span>
                        <span class="info-value">
                            @php
                                $approver = $finalApprovers->firstWhere('id', $user->approving_officer_id);
                            @endphp
                            {{ $approver ? $approver->full_name : 'Not Set' }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Dept Head</span>
                        <span class="info-value">
                            @if($user->is_dept_head)
                                Not Applicable (Bypass / Department Head)
                            @else
                                @php
                                    $deptHead = $departmentHeads->firstWhere('id', $user->department_head_id);
                                @endphp
                                {{ $deptHead ? $deptHead->full_name : 'Not Set' }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- 3. Administrative Privilege Card (Top Right) -->
            <div class="dash-card animate__animated animate__fadeIn" style="animation-delay: 0.2s;">
                <div class="dash-card-header">
                    <h4 class="dash-card-title"><i class="fas fa-user-shield"></i> Administrative Privilege</h4>
                </div>
                <div class="privilege-content">
                    <div class="role-display mb-3">
                        <span class="role-badge">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                    </div>
                    <ul class="privilege-list">
                        <li><i class="fas fa-check-double"></i> Leave Verification</li>
                        <li><i class="fas fa-calculator"></i> Credit Management</li>
                        <li><i class="fas fa-file-invoice"></i> Form 6 Auditing</li>
                        @if($user->role == 'head_hr')
                            <li><i class="fas fa-cogs"></i> Policy & Type Overrides</li>
                        @endif
                    </ul>
                    <div class="mt-4">
                        <div class="print-tools-wrapper">
                            <a href="{{ route('reports.print-hub') }}" class="dash-btn-primary print-btn">
                                <i class="fas fa-print"></i> Print Documents / Forms
                            </a>
                            <p class="print-btn-helper">Generate official Leave Cards, Summary Tables, and Individual Reports.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Management Center (Bottom Left - Tabs) -->
            <div class="dash-card large-area animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                <div class="dash-tabs">
                    <button class="dash-tab-btn active" onclick="switchProfileTab('editInfo', this)">
                        <i class="fas fa-user-edit"></i> Edit Details
                    </button>
                    <button class="dash-tab-btn" onclick="switchProfileTab('eSignature', this)">
                        <i class="fas fa-file-signature"></i> E-Signature
                    </button>
                    <button class="dash-tab-btn" onclick="switchProfileTab('security', this)">
                        <i class="fas fa-shield-alt"></i> Security & Password
                    </button>
                </div>

                <!-- Tab 1: Edit Information -->
                <div id="editInfo" class="profile-tab-content">
                    <form action="{{ route('hr.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="dash-form-grid">
                            <div class="dash-field" style="grid-column: span 2;">
                                <label class="dash-label">Profile Image</label>
                                <input type="file" name="profile_picture" class="dash-input" accept="image/*">
                            </div>
                            <div class="dash-field">
                                <label class="dash-label">First Name</label>
                                <input type="text" class="dash-input" name="first_name" value="{{ $user->first_name }}" required>
                            </div>
                            <div class="dash-field">
                                <label class="dash-label">Middle Name</label>
                                <input type="text" class="dash-input" name="middle_name" value="{{ $user->middle_name }}">
                            </div>
                            <div class="dash-field">
                                <label class="dash-label">Last Name</label>
                                <input type="text" class="dash-input" name="last_name" value="{{ $user->last_name }}" required>
                            </div>
                            <div class="dash-field">
                                <label class="dash-label">Employee Number (7-Digit)</label>
                                <input type="text" class="dash-input" name="employee_number" value="{{ $user->employee_number }}" pattern="\d{7}" maxlength="7">
                            </div>
                            <div class="dash-field">
                                <label class="dash-label">Designation / Position</label>
                                <input type="text" class="dash-input" name="position" value="{{ $user->position }}">
                            </div>
                             <div class="dash-field">
                                <label class="dash-label">Office / Station</label>
                                <input type="text" class="dash-input" name="office_station" value="{{ $user->office_station }}">
                            </div>
                            <div class="dash-field">
                                <label class="dash-label">Monthly Salary</label>
                                <input type="text" class="dash-input" name="salary" value="{{ $user->salary }}">
                            </div>
                            <div class="dash-field">
                                <label class="dash-label">Recommending Officer</label>
                                <select class="dash-input" name="recommending_officer_id">
                                    <option value="">Select Recommender</option>
                                    @foreach($recommendingOfficers as $officer)
                                        <option value="{{ $officer->id }}" {{ ($user->recommending_officer_id == $officer->id) ? 'selected' : '' }}>
                                            {{ $officer->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="dash-field">
                                 <label class="dash-label">Final Approving Officer</label>
                                 <select class="dash-input" name="approving_officer_id">
                                     <option value="">Select Approver</option>
                                     @foreach($finalApprovers as $officer)
                                         <option value="{{ $officer->id }}" {{ ($user->approving_officer_id == $officer->id) ? 'selected' : '' }}>
                                             {{ $officer->full_name }}
                                         </option>
                                     @endforeach
                                 </select>
                             </div>
                             <div class="dash-field" style="grid-column: span 2;">
                                 <label class="dash-label">Department Head</label>
                                 <select class="dash-input select2-search-dept" name="department_head_id">
                                     <option value="">Select Department Head</option>
                                     <option value="bypass" {{ ($user->is_dept_head) ? 'selected' : '' }}>Not Applicable (Bypass / I am a Department Head)</option>
                                     @foreach($departmentHeads as $dh)
                                         <option value="{{ $dh->id }}" data-employee-number="{{ $dh->employee_number }}" {{ ($user->department_head_id == $dh->id) ? 'selected' : '' }}>
                                             {{ $dh->full_name }} @if($dh->position) — {{ $dh->position }} @endif @if($dh->office_station) ({{ $dh->office_station }}) @endif
                                         </option>
                                     @endforeach
                                 </select>
                             </div>

                            <div class="dash-field" style="grid-column: span 2; margin-top: 10px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                                <label class="dash-label" style="color: var(--primary); font-weight: 800;">
                                    <i class="fas fa-user-tie me-2"></i> Designated Secretary (Approval Delegate)
                                </label>
                                <select class="dash-input" name="secretary_id">
                                    <option value="">No Secretary Assigned</option>
                                    @foreach($allUsers as $potentialSecretary)
                                        <option value="{{ $potentialSecretary->id }}" {{ ($user->secretary_id == $potentialSecretary->id) ? 'selected' : '' }}>
                                            {{ $potentialSecretary->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="mt-2 p-2" style="background: rgba(15, 76, 117, 0.05); border-radius: 8px; font-size: 0.72rem; color: #475569;">
                                    <i class="fas fa-info-circle me-1"></i> The selected user will be granted authority to view and <strong>approve</strong> leave requests on your behalf.
                                </div>
                            </div>
                        </div>
                        <div style="margin-top: 24px; text-align: right;">
                            <button type="submit" class="dash-btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                        </div>
                    </form>
                </div>

                <!-- Tab 2: E-Signature Management -->
                <div id="eSignature" class="profile-tab-content" style="display: none;">
                    <form action="{{ route('hr.profile.update') }}" method="POST" enctype="multipart/form-data" id="sigFormRedesign">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="esignature_mode" id="sigModeRedesign" value="upload">
                        <input type="hidden" name="esignature_data" id="sigDataRedesign">

                        <div class="sig-box-redesign">
                            @if($user->esignature)
                                <img src="{{ storage_url($user->esignature) }}" alt="Signature" id="currentSigPreview" style="max-height: 80px;">
                            @else
                                <p class="text-muted" style="font-size: 0.8rem; margin: 0;">No signature recorded yet.</p>
                            @endif
                        </div>

                        <div class="dash-field">
                            <label class="dash-label">Signature Action</label>
                            <div style="display: flex; gap: 12px; margin-bottom: 20px;">
                                <button type="button" class="dash-btn-primary" onclick="switchSigInput('upload')" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; flex: 1;">
                                    <i class="fas fa-upload"></i> Upload PNG
                                </button>
                                <button type="button" class="dash-btn-primary" onclick="switchSigInput('draw')" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; flex: 1;">
                                    <i class="fas fa-pen"></i> Draw Now
                                </button>
                            </div>

                            <div id="sigUploadAreaRedesign">
                                <input type="file" name="esignature" class="dash-input" accept="image/png">
                                <small style="display:block; margin-top: 8px; color: #64748b;">Transparent PNG recommended.</small>
                            </div>

                            <div id="sigDrawAreaRedesign" style="display: none;">
                                <div style="border: 1px solid #e2e8f0; border-radius: 12px; height: 160px; background: #fff; overflow: hidden;">
                                    <canvas id="redesignSigCanvas" width="600" height="160" style="width: 100%; height: 100%; cursor: crosshair;"></canvas>
                                </div>
                                    <div style="margin-top: 10px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span style="font-size: 0.8rem; color: #475569; font-weight: 600;">Pen Thickness:</span>
                                            <input type="range" min="1" max="10" value="4.5" step="0.5" style="width: 100px; cursor: pointer;" oninput="updatePenThickness(this.value)">
                                            <span id="thicknessValueHR" style="font-size: 0.8rem; color: #475569; font-weight: 700;">4.5</span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-link text-danger" onclick="clearRedesignCanvas()">Clear Drawing</button>
                                    </div>
                                </div>
                        </div>

                        <div class="sig-privacy-note">
                            <p><i class="fas fa-user-shield me-2"></i><strong>Privacy Notice:</strong> Your digital signature is stored securely and will only be used for official leave applications and system-generated documents. By finalizing, you authorize the E-Leave Application System to include this signature on your behalf.</p>
                        </div>

                        <div class="sig-agreement-row">
                            <input type="checkbox" id="esignatureAgreement" onchange="toggleSigFinalizeBtn(this)" style="cursor: pointer;">
                            <label class="sig-agreement-label" for="esignatureAgreement" style="flex: 1; cursor: pointer; margin: 0; padding-left: 8px;">
                                I agree to the use of my digital signature for leave applications and official system documents.
                            </label>
                        </div>

                        <div style="margin-top: 24px; text-align: right;">
                            <button type="button" id="finalizeSigBtn" onclick="submitSigRedesign()" class="dash-btn-primary" disabled>
                                <i class="fas fa-signature"></i> Finalize Signature
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tab 3: Security & Password -->
                <div id="security" class="profile-tab-content" style="display: none;">
                    <form action="{{ route('hr.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="dash-form-grid" style="max-width: 500px;">
                            <div class="dash-field" style="grid-column: span 2;">
                                <label class="dash-label">Current Password</label>
                                <input type="password" name="current_password" class="dash-input" required>
                            </div>
                            <div class="dash-field">
                                <label class="dash-label">New Password</label>
                                <input type="password" name="password" class="dash-input" required minlength="6">
                            </div>
                            <div class="dash-field">
                                <label class="dash-label">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="dash-input" required minlength="6">
                            </div>
                        </div>
                        <div style="margin-top: 24px; text-align: right;">
                            <button type="submit" class="dash-btn-primary"><i class="fas fa-lock"></i> Update Password</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 5. Quick Communications Card (Bottom Right) -->
            <div class="dash-card animate__animated animate__fadeInRight" style="animation-delay: 0.4s;">
                <div class="dash-card-header">
                    <h4 class="dash-card-title"><i class="fas fa-paper-plane"></i> Quick Communications</h4>
                </div>
                <div class="notification-form-container">
                    <form action="{{ route('admin.notifications.send') }}" method="POST">
                        @csrf
                        <div class="dash-field">
                            <label class="dash-label">Recipient</label>
                            <select name="recipient_id" class="dash-input" required>
                                <option value="">Select Recipient</option>
                                <option value="all">Broadcast to All Users</option>
                                @foreach($allUsers as $u)
                                    <option value="{{ $u->id }}">{{ $u->full_name }} ({{ ucfirst($u->role) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="dash-field mt-3">
                            <label class="dash-label">Message</label>
                            <textarea name="message" class="dash-input" rows="3" placeholder="Compose your system notification..." required></textarea>
                        </div>
                        <button type="submit" class="dash-btn-primary mt-3" style="width: 100%;">
                            <i class="fas fa-paper-plane"></i> Send Notification
                        </button>
                    </form>
                    <p style="font-size: 0.72rem; color: #64748b; margin-top: 12px; line-height: 1.4;">
                        <i class="fas fa-info-circle"></i> Notifications sent here will appear in the recipient's dashboard and system alerts.
                    </p>
                </div>
            </div>

         </div>
    </div>

    @push('scripts')
        <script>
            // --- Tabs Logic ---
            function switchProfileTab(tabId, btn) {
                // Hide all contents
                document.querySelectorAll('.profile-tab-content').forEach(content => {
                    content.style.display = 'none';
                });
                
                // Remove active class from buttons
                document.querySelectorAll('.dash-tab-btn').forEach(b => {
                    b.classList.remove('active');
                });

                // Show target content
                document.getElementById(tabId).style.display = 'block';
                btn.classList.add('active');
            }

            // --- Signature Logic (Redesign Version) ---
            let redesignCanvas, redesignCtx;
            let isDrawingRedesign = false;
            let hasDrawingRedesign = false;

            function initRedesignCanvas() {
                redesignCanvas = document.getElementById('redesignSigCanvas');
                if (!redesignCanvas) return;
                if (redesignCanvas.getAttribute('data-init') === 'true') return;

                redesignCtx = redesignCanvas.getContext('2d');
                redesignCtx.lineWidth = currentPenThickness;
                redesignCtx.lineCap = 'round';
                redesignCtx.strokeStyle = '#000000';

                const rect = redesignCanvas.getBoundingClientRect();
                const dpr = window.devicePixelRatio || 1;
                redesignCanvas.width = rect.width * dpr;
                redesignCanvas.height = rect.height * dpr;
                redesignCtx.scale(dpr, dpr);

                redesignCanvas.addEventListener('mousedown', (e) => startRedesignDraw(e));
                redesignCanvas.addEventListener('mousemove', (e) => redesignDraw(e));
                redesignCanvas.addEventListener('mouseup', endRedesignDraw);
                redesignCanvas.addEventListener('mouseout', endRedesignDraw);

                redesignCanvas.addEventListener('touchstart', (e) => {
                    e.preventDefault();
                    startRedesignDraw(e.touches[0]);
                }, { passive: false });
                redesignCanvas.addEventListener('touchmove', (e) => {
                    e.preventDefault();
                    redesignDraw(e.touches[0]);
                }, { passive: false });
                redesignCanvas.addEventListener('touchend', endRedesignDraw);

                redesignCanvas.setAttribute('data-init', 'true');
            }

            let currentPenThickness = 4.5;
            function updatePenThickness(val) {
                currentPenThickness = parseFloat(val);
                const displayUser = document.getElementById('thicknessValueUser');
                if (displayUser) displayUser.textContent = val;
                const displayHR = document.getElementById('thicknessValueHR');
                if (displayHR) displayHR.textContent = val;
                const displayAdmin = document.getElementById('thicknessValueAdmin');
                if (displayAdmin) displayAdmin.textContent = val;
            }

            function startRedesignDraw(e) {
                isDrawingRedesign = true;
                const rect = redesignCanvas.getBoundingClientRect();
                redesignCtx.lineWidth = currentPenThickness;
                redesignCtx.beginPath();
                redesignCtx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
            }

            function redesignDraw(e) {
                if (!isDrawingRedesign) return;
                hasDrawingRedesign = true;
                const rect = redesignCanvas.getBoundingClientRect();
                redesignCtx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
                redesignCtx.stroke();
            }

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(event) {
                if (!event.target.closest('.search-select-wrapper')) {
                    document.querySelectorAll('.user-dropdown-list').forEach(d => d.style.display = 'none');
                }
            });

            function endRedesignDraw() {
                isDrawingRedesign = false;
            }

            function clearRedesignCanvas() {
                redesignCtx.clearRect(0, 0, redesignCanvas.width, redesignCanvas.height);
                hasDrawingRedesign = false;
            }

            function switchSigInput(mode) {
                const uploadArea = document.getElementById('sigUploadAreaRedesign');
                const drawArea = document.getElementById('sigDrawAreaRedesign');
                document.getElementById('sigModeRedesign').value = mode;

                if (mode === 'upload') {
                    uploadArea.style.display = 'block';
                    drawArea.style.display = 'none';
                } else {
                    uploadArea.style.display = 'none';
                    drawArea.style.display = 'block';
                    initRedesignCanvas();
                }
            }

            function submitSigRedesign() {
                const mode = document.getElementById('sigModeRedesign').value;
                if (mode === 'draw') {
                    if (!hasDrawingRedesign) {
                        alert('Please draw your signature first.');
                        return;
                    }
                    document.getElementById('sigDataRedesign').value = redesignCanvas.toDataURL('image/png');
                }
                document.getElementById('sigFormRedesign').submit();
            }

            function toggleSigFinalizeBtn(checkbox) {
                const btn = document.getElementById('finalizeSigBtn');
                btn.disabled = !checkbox.checked;
            }

            // --- Select2 Custom Matcher for ID/Employee Number ---
            function select2CustomMatcher(params, data) {
                if ($.trim(params.term) === '') {
                    return data;
                }
                if (typeof data.text === 'undefined') {
                    return null;
                }
                var term = params.term.toLowerCase();
                var text = data.text.toLowerCase();
                var empNum = $(data.element).data('employee-number') ? $(data.element).data('employee-number').toString().toLowerCase() : '';
                
                if (text.indexOf(term) > -1 || empNum.indexOf(term) > -1) {
                    return data;
                }
                return null;
            }

            // --- Select2 Initialization ---
            $(document).ready(function() {
                $('.select2-search-dept').select2({
                    placeholder: "Search for a department head...",
                    allowClear: true,
                    width: '100%',
                    matcher: select2CustomMatcher
                });
            });
        </script>
    @endpush
@endsection
