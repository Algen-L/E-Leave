@extends('layouts.sdo')

@section('title', 'Apply for Leave')
@section('page-title', 'Apply for Leave')
@php
    function getIconForLeaveName($name) {
        if (str_contains($name, 'Vacation') || str_contains($name, 'Privilege')) return '<i class="fas fa-plane text-blue-500"></i>';
        if (str_contains($name, 'Sick')) return '<i class="fas fa-notes-medical text-red-500"></i>';
        if (str_contains($name, 'Women')) return '<i class="fas fa-female text-pink-500"></i>';
        if (str_contains($name, 'Study')) return '<i class="fas fa-book-reader text-indigo-500"></i>';
        if (str_contains($name, 'Force') || str_contains($name, 'Mandatory')) return '<i class="fas fa-exclamation-circle text-orange-500"></i>';
        if (str_contains($name, 'Paternity')) return '<i class="fas fa-baby text-blue-400"></i>';
        if (str_contains($name, 'Maternity')) return '<i class="fas fa-baby-carriage text-pink-400"></i>';
        if (str_contains($name, 'Solo Parent')) return '<i class="fas fa-hands-holding-child text-purple-500"></i>';
        if (str_contains($name, 'Calamity')) return '<i class="fas fa-house-damage text-yellow-600"></i>';
        if ($name === 'Others') return '<i class="fas fa-ellipsis-h text-gray-500"></i>';
        return '<i class="fas fa-file-alt text-gray-400"></i>';
    }

    function getDescriptionForLeaveName($name) {
        if (str_contains($name, 'Vacation')) return 'Time off for rest, travel, or personal matters.';
        if (str_contains($name, 'Sick')) return 'For illness, injury, or medical appointments.';
        if (str_contains($name, 'Women')) return 'Special leave benefits for women-specific illnesses.';
        if (str_contains($name, 'Study')) return 'For higher education or board/bar exam preparations.';
        if (str_contains($name, 'Force')) return 'Mandatory leave required by agency policy.';
        if (str_contains($name, 'Maternity')) return '105 days leave for female employees upon childbirth.';
        if (str_contains($name, 'Paternity')) return '7 days leave for married male employees.';
        if (str_contains($name, 'CTO') || str_contains($name, 'Overtime')) return 'Compensatory Time Off in lieu of overtime pay.';
        if ($name === 'Others') return 'Select for other specific leave categories.';
        return 'Standard leave application.';
    }
@endphp

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/apply-leave.css') }}?v={{ time() }}">
@endpush

