@extends('layouts.sdo')

@section('title', 'Edit User')
@section('page-title', 'Edit User: ' . $editUser->full_name)

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .edit-user-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .edit-user-header {
            background: linear-gradient(135deg, #0f4c75 0%, #1b6ca8 100%);
            color: white;
            padding: 24px 28px;
            border: none;
        }

        .edit-user-header h5 {
            margin: 0;
            font-weight: 600;
            font-size: 1.25rem;
        }

        .edit-user-header p {
            margin: 4px 0 0;
            opacity: 0.85;
            font-size: 0.9rem;
        }

        .edit-user-body {
            padding: 28px;
        }

        .form-section {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .form-section-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-section-title i {
            color: #0f4c75;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #334155;
            margin-bottom: 6px;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            padding: 10px 14px;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0f4c75;
            box-shadow: 0 0 0 3px rgba(15, 76, 117, 0.1);
        }

        .form-control:disabled,
        .form-control[readonly] {
            background-color: #f1f5f9;
            color: #64748b;
        }

        .status-toggle {
            display: flex;
            gap: 12px;
        }

        .status-option {
            flex: 1;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .status-option:hover {
            border-color: #cbd5e1;
        }

        .status-option.active {
            border-color: #10b981;
            background: rgba(16, 185, 129, 0.05);
        }

        .status-option.inactive {
            border-color: #ef4444;
            background: rgba(239, 68, 68, 0.05);
        }

        .status-option input {
            display: none;
        }

        .status-option .status-icon {
            font-size: 1.5rem;
            margin-bottom: 4px;
        }

        .status-option .status-label {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .btn-save {
            background: linear-gradient(135deg, #0f4c75 0%, #1b6ca8 100%);
            border: none;
            padding: 12px 28px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.2s;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(15, 76, 117, 0.3);
        }

        .btn-back {
            padding: 12px 28px;
            font-weight: 600;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            color: #64748b;
        }

        .btn-back:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1b6ca8 0%, #0f4c75 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: 700;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .meta-info {
            display: flex;
            gap: 24px;
            margin-top: 12px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.85);
        }

        .ts-wrapper .ts-control {
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            padding: 8px 14px;
        }
    </style>
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card edit-user-card">
                <div class="edit-user-header">
                    <div class="d-flex align-items-center">
                        <div class="user-avatar">
                            {{ strtoupper(substr($editUser->full_name, 0, 1)) }}
                        </div>
                        <div>
                            <h5><i class="fas fa-user-edit me-2"></i>Edit User Account</h5>
                            <p class="mb-0">{{ $editUser->full_name }} &bull;
                                {{ ucfirst(str_replace('_', ' ', $editUser->role)) }}
                            </p>
                            <div class="meta-info">
                                <div class="meta-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    Joined {{ $editUser->created_at->format('M d, Y') }}
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-envelope"></i>
                                    {{ $editUser->gmail }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body edit-user-body">
                    <form action="{{ route('admin.users.update', $editUser) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Personal Information -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-user"></i> Personal Information
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                                        name="username" value="{{ old('username', $editUser->username) }}" required>
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Split Name Fields -->
                                <div class="col-md-4">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                        name="first_name" value="{{ old('first_name', $editUser->first_name) }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" class="form-control @error('middle_name') is-invalid @enderror"
                                        name="middle_name" value="{{ old('middle_name', $editUser->middle_name) }}">
                                    @error('middle_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                        name="last_name" value="{{ old('last_name', $editUser->last_name) }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control @error('gmail') is-invalid @enderror"
                                        name="gmail" value="{{ old('gmail', $editUser->gmail) }}" required>
                                    @error('gmail')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Position</label>
                                    <input type="text" class="form-control @error('position') is-invalid @enderror"
                                        name="position" value="{{ old('position', $editUser->position) }}">
                                    @error('position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Work Information -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-building"></i> Work Information
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Employee No. <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('employee_number') is-invalid @enderror"
                                        name="employee_number"
                                        value="{{ old('employee_number', $editUser->employee_number) }}"
                                        placeholder="Enter 7-digit ID" pattern="\d{7}" maxlength="7"
                                        title="Must be exactly 7 digits" required>
                                    @error('employee_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Office/Station</label>
                                    <select class="form-select" id="office_station" name="office_station">
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

                                <div class="col-md-4">
                                    <label class="form-label">Role <span class="text-danger">*</span></label>
                                    <select class="form-select @error('role') is-invalid @enderror" name="role" required>
                                        <option value="user" {{ old('role', $editUser->role) === 'user' ? 'selected' : '' }}>
                                            USER</option>
                                        <option value="head_hr" {{ old('role', $editUser->role) === 'head_hr' ? 'selected' : '' }}>HUMAN RESOURCE PERSONNEL</option>
                                        @if(auth()->user()->role === 'super_admin')
                                            <optgroup label="High Level Roles">
                                                <option value="asds" {{ old('role', $editUser->role) === 'asds' ? 'selected' : '' }}>ASST. SCHOOLS DIVISION SUPERINTENDENT</option>
                                                <option value="sds" {{ old('role', $editUser->role) === 'sds' ? 'selected' : '' }}>SCHOOLS DIVISION SUPERINTENDENT</option>
                                                <option value="sgod_chief" {{ old('role', $editUser->role) === 'sgod_chief' ? 'selected' : '' }}>CHEIF EDUCATION SUPERVISOR, SGOD</option>
                                                <option value="cid_chief" {{ old('role', $editUser->role) === 'cid_chief' ? 'selected' : '' }}>CHIEF EDUCATION SUPERVISOR, CID</option>
                                                <option value="ao" {{ old('role', $editUser->role) === 'ao' ? 'selected' : '' }}>
                                                    ADMIN OFFICER IV - ADMIN OFFICE</option>
                                            </optgroup>
                                        @endif
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Account Status -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-shield-alt"></i> Account Status
                            </div>
                            <div class="status-toggle">
                                <label class="status-option {{ old('is_active', $editUser->is_active) ? 'active' : '' }}">
                                    <input type="radio" name="is_active" value="1" {{ old('is_active', $editUser->is_active) ? 'checked' : '' }}>
                                    <div class="status-icon text-success"><i class="fas fa-check-circle"></i></div>
                                    <div class="status-label">Active</div>
                                    <div class="text-muted small">User can log in</div>
                                </label>
                                <label
                                    class="status-option {{ !old('is_active', $editUser->is_active) ? 'inactive' : '' }}">
                                    <input type="radio" name="is_active" value="0" {{ !old('is_active', $editUser->is_active) ? 'checked' : '' }}>
                                    <div class="status-icon text-danger"><i class="fas fa-times-circle"></i></div>
                                    <div class="status-label">Inactive</div>
                                    <div class="text-muted small">Account disabled</div>
                                </label>
                            </div>
                        </div>

                        <!-- Password Reset (Optional) -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-key"></i> Password (Leave blank to keep current)
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">New Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        name="password" placeholder="Enter new password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" name="password_confirmation"
                                        placeholder="Confirm new password">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3 pt-3">
                            <button type="submit" class="btn btn-primary btn-save">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                            <a href="{{ route('admin.manage-users') }}" class="btn btn-back">
                                <i class="fas fa-arrow-left me-2"></i>Back to Users
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new TomSelect('#office_station', {
                allowEmptyOption: true,
                placeholder: 'Select Office/Station'
            });

            // Status toggle interaction
            const statusOptions = document.querySelectorAll('.status-option');
            statusOptions.forEach(option => {
                option.addEventListener('click', function () {
                    statusOptions.forEach(opt => {
                        opt.classList.remove('active', 'inactive');
                    });
                    if (this.querySelector('input').value === '1') {
                        this.classList.add('active');
                    } else {
                        this.classList.add('inactive');
                    }
                });
            });
        });
    </script>
@endpush