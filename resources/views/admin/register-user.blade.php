@extends('layouts.sdo')

@section('title', 'Register User')
@section('page-title', 'Register New User')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/register-user.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .register-container {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 32px;
            align-items: start;
            animation: fadeIn 0.4s ease-out;
            margin-top: 10px;
        }

        @media (max-width: 992px) {
            .register-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }

        .avatar-panel {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Stable Entrance Animation */
        .avatar-card, .account-card-compact, .form-panel {
            opacity: 0;
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        .avatar-card { animation-delay: 0.1s; }
        .account-card-compact { animation-delay: 0.2s; }
        .form-panel { animation-delay: 0.3s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Ensure No Overlaps */
        .avatar-card, .form-panel {
            position: relative !important;
            top: 0 !important;
            margin: 0 !important;
        }
    </style>
@endpush

@section('content')
    <form action="{{ route('admin.register-user.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" style="display: none;">
        
        <div class="register-container">
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

            <!-- Relocated Account Information -->
            <div class="form-section account-card-compact" style="background: white; border-radius: 20px; padding: 24px; border: 1px solid var(--border-color); box-shadow: var(--shadow-md); position: relative; z-index: 1;">
                <div class="section-header" style="padding-bottom: 12px; margin-bottom: 20px;">
                    <div class="section-icon" style="width: 36px; height: 36px; font-size: 0.9rem;">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h4 class="section-title" style="font-size: 1rem;">Account Information</h4>
                </div>

                <div class="form-grid-1" style="display: flex; flex-direction: column; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">Email (DepEd Gmail) <span class="required">*</span></label>
                        <input type="email" class="form-control @error('gmail') error @enderror" name="gmail"
                            value="{{ old('gmail') }}" placeholder="username@deped.gov.ph" required>
                        @error('gmail')
                            <div class="input-feedback error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="font-weight: 700; display: block; margin-bottom: 8px;">Roles <span class="required">*</span></label>
                        <div class="roles-checkbox-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 8px;">
                            <label class="role-checkbox-item" style="display: flex; align-items: center; gap: 8px; padding: 10px 12px; background: #f0f4fc; border: 1.5px solid var(--primary, #1b4a9a); border-radius: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--primary, #1b4a9a)'" onmouseout="if(!this.querySelector('input').checked) this.style.borderColor='#e2e8f0'">
                                <input type="checkbox" name="roles[]" value="user" 
                                    {{ !old('roles') || in_array('user', old('roles')) ? 'checked' : '' }} 
                                    style="accent-color: var(--primary, #1b4a9a); width: 16px; height: 16px;"
                                    onchange="if(this.checked) { this.parentElement.style.background='#f0f4fc'; this.parentElement.style.borderColor='var(--primary, #1b4a9a)'; } else { this.parentElement.style.background='white'; this.parentElement.style.borderColor='#e2e8f0'; }">
                                <span style="font-weight: 600; font-size: 0.85rem; color: #334155;">USER</span>
                            </label>
                            @if(old('roles') && !in_array('user', old('roles')))
                                <script>
                                    document.currentScript.previousElementSibling.style.background = 'white';
                                    document.currentScript.previousElementSibling.style.borderColor = '#e2e8f0';
                                </script>
                            @endif

                            @foreach($allRoles as $role)
                                @if($role->name !== 'user')
                                    @php
                                        $isHrOnly = auth()->user()->hasRole(['hr', 'head_hr', 'hr_review_officer']) && !auth()->user()->hasRole(['super_admin', 'admin']);
                                        $isRestricted = in_array($role->name, ['asds', 'sds', 'sgod_chief', 'cid_chief', 'ao']);
                                        $canAssign = true;
                                        if ($isHrOnly) {
                                            $canAssign = false;
                                        }
                                        if ($isRestricted && !auth()->user()->isSuperAdmin()) {
                                            $canAssign = false;
                                        }
                                        if ($role->name === 'super_admin' && !auth()->user()->isSuperAdmin()) {
                                            $canAssign = false;
                                        }
                                        if ($role->name === 'hr_review_officer' && !auth()->user()->isSuperAdmin() && !auth()->user()->isHR()) {
                                            $canAssign = false;
                                        }
                                    @endphp
                                    @if($canAssign)
                                        <label class="role-checkbox-item" style="display: flex; align-items: center; gap: 8px; padding: 10px 12px; background: white; border: 1.5px solid #e2e8f0; border-radius: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--primary, #1b4a9a)'" onmouseout="if(!this.querySelector('input').checked) this.style.borderColor='#e2e8f0'">
                                            <input type="checkbox" name="roles[]" value="{{ $role->name }}" 
                                                {{ old('roles') && in_array($role->name, old('roles')) ? 'checked' : '' }} 
                                                style="accent-color: var(--primary, #1b4a9a); width: 16px; height: 16px;"
                                                onchange="if(this.checked) { this.parentElement.style.background='#f0f4fc'; this.parentElement.style.borderColor='var(--primary, #1b4a9a)'; } else { this.parentElement.style.background='white'; this.parentElement.style.borderColor='#e2e8f0'; }}">
                                            <span style="font-weight: 600; font-size: 0.85rem; color: #334155;">{{ $role->display_name }}</span>
                                        </label>
                                        @if(old('roles') && in_array($role->name, old('roles')))
                                            <script>
                                                document.currentScript.previousElementSibling.style.background = '#f0f4fc';
                                                document.currentScript.previousElementSibling.style.borderColor = 'var(--primary, #1b4a9a)';
                                            </script>
                                        @endif
                                    @endif
                                @endif
                            @endforeach
                        </div>
                        @error('roles')
                            <div class="input-feedback error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password <span class="required">*</span></label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control @error('password') error @enderror"
                                name="password" id="password" placeholder="Enter password" minlength="6" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password', 'toggleIcon')">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="input-feedback error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirm Password <span class="required">*</span></label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control"
                                name="password_confirmation" id="password_confirmation" placeholder="Confirm password" minlength="6" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', 'toggleIconConfirm')">
                                <i class="fas fa-eye" id="toggleIconConfirm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="form-panel">
            <!-- Personal Information -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <h4 class="section-title">Personal Information</h4>
                    </div>

                    <div class="form-grid-3">
                        <div class="form-group">
                            <label class="form-label">First Name <span class="required">*</span></label>
                            <input type="text" class="form-control @error('first_name') error @enderror" name="first_name"
                                value="{{ old('first_name') }}" placeholder="Enter first name" required>
                            @error('first_name')
                                <div class="input-feedback error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control @error('middle_name') error @enderror" name="middle_name"
                                value="{{ old('middle_name') }}" placeholder="Enter middle name">
                            @error('middle_name')
                                <div class="input-feedback error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Last Name <span class="required">*</span></label>
                            <input type="text" class="form-control @error('last_name') error @enderror" name="last_name"
                                value="{{ old('last_name') }}" placeholder="Enter last name" required>
                            @error('last_name')
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
                            <label class="form-label">Employee No. <span class="required">*</span></label>
                            <input type="text" class="form-control @error('employee_number') error @enderror"
                                name="employee_number" value="{{ old('employee_number') }}"
                                placeholder="Enter 7-digit Employee No." pattern="\d{7}" maxlength="7"
                                title="Must be exactly 7 digits" required>
                            @error('employee_number')
                                <div class="input-feedback error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

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
                    </div>

                    <div class="form-grid-2" style="margin-top: 20px;">
                        <div class="form-group">
                            <label class="form-label">Position</label>
                            <select class="form-select" id="position" name="position">
                                <option value="">Select Position</option>
                            </select>
                            <input type="hidden" id="custom_position" name="custom_position_input" disabled>
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
        </div>
    </div>
</form>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Office to Position Mapping
            const positionsMap = {
                // OFFICE OF THE SCHOOLS DIVISION SUPERINTENDENT
                'ADMINISTRATIVE': [
                    'Administrative Officer V', 'Administrative Officer II', 'Administrative Assistant I',
                    'Administrative Assistant II', 'Administrative Assistant III', 'Administrative Support II',
                    'Administrative Aide I', 'Administrative Aide IV', 'Administrative Aide VI',
                    'Division Driver', 'LSB Clerk', 'LSB Utility', 'LSB Watchman', 'LSB Driver'
                ],
                // LEGAL UNIT
                'LEGAL': ['Attorney III', 'Legal Assistant I'],

                // PLANNING AND RESEARCH
                'SGOD (PLANNING AND RESEARCH)': ['Planning Officer III', 'Planning Development Officer II'],

                // HUMAN RESOURCE DEVELOPMENT, PERSONNEL
                'ADMINISTRATIVE (PERSONEL)': [
                    'Administrative Officer IV', 'Administrative Officer II', 'Administrative Assistant III',
                    'Administrative Aide VI', 'LSB Clerk'
                ],

                // FINANCE DIVISION (Accounting and Budget)
                'FINANCE (ACCOUNTING)': [
                    'Administrative Officer IV', 'Accountant III', 'Administrative Assistant III', 'Administrative Aide VI'
                ],
                'FINANCE (BUDGET)': [ // Assuming Budget matches Accounting or is similar
                    'Administrative Officer IV', 'Accountant III', 'Administrative Assistant III', 'Administrative Aide VI'
                ],
                // FINANCE DIVISION (Cash) -> Administrative (Cash)
                'ADMINISTRATIVE (CASH)': ['LSB Utility'],

                // FINANCE DIVISION (Procurement) -> Administrative (Procurement)
                'ADMINISTRATIVE (PROCUREMENT)': [
                    'Administrative Officer IV', 'Administrative Assistant III', 'Administrative Aide VI', 'LSB Clerk'
                ],

                // FINANCE DIVISION (Property and Supply Records)
                'ADMINISTRATIVE (PROPERTY AND SUPPLY)': ['Administrative Officer IV', 'Administrative Aide VI', 'LSB Clerk'],
                'ADMINISTRATIVE (RECORDS)': ['Administrative Officer IV', 'Administrative Aide VI', 'LSB Clerk'],

                // SCHOOL GOVERNANCE AND OPERATIONS DIVISION (General Services)
                'ADMINISTRATIVE (GENERAL SERVICES)': [
                    'Administrative Aide VI', 'LSB Clerk', 'LSB Watchman', 'LSB Utility', 'Division Driver'
                ],

                // ICT
                'ICT': ['IT Officer I'],

                // Social Mobilization and Networking
                'SGOD (SOCIAL MOBILIZATION AND NETWORKING)': ['Project Development Officer II'],

                // Disaster Risk Reduction
                'SGOD (DISASTER RISK REDUCTION AND MANAGEMENT)': ['Engineer III'],

                // Education Facilities
                'SGOD (EDUCATION FACILITIES)': ['Senior Education Program Specialist'],

                // School Management Monitoring and Evaluation
                'SGOD (SCHOOL MANAGEMENT MONITORING & EVALUATION)': ['Education Program Specialist II'],

                // School Health and Nutrition - Medical
                'SGOD (SCHOOL HEALTH AND NUTRITION) (MEDICAL)': ['Medical Officer III', 'Nurse II'],

                // School Health and Nutrition - Dental
                'SGOD (SCHOOL HEALTH AND NUTRITION) (DENTAL)': ['Dentist II'],

                // Alternative Learning System
                'CID (ALTERNATIVE LEARNING SYSTEM)': ['Education Program Specialist II ALS'],

                // Instructional Management
                'CID (INSTRUCTIONAL MANAGEMENT)': ['Education Program Supervisor'],

                // Learning Resource Management
                'CID (LEARNING RESOURCES MANAGEMENT)': ['Librarian II', 'Education Program Supervisor LRMDS'],

                // District Instructional Supervision
                'CID (DISTRICT INSTRUCTIONAL SUPERVISION)': ['Public Schools District Supervisor'],

                // Additional mappings just in case user selects others:
                'SGOD (HUMAN RESOURCES DEVELOPMENT)': [ // Often overlaps with Personnel
                    'Senior Education Program Specialist', 'Education Program Specialist II'
                ]
            };

            const officeSelect = new TomSelect('#office_station', {
                allowEmptyOption: true,
                placeholder: 'Select Office/Station',
                onChange: function (value) {
                    updatePositions(value);
                }
            });

            const positionSelect = new TomSelect('#position', {
                create: true,
                persist: false,
                createOnBlur: true,
                placeholder: 'Select or type position...'
            });

            function updatePositions(officeName) {
                positionSelect.clear();
                positionSelect.clearOptions();

                if (!officeName) return;

                let positions = positionsMap[officeName] || [];

                // Special Handling for CID Instructional Management (Subject Area Supervisors)
                if (officeName === 'CID (INSTRUCTIONAL MANAGEMENT)') {
                    const supervisors = [
                        'Education Program Supervisor',
                        'Education Program Supervisor Filipino',
                        'Education Program Supervisor English',
                        'Education Program Supervisor Mathematics',
                        'Education Program Supervisor Science',
                        'Education Program Supervisor Araling Panlipunan',
                        'Education Program Supervisor Kindergarten',
                        'Education Program Supervisor TLE',
                        'Education Program Supervisor MAPEH',
                        'Education Program Supervisor Values Education'
                    ];
                    // Merge unique values
                    positions = [...new Set([...positions, ...supervisors])];
                } else if (officeName.startsWith('FINANCE') || officeName.startsWith('ADMINISTRATIVE (FINANCE)')) {
                    // Consolidated FINANCE Fallback if specific sub-office fails
                    if (positions.length === 0) {
                        positions = [
                            'Administrative Officer IV', 'Accountant III', 'Administrative Assistant III', 'Administrative Aide VI', 'LSB Utility', 'LSB Clerk'
                        ];
                    }
                }

                // Add options
                positions.forEach(pos => {
                    positionSelect.addOption({ value: pos, text: pos });
                });

                positionSelect.refreshOptions();
            }
        });

        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);

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
