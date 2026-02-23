@extends('layouts.sdo')

@section('title', 'Apply for Leave')
@section('page-title', 'Apply for Leave')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .leave-form-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .form-section {
            background: white;
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease-in-out;
        }

        .form-section:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .form-section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
            /* Assuming var exists, else fallback */
            color: #1e3a8a;
            /* Fallback dark blue */
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 16px;
            border-bottom: 2px solid #f1f5f9;
        }

        .details-group {
            display: none;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            margin-top: 20px;
            animation: fadeIn 0.3s ease-out;
        }

        .details-group.active {
            display: block;
        }

        /* Custom Input Enhancements */
        .field-input {
            transition: all 0.2s;
            border-color: #e2e8f0;
        }

        .field-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .radio-option {
            padding: 8px 12px;
            border-radius: 8px;
            transition: background-color 0.2s;
        }

        .radio-option:hover {
            background-color: #f1f5f9;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@section('content')
    <div class="leave-form-container">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">New Leave Application</h1>
                <p class="text-gray-500">Fill in the details below to submit your request.</p>
            </div>
            <a href="{{ route('user.leave.history') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                <i class="fas fa-history mr-1"></i> View History
            </a>
        </div>

        <form action="{{ route('user.leave.submit') }}" method="POST">
            @csrf

            <!-- Leave Type Section -->
            <div class="form-section">
                <h2 class="form-section-title text-blue-900">
                    <div class="bg-blue-100 p-2 rounded-lg text-blue-600">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    6.A Type of Leave
                </h2>

                <div class="form-group-custom">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Select Leave Type</label>
                    <div class="relative">
                        <select name="selected_option_only" id="leave_type_select"
                            class="field-input w-full p-3 bg-white border rounded-xl appearance-none cursor-pointer text-gray-700 font-medium"
                            onchange="toggleDetails()">
                            <option value="" disabled selected>-- Choose a leave type --</option>
                            @foreach($standardTypes as $type)
                                <option value="{{ $type->id }}" data-name="{{ $type->type_name }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->type_name }}
                                </option>
                            @endforeach
                            <option value="Others" data-name="Others" {{ old('leave_type_id') === 'Others' ? 'selected' : '' }}>Others</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <input type="hidden" name="leave_type_id" id="real_leave_type_id" value="{{ old('leave_type_id') }}"
                        required>
                    <p class="text-sm text-blue-500 mt-2 italic" id="type_description"></p>
                </div>

                <!-- Dynamic Details Container -->
                <div id="details_container">
                    <!-- Details groups content kept as logic is robust, just ensure spacing handled by global css -->

                    <!-- Vacation Leave Details -->
                    <div id="details_vacation" class="details-group">
                        <h4 class="text-blue-700 font-bold mb-4 flex items-center gap-2">
                            <i class="fas fa-plane"></i> Vacation / Special Privilege Details
                        </h4>
                        <div class="flex flex-col gap-3">
                            <label class="radio-option flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="vacation_loc_type" value="Philippines"
                                    onchange="toggleInput('vacation_specify', false)" class="w-5 h-5 text-blue-600">
                                <span class="text-gray-700 font-medium">Within the Philippines</span>
                            </label>
                            <div class="ml-8 w-full max-w-xl"> <!-- Indented container for the abroad option -->
                                <label class="radio-option flex items-center gap-3 cursor-pointer mb-2">
                                    <input type="radio" name="vacation_loc_type" value="Abroad"
                                        onchange="toggleInput('vacation_specify', true)" class="w-5 h-5 text-blue-600">
                                    <span class="text-gray-700 font-medium">Abroad (Specify)</span>
                                </label>
                                <input type="text" name="vacation_loc_details" id="vacation_specify"
                                    class="field-input w-full p-2 border rounded-md"
                                    placeholder="Enter specific location..." disabled>
                            </div>
                        </div>
                    </div>

                    <!-- Sick Leave Details -->
                    <div id="details_sick" class="details-group">
                        <h4 class="text-red-600 font-bold mb-4 flex items-center gap-2">
                            <i class="fas fa-notes-medical"></i> Sick Leave Details
                        </h4>
                        <div class="flex flex-col gap-4">
                            <!-- Hospital -->
                            <div class="bg-white p-4 rounded-lg border border-gray-100">
                                <label class="radio-option flex items-center gap-3 cursor-pointer mb-2">
                                    <input type="radio" name="sick_loc_type" value="Hospital"
                                        onchange="toggleInput('sick_hospital', true); toggleInput('sick_outpatient', false)"
                                        class="w-5 h-5 text-red-500">
                                    <span class="text-gray-700 font-medium">In Hospital (Specify Illness)</span>
                                </label>
                                <input type="text" name="sick_illness" id="sick_hospital"
                                    class="field-input w-full p-2 border rounded-md ml-8 md:w-3/4"
                                    placeholder="Enter illness details..." disabled>
                            </div>

                            <!-- Out Patient -->
                            <div class="bg-white p-4 rounded-lg border border-gray-100">
                                <label class="radio-option flex items-center gap-3 cursor-pointer mb-2">
                                    <input type="radio" name="sick_loc_type" value="Out Patient"
                                        onchange="toggleInput('sick_outpatient', true); toggleInput('sick_hospital', false)"
                                        class="w-5 h-5 text-red-500">
                                    <span class="text-gray-700 font-medium">Out Patient (Specify Illness)</span>
                                </label>
                                <input type="text" name="sick_illness" id="sick_outpatient"
                                    class="field-input w-full p-2 border rounded-md ml-8 md:w-3/4"
                                    placeholder="Enter illness details..." disabled>
                            </div>
                        </div>
                    </div>

                    <!-- Women Leave Details -->
                    <div id="details_women" class="details-group">
                        <h4 class="text-pink-600 font-bold mb-3">Special Leave Benefits for Women</h4>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Specify Illness</label>
                        <input type="text" name="women_illness" class="field-input w-full p-3 border rounded-lg"
                            placeholder="Specify illness...">
                    </div>

                    <!-- Study Leave Details -->
                    <div id="details_study" class="details-group">
                        <h4 class="text-indigo-600 font-bold mb-3">Study Leave Details</h4>
                        <div class="flex flex-col gap-3">
                            <label class="radio-option flex items-center gap-3">
                                <input type="radio" name="study_type" value="Masters"
                                    onchange="toggleInput('study_specify', false)" class="w-5 h-5 text-indigo-500">
                                <span class="text-gray-700">Completion of Master's Degree</span>
                            </label>
                            <label class="radio-option flex items-center gap-3">
                                <input type="radio" name="study_type" value="Bar"
                                    onchange="toggleInput('study_specify', false)" class="w-5 h-5 text-indigo-500">
                                <span class="text-gray-700">BAR/Board Examination Review</span>
                            </label>
                            <div class="ml-8 w-full max-w-xl">
                                <label class="radio-option flex items-center gap-3 mb-2">
                                    <input type="radio" name="study_type" value="Other"
                                        onchange="toggleInput('study_specify', true)" class="w-5 h-5 text-indigo-500">
                                    <span class="text-gray-700">Other (Specify)</span>
                                </label>
                                <input type="text" name="study_details" id="study_specify"
                                    class="field-input w-full p-2 border rounded-md" placeholder="Enter details..."
                                    disabled>
                            </div>
                        </div>
                    </div>

                    <!-- Other Details -->
                    <div id="details_others" class="details-group">
                        <h4 class="text-gray-800 font-bold mb-3">Others Details</h4>

                        <div class="flex flex-col gap-3">
                            <!-- Dynamic Other Leave Types -->
                            @foreach($otherTypes as $other)
                                <label
                                    class="radio-option flex items-center gap-3 cursor-pointer p-2 rounded-lg hover:bg-gray-50 border border-transparent hover:border-gray-200 transition-all">
                                    <input type="radio" name="others_type" value="{{ $other->id }}" data-is-custom="true"
                                        onchange="setOtherLeaveId(this, '{{ $other->id }}')" class="w-5 h-5 text-indigo-600">
                                    <span class="text-gray-700 font-medium">{{ $other->type_name }}</span>
                                </label>
                            @endforeach

                            <!-- Specify (Free Text) -->
                            <div class="w-full max-w-xl pl-2 pt-2 border-t border-gray-100 mt-2">
                                <label class="radio-option flex items-center gap-3 cursor-pointer mb-2">
                                    <input type="radio" name="others_type" value="Specify" data-is-custom="false"
                                        onchange="setOtherLeaveId(this, 'specify')" class="w-5 h-5 text-gray-600">
                                    <span class="text-gray-700 font-medium">Specify Purpose</span>
                                </label>
                                <input type="text" name="other_purpose" id="others_specify"
                                    class="field-input w-full p-2 border rounded-md ml-8" placeholder="Specify purpose..."
                                    disabled
                                    oninput="document.getElementById('real_leave_type_id').value = 'Specify:' + this.value">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date & Duration Section -->
            <div class="form-section">
                <h2 class="form-section-title text-blue-900">
                    <div class="bg-blue-100 p-2 rounded-lg text-blue-600">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    Dates & Duration
                </h2>

                <!-- Dates Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Date Picker -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Select Dates</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar text-gray-400"></i>
                            </div>
                            <input type="text" id="date_picker"
                                class="field-input w-full pl-10 p-3 bg-gray-50 border rounded-xl cursor-pointer hover:bg-white"
                                placeholder="Click here to select dates...">
                        </div>
                        <p class="text-xs text-gray-500 mt-2 ml-1">You can select multiple single dates.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Total Days Applied</label>
                        <div class="relative">
                            <input type="number" name="days_applied" id="days_applied"
                                class="field-input w-full p-3 bg-gray-100 border rounded-xl font-bold text-gray-800 text-lg"
                                step="0.5" min="0.5" required readonly>
                            <div
                                class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-500 font-medium">
                                Days
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Input for submission -->
                    <input type="hidden" name="selected_dates" id="selected_dates" required>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-100">
                    <h4 class="font-bold text-gray-800 mb-4 text-lg">6.C Commutation</h4>
                    <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100">
                        <label
                            class="flex items-start gap-4 cursor-pointer hover:bg-indigo-100 p-2 rounded-lg transition-colors">
                            <div class="flex items-center h-5 mt-1">
                                <input type="checkbox" name="commutation" value="Requested"
                                    class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            </div>
                            <div>
                                <span class="font-bold text-indigo-900 block">Requested</span>
                                <span class="text-sm text-indigo-600">Check this box if you are requesting commutation of
                                    leave credits.</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Approval Workflow Section -->
            <div class="form-section">
                <h2 class="form-section-title text-blue-900">
                    <div class="bg-blue-100 p-2 rounded-lg text-blue-600">
                        <i class="fas fa-user-check"></i>
                    </div>
                    Approval Workflow
                </h2>

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                        <p class="text-sm text-blue-800">
                            This application will be automatically routed to your assigned officers.
                            You can change your routing settings in your <a href="{{ route('user.profile') }}"
                                class="font-bold underline">Profile</a>.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Recommending Officer -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Recommending Approval (7.A)</label>
                        <div class="bg-gray-100 p-3 rounded-xl border border-gray-200">
                            @if($user->recommendingOfficer)
                                <div class="font-bold text-gray-800">{{ $user->recommendingOfficer->full_name }}</div>
                                <div class="text-xs text-gray-500 uppercase">
                                    {{ str_replace('_', ' ', $user->recommendingOfficer->role) }}
                                </div>
                            @else
                                <div class="text-red-500 font-medium italic">Not configured</div>
                            @endif
                        </div>
                    </div>

                    <!-- Final Approver -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Approved For (7.B)</label>
                        <div class="bg-gray-100 p-3 rounded-xl border border-gray-200">
                            @if($user->approvingOfficer)
                                <div class="font-bold text-gray-800">{{ $user->approvingOfficer->full_name }}</div>
                                <div class="text-xs text-gray-500 uppercase">
                                    {{ str_replace('_', ' ', $user->approvingOfficer->role) }}
                                </div>
                            @else
                                <div class="text-red-500 font-medium italic">Not configured</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions flex justify-end gap-4 mt-8">
                <a href="{{ route('user.leave.history') }}"
                    class="px-6 py-3 rounded-xl border border-gray-300 text-gray-600 font-bold hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="btn-save bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-10 py-3 text-lg rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                    <i class="fas fa-paper-plane mr-2"></i> Submit Application
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr("#date_picker", {
                mode: "multiple",
                dateFormat: "Y-m-d",
                onChange: function (selectedDates, dateStr, instance) {
                    updateCalculations(selectedDates);
                }
            });
        });

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

        function toggleDetails() {
            const select = document.getElementById('leave_type_select'); // Get the main dropdown
            if (!select) return; // Exit if not found

            const selectedOption = select.options[select.selectedIndex];
            const selectedText = selectedOption.getAttribute('data-name');
            const leaveTypeId = selectedOption.value;

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
        function setOtherLeaveId(radio, value) {
            const hiddenInput = document.getElementById('real_leave_type_id');
            const specifyInput = document.getElementById('others_specify');

            if (value === 'specify') {
                specifyInput.disabled = false;
                specifyInput.focus();
                // We set a flag or keep empty? Validation requires a valid ID or handling.
                // Backend will likely need to handle 'Specify' case if it's not a real ID.
                // But if user types, we might need a way to pass that.
                // For now, let's assume the backend handles specific logic if ID is missing but 'other_purpose' is set. 
                // Or we create a dummy "Others" leave type in DB.
            } else if (value === 'cto') {
                specifyInput.disabled = true;
                specifyInput.value = '';
                // Backend check: $request->other_purpose === 'COC COMPENSATORY OVERTIME CREDIT'
                // We need to pass something that passes 'exists:leave_types,id' OR modify backend validation.
            } else {
                // It is a real dynamic Leave Type ID (e.g., 5, 8, etc.)
                specifyInput.disabled = true;
                specifyInput.value = '';
                hiddenInput.value = value;
            }
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