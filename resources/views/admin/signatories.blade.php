@extends('layouts.sdo')

@section('title', 'Offices & Signatories')

@section('page-title', 'Offices & Signatories Management')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/signatories.css') }}?v={{ time() }}">
<style>
    .page-intro {
        animation: fadeInDown 0.6s ease-out;
    }

    .signatories-grid .sig-card, .offices-grid .office-category-card {
        opacity: 0;
        animation: backInDown 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    @foreach(range(1, 10) as $i)
        .signatories-grid .sig-card:nth-child({{ $i }}), 
        .offices-grid .office-category-card:nth-child({{ $i }}) {
            animation-delay: {{ 0.1 + ($i * 0.1) }}s;
        }
    @endforeach

    /* Tabs Styling */
    .management-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        background: rgba(255, 255, 255, 0.5);
        padding: 6px;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        backdrop-filter: blur(10px);
        width: fit-content;
    }

    .tab-trigger {
        padding: 10px 24px;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        background: transparent;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .tab-trigger i {
        font-size: 1rem;
    }

    .tab-trigger.active {
        background: white;
        color: var(--primary-blue);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
        animation: fadeIn 0.4s ease-out;
    }

    /* Office Management Specifics */
    .offices-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
    }

    .office-category-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .category-header {
        padding: 16px 20px;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .category-title {
        font-weight: 800;
        color: #1e293b;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .category-title i {
        color: var(--primary-blue);
    }

    .office-list {
        padding: 10px 0;
        max-height: 400px;
        overflow-y: auto;
    }

    .office-item {
        padding: 12px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #f8fafc;
        transition: background 0.2s;
    }

    .office-item:last-child {
        border-bottom: none;
    }

    .office-item:hover {
        background: #f1f5f9;
    }

    .office-name {
        font-size: 0.9rem;
        color: #475569;
        font-weight: 500;
    }

    .office-actions {
        display: flex;
        gap: 8px;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .office-item:hover .office-actions {
        opacity: 1;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        background: white;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.8rem;
    }

    .action-btn:hover {
        background: #f8fafc;
        color: var(--primary-blue);
        border-color: var(--primary-blue);
    }

    .action-btn.btn-delete:hover {
        color: #ef4444;
        border-color: #ef4444;
    }

    .add-office-btn {
        width: 100%;
        padding: 12px;
        background: transparent;
        border: 1px dashed #cbd5e1;
        color: #64748b;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: auto;
    }

    .add-office-btn:hover {
        background: #f8fafc;
        color: var(--primary-blue);
        border-color: var(--primary-blue);
        border-style: solid;
    }

    /* Modal Styling */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .modal.active {
        display: flex;
        animation: fadeIn 0.3s ease-out;
    }

    .modal-content {
        background: white;
        border-radius: 20px;
        width: 100%;
        max-width: 450px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        animation: slideUp 0.3s ease-out;
    }

    .modal-header {
        padding: 20px 24px;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-title {
        font-weight: 800;
        color: #1e293b;
        font-size: 1.1rem;
    }

    .close-modal {
        background: transparent;
        border: none;
        color: #64748b;
        font-size: 1.25rem;
        cursor: pointer;
    }

    .modal-body {
        padding: 24px;
    }

    .modal-footer {
        padding: 16px 24px;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

    @keyframes backInDown {
        0% { transform: translateY(-100px) scale(0.7); opacity: 0; }
        80% { transform: translateY(0px) scale(0.7); opacity: 0.7; }
        100% { transform: scale(1); opacity: 1; }
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="signatories-container">
    <div class="page-intro">
        <p>
            <i class="fas fa-info-circle mr-2 text-blue-500"></i>
            Manage the official list of offices/stations and the signatories responsible for approving leave documents.
        </p>
    </div>

    <div class="management-tabs">
        <button class="tab-trigger active" onclick="switchTab(event, 'signatories')">
            <i class="fas fa-signature"></i> System Signatories
        </button>
        <button class="tab-trigger" onclick="switchTab(event, 'offices')">
            <i class="fas fa-building"></i> Offices & Stations
        </button>
    </div>

    <!-- Signatories Tab -->
    <div id="signatories" class="tab-pane active">
        <form action="{{ route('admin.signatories.update') }}" method="POST" id="signatoriesForm">
            @csrf
            
            <div class="signatories-grid">
                @php
                    $roleIcons = [
                        'CID CHIEF' => 'fas fa-book',
                        'SGOD CHIEF' => 'fas fa-project-diagram',
                        'AO' => 'fas fa-user-tie',
                        'ASDS' => 'fas fa-user-shield',
                        'SDS' => 'fas fa-universal-access',
                        'VERIFIER OF LEAVE CREDITS' => 'fas fa-user-check'
                    ];
                    $roleBadges = [
                        'CID CHIEF' => 'role-badge-cid',
                        'SGOD CHIEF' => 'role-badge-sgod',
                        'AO' => 'role-badge-ao',
                        'ASDS' => 'role-badge-asds',
                        'SDS' => 'role-badge-sds',
                        'VERIFIER OF LEAVE CREDITS' => 'role-badge-verifier'
                    ];
                    $headerClasses = [
                        'CID CHIEF' => 'header-cid',
                        'SGOD CHIEF' => 'header-sgod',
                        'AO' => 'header-ao',
                        'ASDS' => 'header-asds',
                        'SDS' => 'header-sds',
                        'VERIFIER OF LEAVE CREDITS' => 'header-verifier'
                    ];
                @endphp

                @foreach($signatories as $index => $sig)
                    @php $posKey = strtoupper($sig->position); @endphp
                    <div class="sig-card">
                        <div class="sig-card-header {{ $headerClasses[$posKey] ?? 'header-default' }}">
                            <div class="sig-role-info">
                                <div class="sig-role-icon">
                                    <i class="{{ $roleIcons[$posKey] ?? 'fas fa-user' }}"></i>
                                </div>
                                <span class="sig-role-badge {{ $roleBadges[$posKey] ?? 'role-badge-default' }}">
                                    {{ $sig->position }}
                                </span>
                            </div>
                            <div class="sig-active-status">
                                <i class="fas fa-check-circle"></i> Active
                            </div>
                        </div>
                        
                        <div class="sig-card-body">
                            <input type="hidden" name="signatories[{{ $index }}][id]" value="{{ $sig->id }}">
                            <div class="sig-form-grid">
                                <div class="sig-input-group">
                                    <label class="field-label">Full Name</label>
                                    <input type="text" class="field-input" name="signatories[{{ $index }}][name]" value="{{ old("signatories.$index.name", $sig->name) }}" placeholder="Full Name">
                                </div>
                                <div class="sig-input-group">
                                    <label class="field-label">Position Title</label>
                                    <input type="text" class="field-input" name="signatories[{{ $index }}][title]" value="{{ old("signatories.$index.title", $sig->title) }}" placeholder="Position Title">
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="action-bar">
                <div class="action-bar-content">
                    <div class="status-indicator">
                        <div class="status-dot"></div>
                        <span>Updates apply to all generated documents</span>
                    </div>
                    <button type="submit" class="btn-save-fixed">
                        <i class="fas fa-save"></i> Save All Changes
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Offices Tab -->
    <div id="offices" class="tab-pane">
        <div class="offices-grid">
            @php
                $categoryIcons = [
                    'OSDS' => 'fas fa-user-shield',
                    'SGOD' => 'fas fa-chart-line',
                    'CID' => 'fas fa-graduation-cap'
                ];
            @endphp

            @foreach($offices as $category => $items)
            <div class="office-category-card">
                <div class="category-header">
                    <div class="category-title">
                        <i class="{{ $categoryIcons[strtoupper($category)] ?? 'fas fa-building' }}"></i>
                        {{ $category }}
                    </div>
                    <span class="badge" style="background: #e2e8f0; color: #475569; font-size: 0.7rem;">{{ count($items) }} units</span>
                </div>
                <div class="office-list">
                    @foreach($items as $office)
                    <div class="office-item">
                        <span class="office-name">{{ $office->name }}</span>
                        <div class="office-actions">
                                <button class="action-btn" onclick="openEditOfficeModal({{ $office->id }}, '{{ $office->name }}', '{{ $office->category }}')" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="action-btn btn-delete" onclick="openDeleteOfficeModal({{ $office->id }}, '{{ $office->name }}', {{ $office->user_count }})" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                <button class="add-office-btn" onclick="openAddOfficeModal('{{ $category }}')">
                    <i class="fas fa-plus"></i> Add Unit to {{ $category }}
                </button>
            </div>
            @endforeach
            
            <!-- Add New Category Card -->
            <div class="office-category-card" style="border-style: dashed; background: rgba(255,255,255,0.4);">
                <div class="category-header" style="background: transparent; border-bottom: none;">
                    <div class="category-title" style="color: #94a3b8;">
                        <i class="fas fa-folder-plus"></i> New Category
                    </div>
                </div>
                <div style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 20px;">
                    <button class="btn" style="background: var(--primary-blue);" onclick="openAddOfficeModal()">
                        <i class="fas fa-plus mr-2"></i> Create New Office Group
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Office Modal -->
<div class="modal" id="officeModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Add New Office</h3>
            <button class="close-modal" onclick="closeModal()">&times;</button>
        </div>
        <form id="officeForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label class="field-label">Category / Group Name</label>
                    <input type="text" name="category" id="officeCategory" class="form-control" placeholder="e.g. OSDS, SGOD, CID" required>
                </div>
                <div class="form-group">
                    <label class="field-label">Office / Station Name</label>
                    <input type="text" name="name" id="officeName" class="form-control" placeholder="Enter full office name" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" style="background: #f1f5f9; color: #475569;" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn" style="background: var(--primary-blue);" id="submitBtn">Save Office</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Office Modal -->
<div class="modal" id="deleteOfficeModal">
    <div class="modal-content" style="max-width: 400px; border-bottom: 4px solid #ef4444;">
        <div class="modal-header" style="background: #fef2f2;">
            <h3 class="modal-title" style="color: #991b1b;">Confirm Deletion</h3>
            <button class="close-modal" onclick="closeDeleteModal()">&times;</button>
        </div>
        <form id="deleteOfficeForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-body" style="text-align: center; padding: 30px 24px;">
                <div style="width: 60px; height: 60px; background: #fee2e2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 20px;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h4 style="font-weight: 800; color: #1e293b; margin-bottom: 10px;">Delete Office Unit?</h4>
                <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5; margin-bottom: 20px;">
                    Are you sure you want to delete <strong id="deleteOfficeName" style="color: #1e293b;"></strong>? 
                    This action cannot be undone.
                </p>
                
                <div id="userWarningBox" style="background: #fff7ed; border: 1px solid #ffedd5; padding: 12px; border-radius: 12px; display: none;">
                    <div style="display: flex; align-items: flex-start; gap: 12px; text-align: left;">
                        <i class="fas fa-users-slash" style="color: #f97316; margin-top: 3px;"></i>
                        <div>
                            <div style="font-weight: 800; color: #9a3412; font-size: 0.85rem;">Potentially Affected Accounts</div>
                            <div style="color: #c2410c; font-size: 0.75rem;">
                                There are <strong id="affectedUserCount"></strong> user account(s) currently assigned to this office. 
                                Their records will remain, but they will no longer belong to a valid office.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background: #fef2f2;">
                <button type="button" class="btn" style="background: white; border: 1px solid #e2e8f0; color: #64748b;" onclick="closeDeleteModal()">Cancel</button>
                <button type="submit" class="btn" style="background: #ef4444; color: white;">Delete Permanently</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function switchTab(evt, tabName) {
        const panes = document.getElementsByClassName('tab-pane');
        for (let pane of panes) pane.classList.remove('active');
        
        const triggers = document.getElementsByClassName('tab-trigger');
        for (let trigger of triggers) trigger.classList.remove('active');
        
        document.getElementById(tabName).classList.add('active');
        evt.currentTarget.classList.add('active');

        // Store active tab in session-like storage
        localStorage.setItem('activeManagementTab', tabName);
    }

    // Restore active tab on load
    document.addEventListener('DOMContentLoaded', () => {
        const activeTab = localStorage.getItem('activeManagementTab');
        if (activeTab && document.getElementById(activeTab)) {
            const btn = Array.from(document.querySelectorAll('.tab-trigger')).find(b => b.innerText.toLowerCase().includes(activeTab.slice(0, 5)));
            if (btn) btn.click();
        }
    });

    const modal = document.getElementById('officeModal');
    const officeForm = document.getElementById('officeForm');
    
    function openAddOfficeModal(category = '') {
        document.getElementById('modalTitle').innerText = 'Add New Office Unit';
        document.getElementById('formMethod').value = 'POST';
        officeForm.action = "{{ route('admin.offices.store') }}";
        document.getElementById('officeCategory').value = category;
        document.getElementById('officeName').value = '';
        modal.classList.add('active');
    }

    function openEditOfficeModal(id, name, category) {
        document.getElementById('modalTitle').innerText = 'Edit Office Unit';
        document.getElementById('formMethod').value = 'PUT';
        // Use a placeholder to avoid UrlGenerationException
        let url = "{{ route('admin.offices.update', ':id') }}";
        officeForm.action = url.replace(':id', id);
        document.getElementById('officeCategory').value = category;
        document.getElementById('officeName').value = name;
        modal.classList.add('active');
    }

    function closeModal() {
        modal.classList.remove('active');
    }

    const deleteModal = document.getElementById('deleteOfficeModal');
    const deleteForm = document.getElementById('deleteOfficeForm');

    function openDeleteOfficeModal(id, name, userCount) {
        document.getElementById('deleteOfficeName').innerText = name;
        deleteForm.action = "{{ route('admin.offices.delete', ':id') }}".replace(':id', id);
        
        const warningBox = document.getElementById('userWarningBox');
        if (userCount > 0) {
            warningBox.style.display = 'block';
            document.getElementById('affectedUserCount').innerText = userCount;
        } else {
            warningBox.style.display = 'none';
        }
        
        deleteModal.classList.add('active');
    }

    function closeDeleteModal() {
        deleteModal.classList.remove('active');
    }

    // Close on outside click
    window.onclick = function(event) {
        if (event.target == modal) closeModal();
        if (event.target == deleteModal) closeDeleteModal();
    }
</script>
@endpush
@endsection
