@extends('layouts.sdo')

@section('title', 'Edit User')
@section('page-title', 'Edit User: ' . ($editUser->full_name ?? ($editUser->first_name . ' ' . $editUser->last_name)))

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <link href="{{ asset('css/edit-user.css') }}?v={{ time() }}" rel="stylesheet">
@endpush

@section('content')
    <form action="{{ route('admin.users.update', $editUser) }}" method="POST" enctype="multipart/form-data" class="edit-container">
        @csrf
        @method('PUT')
        
        <div class="form-grid-main">
            <!-- Left Column: Avatar -->
            <div class="avatar-preview-section">
                @php
                    $initials = strtoupper(substr($editUser->first_name ?? $editUser->full_name, 0, 1) . substr($editUser->last_name, 0, 1));
                    $profilePic = $editUser->profile_picture ? asset($editUser->profile_picture) : null;
                @endphp
                
                @if($profilePic)
                    <img src="{{ $profilePic }}" class="preview-circle" id="imgPreview" alt="Profile Picture">
                @else
                    <div class="preview-circle" id="imgPlaceholder">{{ $initials }}</div>
                    <img src="" class="preview-circle" id="imgPreview" style="display: none;">
                @endif

                <label class="btn btn-secondary btn-sm w-100 mt-3" style="background: #f1f5f9; color: #475569; border: 1.5px solid #e2e8f0; display: flex; align-items: center; justify-content: center; padding: 10px; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                    <i class="fas fa-camera" style="margin-right: 8px;"></i> Change Photo
                    <input type="file" name="profile_picture" hidden onchange="previewImage(this)">
                </label>
                <p class="text-muted mt-3" style="font-size: 0.75rem;">JPG, PNG or WEBP. Max 2MB.</p>
                
                <hr style="margin: 24px 0; border-color: #f1f5f9;">
                
                <div class="text-start">
                    <div class="mb-3">
                        <small class="text-muted d-block text-uppercase fw-bold mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px; opacity: 0.8;">Account Status</small>
                        <div class="d-flex gap-2">
                             <label class="status-option {{ old('is_active', $editUser->is_active) ? 'active' : '' }} flex-grow-1" style="padding: 8px; margin: 0; display: flex; align-items: center; justify-content: center; gap: 6px; border-radius: 10px; cursor: pointer; transition: all 0.2s; border: 2px solid #f1f5f9;">
                                 <input type="radio" name="is_active" value="1" {{ old('is_active', $editUser->is_active) ? 'checked' : '' }} hidden>
                                 <i class="fas fa-check-circle" style="font-size: 0.9rem;"></i>
                                 <span style="font-size: 0.85rem; font-weight: 700;">Active</span>
                             </label>
                             <label class="status-option {{ !old('is_active', $editUser->is_active) ? 'inactive' : '' }} flex-grow-1" style="padding: 8px; margin: 0; display: flex; align-items: center; justify-content: center; gap: 6px; border-radius: 10px; cursor: pointer; transition: all 0.2s; border: 2px solid #f1f5f9;">
                                 <input type="radio" name="is_active" value="0" {{ !old('is_active', $editUser->is_active) ? 'checked' : '' }} hidden>
                                 <i class="fas fa-times-circle" style="font-size: 0.9rem;"></i>
                                 <span style="font-size: 0.85rem; font-weight: 700;">Inactive</span>
                             </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Details -->
            <div class="edit-card">
                <div class="form-section-title"><i class="fas fa-id-card" style="margin-right: 8px; color: var(--primary, #0f4c75);"></i> Personal details</div>
                <div class="form-grid-inner">
                    <div class="form-group full-width">
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" required
                                    value="{{ old('first_name', $editUser->first_name) }}" placeholder="First Name">
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Middle Name</label>
                                <input type="text" name="middle_name" class="form-control @error('middle_name') is-invalid @enderror"
                                    value="{{ old('middle_name', $editUser->middle_name) }}" placeholder="Middle Name">
                                @error('middle_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" required
                                    value="{{ old('last_name', $editUser->last_name) }}" placeholder="Last Name">
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>


                </div>

                <div class="form-section-title mt-4"><i class="fas fa-briefcase" style="margin-right: 8px; color: var(--primary, #0f4c75);"></i> Professional assignment</div>
                <div class="form-grid-inner">
                    <div class="form-group full-width">
                        <label class="form-label">Office / Station</label>
                        <select name="office_station" id="office_select" class="form-control" required>
                            <option value="">Select Office</option>
                            @foreach($offices as $category => $officeList)
                                <optgroup label="{{ $category }}">
                                    @foreach($officeList as $office)
                                        <option value="{{ $office->name }}" {{ old('office_station', $editUser->office_station) === $office->name ? 'selected' : '' }}>
                                            {{ $office->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">Position / Designation</label>
                        <input type="text" name="position" class="form-control @error('position') is-invalid @enderror"
                            value="{{ old('position', $editUser->position) }}" placeholder="Enter position">
                        @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group hidable-field">
                        <label class="form-label">Employee Number <span class="text-danger">*</span></label>
                        <input type="text" name="employee_number" class="form-control @error('employee_number') is-invalid @enderror" required
                            value="{{ old('employee_number', $editUser->employee_number) }}"
                            placeholder="e.g. 1234567" maxlength="7" pattern="\d{7}" title="Must be exactly 7 digits"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                        @error('employee_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group hidable-field">
                        <label class="form-label">System Role <span class="text-danger">*</span></label>
                        <select name="role" id="role-select" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="user" {{ old('role', $editUser->role) === 'user' ? 'selected' : '' }}>USER</option>
                            <option value="head_hr" {{ old('role', $editUser->role) === 'head_hr' ? 'selected' : '' }}>HR PERSONNEL</option>
                            @if(in_array(auth()->user()->role, ['hr', 'head_hr', 'super_admin']))
                                <option value="hr_review_officer" {{ old('role', $editUser->role) === 'hr_review_officer' ? 'selected' : '' }}>HR REVIEW OFFICER</option>
                            @endif
                            <option value="record_personnel" {{ old('role', $editUser->role) === 'record_personnel' ? 'selected' : '' }}>RECORD PERSONNEL</option>
                            @if(auth()->user()->role === 'super_admin')
                                <optgroup label="High Level Roles">
                                    <option value="asds" {{ old('role', $editUser->role) === 'asds' ? 'selected' : '' }}>ASST. SCHOOLS DIVISION SUPERINTENDENT</option>
                                    <option value="sds" {{ old('role', $editUser->role) === 'sds' ? 'selected' : '' }}>SCHOOLS DIVISION SUPERINTENDENT</option>
                                    <option value="sgod_chief" {{ old('role', $editUser->role) === 'sgod_chief' ? 'selected' : '' }}>CHEIF EDUCATION SUPERVISOR, SGOD</option>
                                    <option value="cid_chief" {{ old('role', $editUser->role) === 'cid_chief' ? 'selected' : '' }}>CHIEF EDUCATION SUPERVISOR, CID</option>
                                    <option value="ao" {{ old('role', $editUser->role) === 'ao' ? 'selected' : '' }}>ADMIN OFFICER IV - ADMIN OFFICE</option>
                                </optgroup>
                            @endif
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-section-title mt-4"><i class="fas fa-shield-alt" style="margin-right: 8px; color: var(--primary, #0f4c75);"></i> Security</div>
                <div class="form-grid-inner">
                    <div class="form-group full-width">
                        <label class="form-label">Email (DepEd Gmail) <span class="text-danger">*</span></label>
                        <input type="email" name="gmail" class="form-control @error('gmail') is-invalid @enderror" required
                            value="{{ old('gmail', $editUser->gmail) }}" placeholder="username@deped.gov.ph">
                        @error('gmail')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">Reset Password <span style="text-transform:none; font-weight:normal; letter-spacing:0;" class="text-muted">(Blank to keep)</span></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" minlength="6">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••">
                    </div>
                </div>

                <div class="mt-4 pt-4 text-center" style="display: flex; gap: 16px; border-top: 2px solid #f1f5f9;">
                    <a href="{{ route('admin.manage-users') }}" class="btn-back" style="flex: 1; text-align: center;">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn-save" style="flex: 2;">
                        <i class="fas fa-check-circle"></i> Save All Changes
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const officeSelect = document.getElementById('office_select');
            if (officeSelect) {
                new TomSelect('#office_select', {
                    create: true,
                    placeholder: 'Select or type office...'
                });
            }
        });

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview = document.getElementById('imgPreview');
                    const placeholder = document.getElementById('imgPlaceholder');
                    preview.src = e.target.result;
                    preview.style.display = 'flex';
                    if (placeholder) placeholder.style.display = 'none';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Status toggle interaction
        const statusOptions = document.querySelectorAll('.status-option');
        statusOptions.forEach(option => {
            option.addEventListener('click', function () {
                const input = this.querySelector('input[type="radio"]');
                if (!input) return;
                
                input.checked = true; // Ensure input is checked
                
                statusOptions.forEach(opt => {
                    opt.classList.remove('active', 'inactive');
                });
                
                if (input.value === '1') {
                    this.classList.add('active');
                } else {
                    this.classList.add('inactive');
                }
            });
        });
    </script>
@endpush