@extends('layouts.sdo')

@section('title', 'Reporting Hub')
@section('page-title', 'Document & Report Generation')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile-redesign.css') }}?v={{ time() }}">
    <style>
        .print-hub-wrapper {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .hub-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            padding: 30px;
            margin-bottom: 30px;
        }
        .hub-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin: -30px -30px 30px -30px;
            padding: 25px 30px;
            background: var(--primary-gradient, linear-gradient(135deg, #1b4a9a 0%, #3b66bc 100%));
            border-radius: 19px 19px 0 0;
            color: #fff;
        }
        .hub-icon {
            width: 54px;
            height: 54px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(5px);
            color: #fff;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .hub-title-box h2 {
            margin: 0;
            font-size: 1.4rem;
            color: #fff;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .hub-title-box p {
            margin: 2px 0 0 0;
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
            font-weight: 400;
        }
        
        /* Adjusting existing modular component styles for full page */
        .print-filters-box {
            background: #f8fafc;
            border-radius: 15px;
            padding: 25px;
            margin-top: 20px;
            border: 1px solid #e2e8f0;
        }
        
        .hub-footer-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
        }

        .btn-hub-primary {
            padding: 12px 30px;
            background: linear-gradient(135deg, #1b4a9a, #1b4a9a);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .btn-hub-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(37, 99, 235, 0.3);
        }

        .btn-hub-secondary {
            padding: 12px 25px;
            background: #fff;
            color: #64748b;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-hub-secondary:hover {
            background: #f8fafc;
            color: #1e293b;
        }
    </style>
@endpush

@section('content')
<div class="print-hub-wrapper animate__animated animate__fadeIn">
    
    <div class="hub-card">
        <div class="hub-header">
            <div class="hub-icon">
                <i class="fas fa-print"></i>
            </div>
            <div class="hub-title-box">
                <h2>Document & Report Generation</h2>
                <p>Configure filters to generate official leave cards, summary tables, or bulk application packages.</p>
            </div>
        </div>

        <div class="hub-body">
            <!-- Form Type Selection -->
            <label class="dash-label mb-3" style="display: block; font-size: 1rem;">Select Report Type</label>
            <div class="print-type-grid mb-6">
                @if(auth()->user()->isRecordPersonnel() || auth()->user()->role === 'super_admin')
                    <div class="print-type-card active" onclick="switchPrintType('leave_summary', this)">
                        <i class="fas fa-table"></i>
                        <span>Leave Summary Table</span>
                    </div>
                    <div class="print-type-card" onclick="switchPrintType('bulk_zip', this)">
                        <i class="fas fa-file-archive"></i>
                        <span>Bulk Application PDF (ZIP)</span>
                    </div>
                @else
                    <div class="print-type-card active" onclick="switchPrintType('leave_card', this)">
                        <i class="fas fa-id-card"></i>
                        <span>Leave Card</span>
                    </div>
                    <div class="print-type-card" onclick="switchPrintType('leave_summary', this)">
                        <i class="fas fa-table"></i>
                        <span>Leave Summary Table</span>
                    </div>
                    <div class="print-type-card" onclick="switchPrintType('leave_individual', this)">
                        <i class="fas fa-user-tag"></i>
                        <span>Leave Individual Table</span>
                    </div>
                @endif
            </div>

            <!-- Dynamic Filters -->
            <div id="printFiltersContainer" class="print-filters-box">
                
                @if(!auth()->user()->isRecordPersonnel())
                <!-- LEAVE CARD FILTERS -->
                <div id="filter_leave_card" class="print-filter-group active">
                    <div class="dash-form-grid">
                        <div class="dash-field" style="grid-column: span 2;">
                            <label class="dash-label">Search User</label>
                            <div class="search-select-wrapper">
                                <input type="text" class="dash-input user-search-input" placeholder="Type name to search..." onkeyup="filterUserList(this)">
                                <div class="user-dropdown-list">
                                    @foreach($allUsers as $u)
                                        <div class="user-option" onclick="selectUserForPrint('{{ $u->id }}', '{{ $u->full_name }}', this)">
                                            <span class="u-name">{{ $u->full_name }}</span>
                                            <span class="u-role">{{ ucfirst($u->role) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="print_user_id" id="print_user_id_leave_card">
                            </div>
                        </div>
                        <div class="dash-field">
                            <label class="dash-label">Start Date</label>
                            <input type="date" class="dash-input" id="print_start_date_leave_card">
                        </div>
                        <div class="dash-field">
                            <label class="dash-label">End Date</label>
                            <input type="date" class="dash-input" id="print_end_date_leave_card">
                        </div>
                    </div>
                </div>
                @endif

                <!-- LEAVE SUMMARY TABLE FILTERS -->
                <div id="filter_leave_summary" class="print-filter-group {{ (auth()->user()->isRecordPersonnel() || auth()->user()->role === 'super_admin') ? 'active' : '' }}">
                    <div class="dash-form-grid">
                        @if(auth()->user()->isRecordPersonnel() || auth()->user()->role === 'super_admin')
                        <div class="dash-field" style="grid-column: span 2;">
                            <div class="row g-2">
                                <div class="col-md-12">
                                    <label class="dash-label">Filter by Office</label>
                                    <select class="dash-input" id="print_office_summary_rp">
                                        <option value="ALL">All Offices</option>
                                        @foreach($allOffices as $o)
                                            <option value="{{ $o->name ?? 'N/A' }}">{{ $o->name ?? 'N/A' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="dash-field" style="grid-column: span 2;">
                            <label class="dash-label">Report Range Type</label>
                            <div class="range-toggle-group">
                                <button type="button" class="range-toggle-btn active" onclick="switchRangeType('yearly', this)">Yearly</button>
                                <button type="button" class="range-toggle-btn" onclick="switchRangeType('monthly', this)">Month Range</button>
                            </div>
                        </div>
                        <div id="yearly_range_box" class="dash-field" style="grid-column: span 2;">
                            <label class="dash-label">Select Year</label>
                            <select class="dash-input" id="print_year_summary">
                                @for($y = date('Y'); $y >= 2020; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div id="monthly_range_box" class="dash-form-grid" style="grid-column: span 2; display: none;">
                            <div class="dash-field">
                                <label class="dash-label">From Month</label>
                                <input type="month" class="dash-input" id="print_month_from_summary">
                            </div>
                            <div class="dash-field">
                                <label class="dash-label">To Month</label>
                                <input type="month" class="dash-input" id="print_month_to_summary">
                            </div>
                        </div>
                        
                        @if(!auth()->user()->isRecordPersonnel() && auth()->user()->role !== 'super_admin')
                        <div class="dash-field" style="grid-column: span 2;">
                            <label class="dash-label">Office Filter</label>
                            <select class="dash-input" id="print_office_summary">
                                <option value="ALL">ALL OFFICES</option>
                                <option value="OSDS">OSDS</option>
                                <option value="CID">CID</option>
                                <option value="SGOD">SGOD</option>
                            </select>
                        </div>
                        @endif
                    </div>
                </div>

                @if(!auth()->user()->isRecordPersonnel())
                <!-- LEAVE INDIVIDUAL TABLE FILTERS -->
                <div id="filter_leave_individual" class="print-filter-group">
                    <div class="dash-form-grid">
                        <div class="dash-field" style="grid-column: span 2;">
                            <label class="dash-label">Search User</label>
                            <div class="search-select-wrapper">
                                <input type="text" class="dash-input user-search-input" placeholder="Type name to search..." onkeyup="filterUserList(this)">
                                <div class="user-dropdown-list">
                                    @foreach($allUsers as $u)
                                        <div class="user-option" onclick="selectUserForPrint('{{ $u->id }}', '{{ $u->full_name }}', this)">
                                            <span class="u-name">{{ $u->full_name }}</span>
                                            <span class="u-role">{{ ucfirst($u->role) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="print_user_id" id="print_user_id_individual">
                            </div>
                        </div>
                        <div class="dash-field">
                            <label class="dash-label">Start Date</label>
                            <input type="date" class="dash-input" id="print_start_date_individual">
                        </div>
                        <div class="dash-field">
                            <label class="dash-label">End Date</label>
                            <input type="date" class="dash-input" id="print_end_date_individual">
                        </div>
                        <div class="dash-field" style="grid-column: span 2;">
                            <label class="dash-label">Application Progress (Status)</label>
                            <select class="dash-input" id="print_status_individual">
                                <option value="ALL">All Status</option>
                                <option value="Pending">Pending</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </div>
                    </div>
                </div>
                @endif

                @if(auth()->user()->isRecordPersonnel() || auth()->user()->role === 'super_admin')
                <!-- BULK ZIP FILTERS -->
                <div id="filter_bulk_zip" class="print-filter-group">
                    <div class="dash-form-grid">
                        <div class="dash-field" style="grid-column: span 2;">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="dash-label">Filter by Office</label>
                                    <select class="dash-input" id="bulk_office_filter" onchange="filterBulkUsersByOffice(this.value)">
                                        <option value="ALL">All Offices</option>
                                        @foreach($allOffices as $o)
                                            <option value="{{ $o->name ?? 'N/A' }}">{{ $o->name ?? 'N/A' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="dash-label">Quick Search</label>
                                    <input type="text" class="dash-input" placeholder="Name..." onkeyup="searchBulkUsers(this.value)">
                                </div>
                            </div>
                        </div>
                        
                        <div class="dash-field" style="grid-column: span 2;">
                            <label class="dash-label">Include Users</label>
                            <div class="bulk-user-selection-box">
                                <div class="bulk-user-list-checkboxes" id="bulk_user_list" style="max-height: 200px;">
                                    @foreach($subordinateUsers ?? $allUsers as $u)
                                        <div class="bulk-user-item" data-office="{{ $u->office_station }}">
                                            <div class="d-flex align-items-center gap-2" style="padding: 5px 10px;">
                                                <input type="checkbox" class="bulk-user-checkbox" value="{{ $u->id }}" checked>
                                                <div class="u-info" style="line-height:1.2">
                                                    <div class="u-name" style="font-weight: 500; font-size: 0.9rem;">{{ $u->full_name }}</div>
                                                    <div class="u-off" style="font-size: 0.75rem; color: #64748b;">{{ $u->office_station ?: 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="bulk-selection-controls mt-2 d-flex justify-content-between align-items-center">
                                    <span id="bulk-selected-count" style="font-size: 0.8rem; color: #64748b;">All users selected</span>
                                    <div class="d-flex gap-3">
                                        <button type="button" class="btn btn-sm btn-link p-0" onclick="toggleAllBulkUsers(true)" style="font-size: 0.8rem; text-decoration: none;">Select All</button>
                                        <button type="button" class="btn btn-sm btn-link p-0" onclick="toggleAllBulkUsers(false)" style="font-size: 0.8rem; text-decoration: none; color: #ef4444;">Deselect All</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="dash-field">
                            <label class="dash-label">From Date</label>
                            <input type="date" class="dash-input" id="bulk_start_date">
                        </div>
                        <div class="dash-field">
                            <label class="dash-label">To Date</label>
                            <input type="date" class="dash-input" id="bulk_end_date">
                        </div>
                    </div>
                </div>
                <style>
                    .bulk-user-selection-box { border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; background: #fff; }
                    .bulk-user-list-checkboxes { border: 1px solid #f1f5f9; border-radius: 8px; background: #fff; overflow-y: auto; }
                    .bulk-user-item { border-bottom: 1px solid #f8fafc; transition: all 0.2s ease; }
                    .bulk-user-item:hover { background: #f1f5f9; }
                    .bulk-user-checkbox { width: 18px; height: 18px; cursor: pointer; }
                </style>
                @endif
            </div>
        </div>

        <div class="hub-footer-actions">
            <a href="{{ url()->previous() ?: route('hr.profile') }}" class="btn-hub-secondary">
                <i class="fas fa-arrow-left me-2"></i> Go Back
            </a>
            <button type="button" class="btn-hub-primary" onclick="triggerHubPrintGeneration()">
                <i class="fas fa-file-export"></i> Generate Document
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentPrintType = "{{ (auth()->user()->isRecordPersonnel() || auth()->user()->role === 'super_admin') ? 'leave_summary' : 'leave_card' }}";

    function switchPrintType(type, element) {
        currentPrintType = type;
        
        // Update cards
        document.querySelectorAll('.print-type-card').forEach(card => card.classList.remove('active'));
        element.classList.add('active');
        
        // Update filters
        document.querySelectorAll('.print-filter-group').forEach(group => group.classList.remove('active'));
        document.getElementById(`filter_${type}`).classList.add('active');
    }

    function switchRangeType(type, element) {
        const yearlyBox = document.getElementById('yearly_range_box');
        const monthlyBox = document.getElementById('monthly_range_box');
        
        document.querySelectorAll('.range-toggle-btn').forEach(btn => btn.classList.remove('active'));
        element.classList.add('active');
        
        if (type === 'yearly') {
            yearlyBox.style.display = 'flex';
            monthlyBox.style.display = 'none';
        } else {
            yearlyBox.style.display = 'none';
            monthlyBox.style.display = 'grid';
        }
    }

    function filterUserList(input) {
        const filter = input.value.toLowerCase();
        const dropdown = input.nextElementSibling;
        const options = dropdown.getElementsByClassName('user-option');
        
        let visibleCount = 0;
        for (let i = 0; i < options.length; i++) {
            const name = options[i].getElementsByClassName('u-name')[0].innerText.toLowerCase();
            if (name.includes(filter)) {
                options[i].style.display = 'flex';
                visibleCount++;
            } else {
                options[i].style.display = 'none';
            }
        }
        
        dropdown.style.display = (filter && visibleCount > 0) ? 'block' : 'none';
    }

    function selectUserForPrint(id, name, element) {
        const wrapper = element.closest('.search-select-wrapper');
        const input = wrapper.querySelector('.user-search-input');
        const hiddenInput = wrapper.querySelector('input[type="hidden"]');
        const dropdown = wrapper.querySelector('.user-dropdown-list');
        
        input.value = name;
        hiddenInput.value = id;
        dropdown.style.display = 'none';
    }

    function triggerHubPrintGeneration() {
        let baseUrl = '';
        let params = new URLSearchParams();
        
        if (currentPrintType === 'leave_card') {
            baseUrl = "{{ route('hr.reports.leave-card') }}";
            params.append('userId', document.getElementById('print_user_id_leave_card').value);
            params.append('start', document.getElementById('print_start_date_leave_card').value);
            params.append('end', document.getElementById('print_end_date_leave_card').value);
            
            if (!document.getElementById('print_user_id_leave_card').value) {
                alert('Please select a user first.');
                return;
            }
        } else if (currentPrintType === 'leave_individual') {
            baseUrl = "{{ route('hr.reports.leave-individual') }}";
            params.append('userId', document.getElementById('print_user_id_individual').value);
            params.append('start', document.getElementById('print_start_date_individual').value);
            params.append('end', document.getElementById('print_end_date_individual').value);
            params.append('status', document.getElementById('print_status_individual').value);
            
            if (!document.getElementById('print_user_id_individual').value) {
                alert('Please select a user first.');
                return;
            }
        } else if (currentPrintType === 'leave_summary') {
            baseUrl = "{{ route('hr.reports.leave-summary') }}";
            const isYearly = document.querySelector('.print-filter-group.active .range-toggle-btn.active').innerText === 'Yearly';
            params.append('rangeType', isYearly ? 'yearly' : 'monthly');
            
            if (isYearly) {
                params.append('year', document.getElementById('print_year_summary').value);
            } else {
                params.append('monthFrom', document.getElementById('print_month_from_summary').value);
                params.append('monthTo', document.getElementById('print_month_to_summary').value);
                
                if (!document.getElementById('print_month_from_summary').value || !document.getElementById('print_month_to_summary').value) {
                    alert('Please select both Start and End months.');
                    return;
                }
            }

            const isRP = "{{ (auth()->user()->isRecordPersonnel() || auth()->user()->role === 'super_admin') ? '1' : '0' }}";
            if (isRP == "1") {
                params.set('office', document.getElementById('print_office_summary_rp').value);
            } else {
                params.append('office', document.getElementById('print_office_summary').value);
            }
        } else if (currentPrintType === 'bulk_zip') {
            baseUrl = "{{ route('records.bulk-download') }}";
            
            const checkedUsers = Array.from(document.querySelectorAll('.bulk-user-checkbox:checked')).map(cb => cb.value);
            if (checkedUsers.length === 0) {
                alert('Please select at least one user.');
                return;
            }
            
            checkedUsers.forEach(id => params.append('user_ids[]', id));
            params.append('office', document.getElementById('bulk_office_filter').value);
            params.append('start_date', document.getElementById('bulk_start_date').value);
            params.append('end_date', document.getElementById('bulk_end_date').value);
        }
        
        const finalUrl = baseUrl + '?' + params.toString();
        
        // Behavior based on type: Download for Zip, Open in New Tab for PDF Preview
        if (currentPrintType === 'bulk_zip') {
            window.location.href = finalUrl;
        } else {
            window.open(finalUrl, '_blank');
        }
    }

    // --- Record Personnel Helpers ---
    function filterBulkUsersByOffice(office) {
        const items = document.querySelectorAll('.bulk-user-item');
        items.forEach(item => {
            const itemOffice = item.getAttribute('data-office');
            if (office === 'ALL' || itemOffice === office) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
                item.querySelector('.bulk-user-checkbox').checked = false;
            }
        });
        updateBulkSelectedCount();
    }

    function searchBulkUsers(query) {
        const items = document.querySelectorAll('.bulk-user-item');
        const office = document.getElementById('bulk_office_filter').value;
        const lowerQuery = query.toLowerCase();

        items.forEach(item => {
            const name = item.querySelector('.u-name').innerText.toLowerCase();
            const itemOffice = item.getAttribute('data-office');
            
            const officeMatches = (office === 'ALL' || itemOffice === office);
            const nameMatches = name.includes(lowerQuery);

            item.style.display = (officeMatches && nameMatches) ? 'block' : 'none';
        });
    }

    function toggleAllBulkUsers(checked) {
        const items = document.querySelectorAll('.bulk-user-item');
        items.forEach(item => {
            if (item.style.display !== 'none') {
                item.querySelector('.bulk-user-checkbox').checked = checked;
            }
        });
        updateBulkSelectedCount();
    }

    function updateBulkSelectedCount() {
        const total = document.querySelectorAll('.bulk-user-checkbox').length;
        const selected = document.querySelectorAll('.bulk-user-checkbox:checked').length;
        const countSpan = document.getElementById('bulk-selected-count');
        if (countSpan) {
            if (selected === total) countSpan.innerText = 'All users selected';
            else if (selected === 0) countSpan.innerText = 'No users selected';
            else countSpan.innerText = `${selected} of ${total} selected`;
        }
    }

    // Initialize searches and selection counters
    document.addEventListener('DOMContentLoaded', () => {
        updateBulkSelectedCount();
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('bulk-user-checkbox')) {
                updateBulkSelectedCount();
            }
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.search-select-wrapper')) {
            document.querySelectorAll('.user-dropdown-list').forEach(d => d.style.display = 'none');
        }
    });
</script>
@endpush
@endsection