@section('content')
    <div class="leave-form-container">

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-4">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Hero Preloader Overlay -->
        <div class="hero-preloader">
            <div class="preloader-calendar-wrapper">
                <div class="calendar-hero-animation">
                    <div class="calendar-base">
                        <div class="cal-grid">
                            <div class="cal-dot"></div>
                            <div class="cal-dot"></div>
                            <div class="cal-dot"></div>
                            <div class="cal-dot"></div>
                            <div class="cal-dot highlight"></div>
                            <div class="cal-dot highlight"></div>
                            <div class="cal-dot strike"></div>
                            <div class="cal-dot"></div>
                            <div class="cal-dot"></div>
                        </div>
                        <div class="calendar-cursor">
                            <i class="fas fa-mouse-pointer"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-wrapper-hidden">
        <div class="apply-header">
            <div class="apply-header-top">
                <div class="apply-title-group">
                    <div class="apply-header-icon">
                        <div class="calendar-hero-animation static">
                            <div class="calendar-base">
                                <div class="cal-grid">
                                    <div class="cal-dot" style="opacity: 1; animation: none;"></div>
                                    <div class="cal-dot" style="opacity: 1; animation: none;"></div>
                                    <div class="cal-dot" style="opacity: 1; animation: none;"></div>
                                    <div class="cal-dot" style="opacity: 1; animation: none;"></div>
                                    <div class="cal-dot highlight" style="opacity: 1; animation: none;"></div>
                                    <div class="cal-dot highlight" style="opacity: 1; animation: none;"></div>
                                    <div class="cal-dot strike" style="opacity: 1; animation: none;"></div>
                                    <div class="cal-dot" style="opacity: 1; animation: none;"></div>
                                    <div class="cal-dot" style="opacity: 1; animation: none;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="apply-title animate__animated animate__fadeIn">
                        <h1>New Leave Application</h1>
                        <p>Fill in the details below to submit your request.</p>
                    </div>
                </div>

                <div class="apply-header-actions animate__animated animate__backInDown" style="animation-delay: 2.0s;">
                    <a href="{{ route('user.leave.history') }}" class="history-link">
                        <i class="fas fa-clock-rotate-left"></i> <span>View History</span>
                    </a>
                </div>
            </div>
            <div class="header-divider"></div>
        </div>


        <form action="{{ route('user.leave.submit') }}" method="POST">
            @csrf

            <!-- Section 1: Type of Leave -->
            <div class="apply-card animate__animated animate__backInUp animate__fast" style="animation-delay: 2.0s;">
                <div class="apply-card-header">
                    <div class="apply-card-icon animate__animated animate__zoomIn animate__fast" style="animation-delay: 2.1s;">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3>Type of Leave</h3>
                </div>
                <div class="apply-card-body">
                    <div class="form-group-custom">
                        <label class="form-label">Select Leave Type</label>
                        <div class="form-select-wrapper searchable-dropdown-container">
                            <div class="searchable-input-wrapper">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" id="leave_type_search" class="form-select searchable-input" 
                                    placeholder="-- Choose a leave type --" autocomplete="off" readonly
                                    onfocus="this.removeAttribute('readonly')" onclick="showDropdown(event)">
                                <i class="fas fa-chevron-down toggle-icon" onclick="toggleDropdown(event)"></i>
                            </div>
                            
                            <!-- Custom Dropdown Results -->
                            <div id="leave_type_results" class="custom-dropdown-results">
                                <div class="dropdown-scroll-area">
                                    @foreach($standardTypes as $type)
                                        <div class="dropdown-item" data-id="{{ $type->id }}" data-name="{{ $type->type_name }}" onclick="selectLeaveType(this)">
                                            <div class="leave-option-item">
                                                <div class="leave-option-icon">
                                                    <!-- Icon will be injected by JS or hardcoded if preferred -->
                                                    {!! getIconForLeaveName($type->type_name) !!}
                                                </div>
                                                <div class="leave-option-content">
                                                    <div class="leave-option-title">{{ $type->type_name }}</div>
                                                    <div class="leave-option-desc">{{ getDescriptionForLeaveName($type->type_name) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="dropdown-item" data-id="Others" data-name="Others" onclick="selectLeaveType(this)">
                                        <div class="leave-option-item">
                                            <div class="leave-option-icon">
                                                <i class="fas fa-ellipsis-h text-gray-500"></i>
                                            </div>
                                            <div class="leave-option-content">
                                                <div class="leave-option-title">Others</div>
                                                <div class="leave-option-desc">Select for other specific leave categories.</div>
                                            </div>
                                        </div>
                                    <div id="no_results" class="dropdown-item text-center py-4 text-slate-400 italic" style="display: none;">
                                        No matching leave types found.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="leave_type_id" id="real_leave_type_id" value="{{ old('leave_type_id') }}" required>
                        <p class="text-sm text-blue-500 mt-2 italic" id="type_description"></p>
                    </div>

                    <!-- Dynamic Details Container -->
                    <!-- Vacation Leave Details -->
                    <div id="details_container" class="mt-2">
                        <!-- Vacation Leave Details -->
                        <div id="details_vacation" class="details-group">
                            <h4 class="text-slate-800 font-bold mb-3 flex items-center gap-2">
                                <i class="fas fa-plane"></i> Vacation / Special Privilege Details
                            </h4>
                            <div class="flex flex-col gap-2">
                                <label class="radio-option flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="vacation_loc_type" value="Philippines"
                                        onchange="toggleInput('vacation_specify', false)" class="w-4 h-4 text-slate-600 border-gray-300">
                                    <span class="text-slate-600">Within the Philippines</span>
                                </label>
                                <div class="ml-7 w-full max-w-xl">
                                    <label class="radio-option flex items-center gap-3 cursor-pointer mb-2">
                                        <input type="radio" name="vacation_loc_type" value="Abroad"
                                            onchange="toggleInput('vacation_specify', true)" class="w-4 h-4 text-slate-600 border-gray-300">
                                        <span class="text-slate-600">Abroad (Specify)</span>
                                    </label>
                                    <input type="text" name="vacation_loc_details" id="vacation_specify"
                                        class="form-input" placeholder="Enter specific location..." disabled>
                                </div>
                            </div>
                        </div>

                        <!-- Sick Leave Details -->
                        <div id="details_sick" class="details-group">
                            <h4 class="text-slate-800 font-bold mb-3 flex items-center gap-2">
                                <i class="fas fa-notes-medical"></i> Sick Leave Details
                            </h4>
                            <div class="flex flex-col gap-4">
                                <div>
                                    <label class="radio-option flex items-center gap-3 cursor-pointer mb-2">
                                        <input type="radio" name="sick_loc_type" value="Hospital"
                                            onchange="toggleInput('sick_hospital', true); toggleInput('sick_outpatient', false)"
                                            class="w-4 h-4 text-slate-600 border-gray-300">
                                        <span class="text-slate-600">In Hospital (Specify Illness)</span>
                                    </label>
                                    <input type="text" name="sick_illness" id="sick_hospital"
                                        class="form-input ml-7 md:w-3/4" placeholder="Enter illness details..." disabled>
                                </div>
                                <div>
                                    <label class="radio-option flex items-center gap-3 cursor-pointer mb-2">
                                        <input type="radio" name="sick_loc_type" value="Out Patient"
                                            onchange="toggleInput('sick_outpatient', true); toggleInput('sick_hospital', false)"
                                            class="w-4 h-4 text-slate-600 border-gray-300">
                                        <span class="text-slate-600">Out Patient (Specify Illness)</span>
                                    </label>
                                    <input type="text" name="sick_illness" id="sick_outpatient"
                                        class="form-input ml-7 md:w-3/4" placeholder="Enter illness details..." disabled>
                                </div>
                            </div>
                        </div>

                        <!-- Women Leave Details -->
                        <div id="details_women" class="details-group">
                            <h4 class="text-slate-800 font-bold mb-3">Special Leave Benefits for Women</h4>
                            <label class="form-label text-slate-600 font-normal normal-case mb-2">Specify Illness</label>
                            <input type="text" name="women_illness" class="form-input" placeholder="Enter specific illness...">
                        </div>

                        <!-- Study Leave Details -->
                        <div id="details_study" class="details-group">
                            <h4 class="text-slate-800 font-bold mb-3">Study Leave Details</h4>
                            <div class="flex flex-col gap-2">
                                <label class="radio-option flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="study_type" value="Masters"
                                        onchange="toggleInput('study_specify', false)" class="w-4 h-4 text-slate-600 border-gray-300">
                                    <span class="text-slate-600">Completion of Master's Degree</span>
                                </label>
                                <label class="radio-option flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="study_type" value="Bar"
                                        onchange="toggleInput('study_specify', false)" class="w-4 h-4 text-slate-600 border-gray-300">
                                    <span class="text-slate-600">BAR/Board Examination Review</span>
                                </label>
                                <div class="ml-7 w-full max-w-xl mt-1">
                                    <label class="radio-option flex items-center gap-3 mb-2 cursor-pointer">
                                        <input type="radio" name="study_type" value="Other"
                                            onchange="toggleInput('study_specify', true)" class="w-4 h-4 text-slate-600 border-gray-300">
                                        <span class="text-slate-600">Other (Specify)</span>
                                    </label>
                                    <input type="text" name="study_details" id="study_specify"
                                        class="form-input" placeholder="Enter details..." disabled>
                                </div>
                            </div>
                        </div>

                        <!-- Other Details -->
                        <div id="details_others" class="details-group">
                            <h4 class="text-slate-800 font-bold mb-3">Others Details</h4>
                            <div class="flex flex-col gap-2">
                                @foreach($otherTypes as $other)
                                    <label class="radio-option flex items-center gap-3 cursor-pointer">
                                        <input type="radio" name="others_type" value="{{ $other->id }}" data-is-custom="true"
                                            onchange="setOtherLeaveId(this, '{{ $other->id }}')" class="w-4 h-4 text-slate-600 border-gray-300">
                                        <span class="text-slate-600">{{ $other->type_name }}</span>
                                    </label>
                                @endforeach
                                <div class="w-full max-w-xl pl-2 pt-2 border-t border-gray-100 mt-2">
                                    <label class="radio-option flex items-center gap-3 cursor-pointer mb-2">
                                        <input type="radio" name="others_type" value="Specify" data-is-custom="false"
                                            onchange="setOtherLeaveId(this, 'specify')" class="w-4 h-4 text-slate-600 border-gray-300">
                                        <span class="text-slate-600">Specify Purpose</span>
                                    </label>
                                    <input type="text" name="other_purpose" id="others_specify"
                                        class="form-input ml-7" placeholder="Specify purpose..." disabled
                                        oninput="document.getElementById('real_leave_type_id').value = 'Specify:' + this.value">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Dates & Duration -->
            <div class="apply-card animate__animated animate__backInUp animate__fast" style="animation-delay: 2.1s;">
                <div class="apply-card-header">
                    <div class="apply-card-icon animate__animated animate__zoomIn animate__fast" style="animation-delay: 2.2s;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>Dates & Duration</h3>
                </div>
                <div class="apply-card-body">
                    <div class="date-grid">
                        <div>
                            <label class="form-label">Select Dates</label>
                            <div class="input-with-icon">
                                <i class="fas fa-calendar"></i>
                                <input type="text" id="date_picker" class="form-input form-input-padded" placeholder="Click to select dates...">
                            </div>
                            <span class="helper-text">You can select multiple non-consecutive dates.</span>
                        </div>
                        <div>
                            <label class="form-label">Total Days Applied</label>
                            <div class="input-with-icon">
                                <i class="fas fa-calculator" style="color: #94a3b8; position: absolute; left: 14px; top: 18px;"></i>
                                <input type="number" name="days_applied" id="days_applied" class="form-input form-input-padded form-input-readonly" step="0.5" min="0.5" required readonly>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="selected_dates" id="selected_dates" required>
                </div>
            </div>

            <!-- Section 3: Commutation -->
            <div class="apply-card animate__animated animate__backInUp animate__fast" style="animation-delay: 2.2s;">
                <div class="apply-card-header">
                    <div class="apply-card-icon animate__animated animate__zoomIn animate__fast" style="animation-delay: 2.3s;">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <h3>Commutation Option</h3>
                </div>
                <div class="apply-card-body">
                    <label class="commutation-option">
                        <div class="toggle-switch">
                            <input type="checkbox" name="commutation" value="Requested">
                            <span class="slider"></span>
                        </div>
                        <div class="commutation-content">
                            <span class="commutation-label">Request commutation of leave credits</span>
                            <span class="commutation-desc">Convert your leave credits into monetary value for this request.</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Section 4: Approval Workflow -->
            <div class="apply-card animate__animated animate__backInUp animate__fast" style="animation-delay: 2.3s;">
                <div class="apply-card-header">
                    <div class="apply-card-icon animate__animated animate__zoomIn animate__fast" style="animation-delay: 2.4s;">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h3>Approval Workflow</h3>
                </div>
                <div class="apply-card-body">
                    <div class="workflow-timeline">
                        <!-- Step 1: Recommending Officer -->
                        <div class="workflow-step">
                            <div class="workflow-node animate__animated animate__zoomIn" style="animation-delay: 2.5s;"></div>

                            <span class="step-label">Step 1 – Recommending Approval</span>
                            <div class="workflow-content">
                                <div class="officer-avatar">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div class="officer-info">
                                    @if($user->recommendingOfficer)
                                        <p class="officer-name">{{ $user->recommendingOfficer->full_name }}</p>
                                        <p class="officer-pos">{{ str_replace('_', ' ', $user->recommendingOfficer->role) }}</p>
                                    @else
                                        <p class="officer-name text-red-500 italic">Not configured</p>
                                        <p class="officer-pos">Please set in your profile</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Final Approver -->
                        <div class="workflow-step">
                            <div class="workflow-node animate__animated animate__zoomIn" style="animation-delay: 2.5s;"></div>

                            <span class="step-label">Step 2 – Final Approval</span>
                            <div class="workflow-content">
                                <div class="officer-avatar">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <div class="officer-info">
                                    @if($user->approvingOfficer)
                                        <p class="officer-name">{{ $user->approvingOfficer->full_name }}</p>
                                        <p class="officer-pos">{{ str_replace('_', ' ', $user->approvingOfficer->role) }}</p>
                                    @else
                                        <p class="officer-name text-red-500 italic">Not configured</p>
                                        <p class="officer-pos">Please set in your profile</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="apply-actions animate__animated animate__backInUp animate__fast" style="animation-delay: 2.4s;">
                <a href="{{ route('user.leave.history') }}" class="btn-cancel animate__animated animate__backInUp animate__fast" style="animation-delay: 2.5s;">Cancel</a>
                <button type="submit" class="btn-submit animate__animated animate__backInUp animate__fast" style="animation-delay: 2.6s;">
                    <i class="fas fa-paper-plane"></i>
                    Submit Application
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>        document.addEventListener('DOMContentLoaded', function () {
            window.fp = flatpickr("#date_picker", {
                mode: "multiple",
                dateFormat: "Y-m-d",
                // minDate is now set dynamically in updateDateRestrictions
                onChange: function (selectedDates, dateStr, instance) {
                    updateCalculations(selectedDates);
                    saveFormData();
                }
            });

            // Inline Search Logic
            const searchInput = document.getElementById('leave_type_search');
            const resultsContainer = document.getElementById('leave_type_results');
            const dropdownItems = document.querySelectorAll('.dropdown-item');

            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                let hasResults = false;

                dropdownItems.forEach(item => {
                    const name = item.getAttribute('data-name');
                    if (name && name.toLowerCase().includes(query)) {
                        item.style.display = 'block';
                        hasResults = true;
                    } else if (name) { // don't hide the 'no_results' div here
                        item.style.display = 'none';
                    }
                });

                document.getElementById('no_results').style.display = hasResults ? 'none' : 'block';
                resultsContainer.classList.add('active');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.searchable-dropdown-container')) {
                    resultsContainer.classList.remove('active');
                    if (searchInput.value === '') {
                        searchInput.setAttribute('readonly', 'readonly');
                    }
                }
            });
            
            // Load persistent data
            loadFormData();

            // Trigger initial details if needed (fallback if loadFormData didn't handle something)
            if (document.getElementById('real_leave_type_id').value && !searchInput.value) {
                // Find existing name if we have an ID (e.g. from old input)
                const initialId = document.getElementById('real_leave_type_id').value;
                const initialItem = Array.from(dropdownItems).find(item => item.getAttribute('data-id') == initialId);
                if (initialItem) {
                    const initialName = initialItem.getAttribute('data-name');
                    searchInput.value = initialName;
                    toggleDetails(initialName, initialId);
                    updateDateRestrictions(initialName);
                }
            }

            // Attach change listeners to all inputs for persistence
            document.querySelectorAll('input, select, textarea').forEach(input => {
                input.addEventListener('change', saveFormData);
                if (input.type === 'text' || input.tagName === 'TEXTAREA') {
                    input.addEventListener('input', saveFormData);
                }
            });

            // Clear storage on form submit
            document.querySelector('form').addEventListener('submit', function() {
                localStorage.removeItem('leave_form_data');
            });
        });

        function saveFormData() {
            const formData = {
                leave_type_search: document.getElementById('leave_type_search').value,
                real_leave_type_id: document.getElementById('real_leave_type_id').value,
                vacation_loc_type: document.querySelector('input[name="vacation_loc_type"]:checked')?.value,
                vacation_loc_details: document.getElementById('vacation_specify').value,
                sick_loc_type: document.querySelector('input[name="sick_loc_type"]:checked')?.value,
                sick_hospital: document.getElementById('sick_hospital').value,
                sick_outpatient: document.getElementById('sick_outpatient').value,
                women_illness: document.querySelector('input[name="women_illness"]')?.value,
                study_type: document.querySelector('input[name="study_type"]:checked')?.value,
                study_specify: document.getElementById('study_specify').value,
                others_type: document.querySelector('input[name="others_type"]:checked')?.value,
                others_specify: document.getElementById('others_specify').value,
                date_picker: document.getElementById('date_picker').value,
                days_applied: document.getElementById('days_applied').value,
                selected_dates: document.getElementById('selected_dates').value,
                commutation: document.querySelector('input[name="commutation"]').checked
            };
            localStorage.setItem('leave_form_data', JSON.stringify(formData));
        }

        function loadFormData() {
            const data = localStorage.getItem('leave_form_data');
            if (!data) return;
            const formData = JSON.parse(data);

            if (formData.leave_type_search) {
                document.getElementById('leave_type_search').value = formData.leave_type_search;
                toggleDetails(formData.leave_type_search, formData.real_leave_type_id);
                updateDateRestrictions(formData.leave_type_search, true);
            }
            if (formData.real_leave_type_id) {
                document.getElementById('real_leave_type_id').value = formData.real_leave_type_id;
            }

            // Radio buttons
            if (formData.vacation_loc_type) {
                const radio = document.querySelector(`input[name="vacation_loc_type"][value="${formData.vacation_loc_type}"]`);
                if (radio) {
                    radio.checked = true;
                    toggleInput('vacation_specify', formData.vacation_loc_type === 'Abroad');
                }
            }
            if (formData.vacation_loc_details) document.getElementById('vacation_specify').value = formData.vacation_loc_details;

            if (formData.sick_loc_type) {
                const radio = document.querySelector(`input[name="sick_loc_type"][value="${formData.sick_loc_type}"]`);
                if (radio) {
                    radio.checked = true;
                    toggleInput('sick_hospital', formData.sick_loc_type === 'Hospital');
                    toggleInput('sick_outpatient', formData.sick_loc_type === 'Out Patient');
                }
            }
            if (formData.sick_hospital) document.getElementById('sick_hospital').value = formData.sick_hospital;
            if (formData.sick_outpatient) document.getElementById('sick_outpatient').value = formData.sick_outpatient;

            if (formData.women_illness) document.querySelector('input[name="women_illness"]').value = formData.women_illness;

            if (formData.study_type) {
                const radio = document.querySelector(`input[name="study_type"][value="${formData.study_type}"]`);
                if (radio) {
                    radio.checked = true;
                    toggleInput('study_specify', formData.study_type === 'Other');
                }
            }
            if (formData.study_specify) document.getElementById('study_specify').value = formData.study_specify;

            if (formData.others_type) {
                const radio = document.querySelector(`input[name="others_type"][value="${formData.others_type}"]`);
                if (radio) {
                    radio.checked = true;
                    setOtherLeaveId(radio, formData.others_type, true);
                }
            }
            if (formData.others_specify) document.getElementById('others_specify').value = formData.others_specify;

            if (formData.date_picker) {
                // document.getElementById('date_picker').value = formData.date_picker;
                if (window.fp) {
                    window.fp.setDate(formData.date_picker.split(','));
                    updateCalculations(window.fp.selectedDates);
                }
            }
            
            if (formData.commutation) document.querySelector('input[name="commutation"]').checked = formData.commutation;
        }

        // Function to restrict dates for COC
        function updateDateRestrictions(leaveTypeName, isInitialLoad = false) {
            if (!window.fp) return;

            // Define leave types that require 5 days advance notice
            const isAdvanceLeave = leaveTypeName && (
                leaveTypeName.includes('Vacation') || 
                leaveTypeName.includes('CTO') || 
                leaveTypeName.toLowerCase().includes('compensatory') ||
                leaveTypeName.includes('Force') || 
                leaveTypeName.includes('Mandatory')
            );

            if (isAdvanceLeave) {
                const today = new Date();
                
                // Set minDate to today to prevent past dates
                window.fp.set('minDate', 'today');

                // 5 days advance notice: Today + 4 days are disabled
                const endOfBlackout = new Date();
                endOfBlackout.setDate(today.getDate() + 4);

                window.fp.set('disable', [
                    {
                        from: today.toISOString().split('T')[0],
                        to: endOfBlackout.toISOString().split('T')[0]
                    }
                ]);
            } else if (leaveTypeName && leaveTypeName.includes('Sick')) {
                // SICK LEAVE: Allow past dates (no minDate)
                window.fp.set('minDate', null);
                window.fp.set('disable', []);
            } else {
                // OTHER LEAVES: Prevent past dates but no 5-day blackout
                window.fp.set('minDate', 'today');
                window.fp.set('disable', []);
            }
            
            // Clear selected dates only if it's a new interaction, not a load
            if (!isInitialLoad) {
                window.fp.clear();
                updateCalculations([]);
            }
        }

        function showDropdown(e) {
            e.stopPropagation();
            const results = document.getElementById('leave_type_results');
            results.classList.add('active');
            document.getElementById('leave_type_search').focus();
        }

        function toggleDropdown(e) {
            e.stopPropagation();
            const results = document.getElementById('leave_type_results');
            results.classList.toggle('active');
            if (results.classList.contains('active')) {
                document.getElementById('leave_type_search').focus();
            }
        }

        function selectLeaveType(element) {
            const id = element.getAttribute('data-id');
            const name = element.getAttribute('data-name');
            const searchInput = document.getElementById('leave_type_search');
            const hiddenInput = document.getElementById('real_leave_type_id');
            const resultsContainer = document.getElementById('leave_type_results');

            searchInput.value = name;
            hiddenInput.value = (name === 'Others') ? '' : id;
            resultsContainer.classList.remove('active');
            
            // Trigger the details logic
            toggleDetails(name, id);
            
            // Trigger date restrictions
            updateDateRestrictions(name);

            saveFormData();
        }

        function updateCalculations(dates) {
            // Format dates as comma-separated string for submission
            // Flatpickr returns Date objects. We convert them to Y-m-d strings.
            const dateStrings = dates.map(date => {
                const offset = date.getTimezoneOffset();
                const localDate = new Date(date.getTime() - (offset * 60 * 1000));
                return localDate.toISOString().split('T')[0];
            });

            document.getElementById('selected_dates').value = dateStrings.join(',');
            document.getElementById('days_applied').value = dates.length;
        }

        function toggleDetails(selectedText, leaveTypeId) {
            // Update hidden leave_type_id input
            const hiddenInput = document.getElementById('real_leave_type_id');
            if (selectedText !== 'Others') {
                hiddenInput.value = leaveTypeId; // Set to the selected standard type ID
            } else {
                hiddenInput.value = ''; // Clear it, user must select sub-option in details
            }
            
            // Hide all details groups first
            document.querySelectorAll('.details-group').forEach(el => el.classList.remove('active'));

            if (!selectedText) return;

            // Logic based on types
            if (includesAny(selectedText, ['Vacation', 'Privilege', 'Mandatory', 'Forced'])) {
                document.getElementById('details_vacation').classList.add('active');
            } else if (includesAny(selectedText, ['Sick'])) {
                document.getElementById('details_sick').classList.add('active');
            } else if (includesAny(selectedText, ['Benefits for Women'])) {
                document.getElementById('details_women').classList.add('active');
            } else if (includesAny(selectedText, ['Study'])) {
                document.getElementById('details_study').classList.add('active');
            } else if (selectedText === 'Others') {
                document.getElementById('details_others').classList.add('active');
            }
        }

        // Helper to set ID when selecting from "Others" radio group
        function setOtherLeaveId(radio, value, isInitialLoad = false) {
            const hiddenInput = document.getElementById('real_leave_type_id');
            const specifyInput = document.getElementById('others_specify');

            if (value === 'specify') {
                specifyInput.disabled = false;
                if (!isInitialLoad) specifyInput.focus();
            } else {
                // It is a real dynamic Leave Type ID (e.g., 5, 8, etc.)
                specifyInput.disabled = true;
                specifyInput.value = '';
                hiddenInput.value = value;
                
                // Trigger date restrictions for "Other" types (like COC)
                const label = radio.closest('label').querySelector('span').innerText;
                updateDateRestrictions(label, isInitialLoad);
            }
            if (!isInitialLoad) saveFormData();
        }

        function includesAny(text, keywords) {
            return keywords.some(keyword => text.includes(keyword));
        }

        function toggleInput(inputId, shouldEnable) {
            const input = document.getElementById(inputId);
            if (input) {
                input.disabled = !shouldEnable;
                if (shouldEnable) input.focus();
            }
        }


    </script>
@endpush
