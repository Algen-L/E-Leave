@extends('layouts.sdo')

@section('title', 'Apply for Leave')
@section('page-title', 'Apply for Leave')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="{{ asset('css/leave_apply.css') }}">
    <style>
        .hidden {
            display: none !important;
        }
    </style>
@endpush

@section('content')
    <div class="leave-form-container">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl relative mb-6 flex items-center gap-3 animate-fade-in"
                role="alert">
                <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                <div>
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl relative mb-6 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-exclamation-circle text-rose-500"></i>
                    <strong class="font-bold">Please correct the following errors:</strong>
                </div>
                <ul class="list-disc list-inside text-sm pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="apply-header-card">
            <div class="header-title-group">
                <h1>New Leave Application</h1>
                <p>Complete the form below to submit your request for approval.</p>
            </div>
            <a href="{{ route('user.leave.history') }}" class="history-btn">
                <i class="fas fa-history"></i>
                <span>View My History</span>
            </a>
        </div>

        <form action="{{ route('user.leave.submit') }}" method="POST" id="leaveApplicationForm">
            @csrf

            <!-- Leave Type Section -->
            <div class="form-card-premium">
                <div class="section-label">
                    <div class="icon-badge bg-blue-soft">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <h3>6.A Type of Leave</h3>
                </div>

                <div class="form-group">
                    <label class="input-label-premium" for="leave_type_select">Select Category of Leave</label>
                    <div class="relative">
                        <select name="selected_option_only" id="leave_type_select"
                            class="select-premium appearance-none cursor-pointer" onchange="toggleDetails()">
                            <option value="" disabled selected>-- Select from Standard Leave Categories --</option>
                            @foreach($standardTypes as $type)
                                <option value="{{ $type->id }}" data-name="{{ $type->type_name }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->type_name }}
                                </option>
                            @endforeach
                            <option value="Others" data-name="Others" {{ old('leave_type_id') === 'Others' ? 'selected' : '' }}>Other Leave Types / Purpose</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-6 text-slate-400">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <input type="hidden" name="leave_type_id" id="real_leave_type_id" value="{{ old('leave_type_id') }}"
                        required>
                    <p id="type_description" class="text-xs text-blue-600 mt-2 font-medium italic"></p>
                    <!-- Filing Rule Alert -->
                    <!-- Filing Rule Alert -->
                    <div id="filing_rule_container"
                        class="hidden mt-3 p-3 rounded-lg bg-amber-50 border border-amber-200 flex items-start gap-3 animate-fade-in">
                        <i class="fas fa-info-circle text-amber-500 mt-0.5"></i>
                        <div class="text-xs text-amber-800 leading-relaxed" id="filing_rule_text"></div>
                    </div>

                    <!-- Exemption Note for < 10 VL -->
                    @if(isset($vlBalance) && $vlBalance < 10)
                        <div id="exemption_note"
                            class="hidden mt-3 p-4 rounded-xl bg-indigo-50 border border-indigo-100 flex items-start gap-4 shadow-sm animate-fade-in">
                            <i class="fas fa-shield-alt text-indigo-500 mt-1"></i>
                            <div class="flex-1">
                                <h4 class="text-xs font-bold text-indigo-900 uppercase tracking-tight mb-1">10-Day Exemption
                                    Rule</h4>
                                <p class="text-xs text-indigo-700 leading-normal">
                                    Your current VL balance is <strong>{{ number_format($vlBalance, 2) }}</strong>. Because it
                                    is below 10 days, you are <strong>exempt</strong> from the mandatory 5-day forced leave
                                    requirement. Your credits will not be forfeited at year-end.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Dynamic Details Box -->
                <div id="details_container">
                    <!-- Vacation/Privilege Details -->
                    <div id="details_vacation" class="details-box">
                        <div class="flex items-center gap-2 mb-4 text-blue-700">
                            <i class="fas fa-info-circle"></i>
                            <span class="font-bold text-sm tracking-wide uppercase">Location Details</span>
                        </div>
                        <div class="options-grid">
                            <label class="selection-card" id="card_vac_ph">
                                <input type="radio" name="vacation_loc_type" value="Philippines"
                                    onchange="handleSelection(this, 'card_vac_ph', 'details_vacation'); toggleInput('vacation_specify', false)">
                                <div class="flex flex-col">
                                    <span>Within the Philippines</span>
                                </div>
                            </label>
                            <label class="selection-card" id="card_vac_abroad">
                                <input type="radio" name="vacation_loc_type" value="Abroad"
                                    onchange="handleSelection(this, 'card_vac_abroad', 'details_vacation'); toggleInput('vacation_specify', true)">
                                <div class="flex flex-col">
                                    <span>Abroad</span>
                                    <span class="desc">Specify location below</span>
                                </div>
                            </label>
                        </div>
                        <div class="mt-4">
                            <input type="text" name="vacation_loc_details" id="vacation_specify" class="input-premium"
                                placeholder="Enter specific destination..." disabled>
                        </div>
                    </div>

                    <!-- Sick Leave Details -->
                    <div id="details_sick" class="details-box">
                        <div class="flex items-center gap-2 mb-4 text-rose-700">
                            <i class="fas fa-notes-medical"></i>
                            <span class="font-bold text-sm tracking-wide uppercase">Treatment Details</span>
                        </div>
                        <div class="options-grid">
                            <label class="selection-card" id="card_sick_hospital">
                                <input type="radio" name="sick_loc_type" value="Hospital"
                                    onchange="handleSelection(this, 'card_sick_hospital', 'details_sick'); toggleInput('sick_hospital', true); toggleInput('sick_outpatient', false)">
                                <div class="flex flex-col">
                                    <span>In Hospital</span>
                                    <span class="desc">Specify illness</span>
                                </div>
                            </label>
                            <label class="selection-card" id="card_sick_outpatient">
                                <input type="radio" name="sick_loc_type" value="Out Patient"
                                    onchange="handleSelection(this, 'card_sick_outpatient', 'details_sick'); toggleInput('sick_outpatient', true); toggleInput('sick_hospital', false)">
                                <div class="flex flex-col">
                                    <span>Outpatient</span>
                                    <span class="desc">Specify illness</span>
                                </div>
                            </label>
                        </div>
                        <div class="mt-4 grid grid-cols-1 gap-3">
                            <input type="text" name="sick_illness" id="sick_hospital" class="input-premium hidden-input"
                                placeholder="What is the illness? (For In-Hospital)" disabled>
                            <input type="text" name="sick_illness" id="sick_outpatient" class="input-premium hidden-input"
                                placeholder="What is the illness? (For Outpatient)" disabled>
                        </div>
                    </div>

                    <!-- Special Women Details -->
                    <div id="details_women" class="details-box">
                        <div class="mb-4 text-emerald-700 font-bold text-sm uppercase">Benefits for Women Details</div>
                        <input type="text" name="women_illness" class="input-premium"
                            placeholder="Specify gynecological illness/surgery details...">
                    </div>

                    <!-- Study Leave Details -->
                    <div id="details_study" class="details-box">
                        <div class="mb-4 text-indigo-700 font-bold text-sm uppercase">Course/Review Details</div>
                        <div class="options-grid">
                            <label class="selection-card" id="card_study_master">
                                <input type="radio" name="study_type" value="Masters"
                                    onchange="handleSelection(this, 'card_study_master', 'details_study'); toggleInput('study_specify', false)">
                                <span>Master's Completion</span>
                            </label>
                            <label class="selection-card" id="card_study_bar">
                                <input type="radio" name="study_type" value="Bar"
                                    onchange="handleSelection(this, 'card_study_bar', 'details_study'); toggleInput('study_specify', false)">
                                <span>BAR/Board Review</span>
                            </label>
                            <label class="selection-card" id="card_study_other">
                                <input type="radio" name="study_type" value="Other"
                                    onchange="handleSelection(this, 'card_study_other', 'details_study'); toggleInput('study_specify', true)">
                                <span>Other Course</span>
                            </label>
                        </div>
                        <div class="mt-4">
                            <input type="text" name="study_details" id="study_specify" class="input-premium"
                                placeholder="Enter other course details..." disabled>
                        </div>
                    </div>

                    <!-- Others Case -->
                    <div id="details_others" class="details-box">
                        <div class="mb-4 text-slate-700 font-bold text-sm uppercase">Select Specific Purpose</div>
                        <div class="options-grid">
                            @foreach($otherTypes as $other)
                                <label class="selection-card" id="card_other_{{ $other->id }}">
                                    <input type="radio" name="others_type" value="{{ $other->id }}" data-is-custom="true"
                                        onchange="handleSelection(this, 'card_other_{{ $other->id }}', 'details_others'); setOtherLeaveId(this, '{{ $other->id }}')">
                                    <span>{{ $other->type_name }}</span>
                                </label>
                            @endforeach


                            <label class="selection-card" id="card_other_specify">
                                <input type="radio" name="others_type" value="Specify" data-is-custom="false"
                                    onchange="handleSelection(this, 'card_other_specify', 'details_others'); setOtherLeaveId(this, 'specify')">
                                <span>Specify Purpose</span>
                            </label>
                        </div>
                        <div class="mt-4">
                            <input type="text" name="other_purpose" id="others_specify" class="input-premium"
                                placeholder="Please specify the type of leave or purpose..." disabled
                                oninput="updateOtherSpec(this.value)">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dates & Duration -->
            <div class="form-card-premium">
                <div class="section-label">
                    <div class="icon-badge bg-indigo-soft">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3>6.B Dates & Duration</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="md:col-span-2">
                        <label class="input-label-premium">Pick Application Dates</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-calendar-alt"></i>
                            <input type="text" id="date_picker" class="input-premium input-with-icon cursor-pointer"
                                placeholder="Click to choose single or multiple dates...">
                        </div>
                        <p class="text-xs text-slate-400 mt-2 italic font-medium">Tip: You can select multiple,
                            non-consecutive dates.</p>
                    </div>

                    <div>
                        <label class="input-label-premium">Calculation Result</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-clock"></i>
                            <input type="number" name="days_applied" id="days_applied"
                                class="input-premium input-with-icon font-bold text-center bg-indigo-50 border-indigo-100"
                                step="0.5" min="0.5" required readonly value="0">
                            <div
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-indigo-400 font-bold text-sm pointer-events-none">
                                DAYS</div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="selected_dates" id="selected_dates" required>

                <div class="pt-8 border-t border-slate-100">
                    <label class="input-label-premium mb-4 text-slate-800">6.C Commutation</label>
                    <div class="options-grid">
                        <label class="selection-card" id="card_commutation">
                            <input type="checkbox" name="commutation" value="Requested"
                                onchange="this.parentElement.classList.toggle('selected')">
                            <div class="flex flex-col">
                                <span>Requested</span>
                                <span class="desc">Check if requesting payment for leave credits</span>
                            </div>
                        </label>
                        <div class="flex items-center text-slate-400 italic text-sm px-4">
                            Note: This will be indicated on Section 6.C of the official Form 6.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approval Routing -->
            <div class="form-card-premium">
                <div class="section-label">
                    <div class="icon-badge bg-emerald-soft">
                        <i class="fas fa-route"></i>
                    </div>
                    <h3>7. Approval Workflow</h3>
                </div>

                <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-6 mb-8 flex items-start gap-4">
                    <div class="bg-white p-2 rounded-xl text-indigo-500 shadow-sm border border-indigo-100">
                        <i class="fas fa-user-shield text-lg"></i>
                    </div>
                    <div>
                        <p class="text-sm text-indigo-900 leading-relaxed">
                            Your application is automatically routed to your assigned officials.
                            Verify your reporting officers below. Incorrect routing? Update in
                            <a href="{{ route('user.profile') }}" class="font-bold text-indigo-600 hover:underline">User
                                Profile</a>.
                        </p>
                    </div>
                </div>

                <div class="workflow-track">
                    <div class="officer-item">
                        <div class="officer-avatar">
                            @if($user->recommendingOfficer)
                                {{ strtoupper(substr($user->recommendingOfficer->full_name, 0, 1)) }}
                            @else
                                ?
                            @endif
                        </div>
                        <div class="officer-info">
                            <label class="input-label-premium text-[10px] mb-0 opacity-60">RECOM. APPROVAL (7.A)</label>
                            @if($user->recommendingOfficer)
                                <span class="name">{{ $user->recommendingOfficer->full_name }}</span>
                                <span class="role">{{ str_replace('_', ' ', $user->recommendingOfficer->role) }}</span>
                            @else
                                <span class="name text-rose-500 italic">Not Assigned</span>
                            @endif
                        </div>
                    </div>

                    <div class="officer-item">
                        <div class="officer-avatar">
                            @if($user->approvingOfficer)
                                {{ strtoupper(substr($user->approvingOfficer->full_name, 0, 1)) }}
                            @else
                                ?
                            @endif
                        </div>
                        <div class="officer-info">
                            <label class="input-label-premium text-[10px] mb-0 opacity-60">FINAL APPROVAL (7.B)</label>
                            @if($user->approvingOfficer)
                                <span class="name">{{ $user->approvingOfficer->full_name }}</span>
                                <span class="role">{{ str_replace('_', ' ', $user->approvingOfficer->role) }}</span>
                            @else
                                <span class="name text-rose-500 italic">Not Assigned</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-footer-premium">
                <a href="{{ route('user.home') }}" class="btn-cancel">Exit to Home</a>
                <button type="submit" class="btn-submit-premium">
                    <i class="fas fa-paper-plane"></i>
                    <span>Submit Form 6 Application</span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let picker;

        const FILING_RULES = {
            'Vacation Leave': { days: 5, type: 'advance', text: 'VACATION LEAVE: File at least 5 days in advance.' },
            'Mandatory/Forced Leave': { days: 5, type: 'advance', text: 'MANDATORY OR FORCED LEAVE: At least 5 days in advance.' },
            'Sick Leave': { days: 0, type: 'any', text: 'SICK LEAVE: File immediately upon return to work, or in advance for scheduled medical appointments/operations. (If 5 days or more, attach medical cert)' },
            'Special Privilege Leave': { days: 7, type: 'advance', text: 'SPECIAL PRIVILEGE LEAVE: File at least 1 week (7 days) in advance.' },
            'Solo Parent Leave': { days: 7, type: 'advance', text: 'SOLO PARENT LEAVE: File at least 1 week (7 days) in advance. (Submit valid Solo Parent ID)' },
            'Maternity Leave': { days: 0, type: 'advance', text: 'MATERNITY LEAVE: File before expected date of delivery.' },
            'Paternity Leave': { days: 0, type: 'advance', text: 'PATERNITY LEAVE: File before delivery of spouse.' },
            'Study Leave': { days: 0, type: 'advance', text: 'STUDY LEAVE: File before start of study period. (Requires approval & contract)' },
            'VAWC Leave': { days: null, type: 'any', text: 'VAWC LEAVE: File as needed. (Attach protection order or supporting document)' },
            'Rehabilitation Leave': { days: 0, type: 'any', text: 'REHABILITATION LEAVE: File immediately after injury, or in advance for scheduled treatments. (Attach medical certificate)' },
            'Special Leave Benefits for Women': { days: 0, type: 'advance', text: 'SPECIAL LEAVE BENEFITS FOR WOMEN: File before surgery. (Attach medical certificate)' },
            'Special Emergency (Calamity) Leave': { days: 30, type: 'retro', text: 'SPECIAL EMERGENCY LEAVE: File within 30 days after calamity.' },
            'Terminal Leave': { days: 0, type: 'advance', text: 'TERMINAL LEAVE: File before separation from service.' },
            'Adoption Leave': { days: 0, type: 'any', text: 'ADOPTION LEAVE: File upon approval/delivery of child. (Attach adoption docs)' },
            'Wellness Leave': { days: 0, type: 'advance', text: 'WELLNESS LEAVE: Recommended to file in advance.' },
            'Compensatory Over-Time Credit': { days: 0, type: 'any', text: 'COMPENSATORY OVER-TIME CREDIT (COC): Used in lieu of overtime pay. (Uses earned COC credits)' }
        };

        document.addEventListener('DOMContentLoaded', function () {
            picker = flatpickr("#date_picker", {
                mode: "multiple",
                dateFormat: "Y-m-d",
                onOpen: function (selectedDates, dateStr, instance) {
                    instance.input.classList.add('bg-white');
                },
                onClose: function (selectedDates, dateStr, instance) {
                    instance.input.classList.remove('bg-white');
                },
                onChange: function (selectedDates, dateStr, instance) {
                    updateCalculations(selectedDates);
                }
            });

            // Initialize details if page reloaded with old data
            if (document.getElementById('leave_type_select').value) {
                toggleDetails();
            }
        });

        function updateFilingConstraints(typeName) {
            if (!picker) return;

            // Reset picker
            picker.clear();
            picker.set('minDate', null);
            picker.set('maxDate', null);

            const container = document.getElementById('filing_rule_container');
            const ruleText = document.getElementById('filing_rule_text');

            // Find matching rule
            let match = null;
            for (const key in FILING_RULES) {
                if (typeName.toLowerCase().includes(key.toLowerCase()) || key.toLowerCase().includes(typeName.toLowerCase())) {
                    match = FILING_RULES[key];
                    break;
                }
            }

            if (match) {
                container.classList.remove('hidden');
                ruleText.innerHTML = match.text;

                const today = new Date();
                today.setHours(0, 0, 0, 0);

                if (match.type === 'advance') {
                    const minDate = new Date(today);
                    minDate.setDate(today.getDate() + (match.days || 0));
                    picker.set('minDate', minDate);
                    picker.set('maxDate', null);
                } else if (match.type === 'retro') {
                    picker.set('minDate', null);
                    picker.set('maxDate', today);
                    // If special emergency (30 days limit)
                    if (match.days) {
                        const minDate = new Date(today);
                        minDate.setDate(today.getDate() - match.days);
                        picker.set('minDate', minDate);
                    }
                } else {
                    // 'any'
                    picker.set('minDate', null);
                    picker.set('maxDate', null);
                }
            } else {
                container.classList.add('hidden');
            }
        }

        function updateCalculations(dates) {
            const dateStrings = dates.map(date => {
                const offset = date.getTimezoneOffset();
                const localDate = new Date(date.getTime() - (offset * 60 * 1000));
                return localDate.toISOString().split('T')[0];
            });

            document.getElementById('selected_dates').value = dateStrings.join(',');
            document.getElementById('days_applied').value = dates.length;
        }

        function toggleDetails() {
            const select = document.getElementById('leave_type_select');
            if (!select) return;

            const selectedOption = select.options[select.selectedIndex];
            const selectedText = selectedOption.getAttribute('data-name') || '';
            const leaveTypeId = selectedOption.value;

            // Handle 10-Day Exemption Note visibility
            const exemptionNote = document.getElementById('exemption_note');
            if (exemptionNote) {
                // Only show if selecting Vacation Leave or Mandatory/Forced Leave
                if (includesAny(selectedText, ['Vacation', 'Mandatory', 'Forced'])) {
                    exemptionNote.classList.remove('hidden');
                } else {
                    exemptionNote.classList.add('hidden');
                }
            }

            // Update constraints based on name
            if (selectedText && selectedText !== 'Others') {
                updateFilingConstraints(selectedText);
            }

            // Update hidden leave_type_id input
            const hiddenInput = document.getElementById('real_leave_type_id');
            if (selectedText !== 'Others') {
                hiddenInput.value = leaveTypeId;
            } else {
                hiddenInput.value = ''; // Let others picker set it
            }

            // Reset sub-selections
            document.querySelectorAll('.details-box input[type="radio"]').forEach(r => r.checked = false);
            document.querySelectorAll('.selection-card').forEach(c => c.classList.remove('selected'));
            document.querySelectorAll('.details-box .input-premium').forEach(i => i.disabled = true);

            // Hide all groups
            document.querySelectorAll('.details-box').forEach(el => el.classList.remove('active'));

            if (!selectedText) return;

            // Visual logic
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

        function handleSelection(radio, cardId, contextId) {
            // Remove 'selected' from all in context
            document.querySelectorAll(`#${contextId} .selection-card`).forEach(c => c.classList.remove('selected'));
            // Add to current
            document.getElementById(cardId).classList.add('selected');
        }

        function setOtherLeaveId(radio, value) {
            const hiddenInput = document.getElementById('real_leave_type_id');
            const specifyInput = document.getElementById('others_specify');

            // Get the name for constraints
            const selectedLabel = radio.parentElement.querySelector('span')?.innerText || '';
            updateFilingConstraints(selectedLabel);

            if (value === 'specify') {
                specifyInput.disabled = false;
                specifyInput.focus();
                hiddenInput.value = 'Specify:' + specifyInput.value;
            } else if (value === 'cto') {
                specifyInput.disabled = true;
                specifyInput.value = '';
                hiddenInput.value = 'cto'; // Adjust if system needs specific ID
            } else {
                specifyInput.disabled = true;
                specifyInput.value = '';
                hiddenInput.value = value;
            }
        }

        function updateOtherSpec(val) {
            if (document.getElementById('others_specify').disabled === false) {
                document.getElementById('real_leave_type_id').value = 'Specify:' + val;
            }
        }

        function includesAny(text, keywords) {
            return keywords.some(keyword => text.includes(keyword));
        }

        function toggleInput(inputId, shouldEnable) {
            const input = document.getElementById(inputId);
            if (input) {
                input.disabled = !shouldEnable;
                if (shouldEnable) {
                    input.focus();
                    input.classList.remove('hidden-input');
                }
            }
        }
    </script>
@endpush