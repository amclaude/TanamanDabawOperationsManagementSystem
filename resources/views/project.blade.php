@extends('layouts.app')

@section('title', 'Projects | Tanaman')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    .swal2-container { z-index: 20000 !important; }
    
    .btn-primary, .btn-danger { 
        padding: 8px 16px; display: inline-flex; align-items: center; justify-content: center; 
        gap: 6px; border: none; cursor: pointer; color: white; border-radius: 6px; font-size: 0.9em; font-weight: 500;
        transition: background-color 0.2s;
    }
    .btn-primary { background-color: #319B72; }
    .btn-primary:hover { background-color: #267a59; }
    .btn-danger { background-color: #ef4444; }
    .btn-danger:hover { background-color: #dc2626; }
    
    .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.8em; font-weight: 600; }
    .status-badge.active { background-color: #dcfce7; color: #166534; }
    .status-badge.completed { background-color: #059669; color: #fff; }
    
    tr[data-href] { cursor: pointer; transition: background-color 0.1s; }
    tr[data-href]:hover { background-color: #f8fafc; }

    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5); display: none; 
        justify-content: center; align-items: center; z-index: 1000;
    }

    .modal-box {
        width: 100%;
        max-width: 500px;
        background: #fff;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        max-height: 90vh;
    }

    .modal-header {
        padding: 16px 20px 0;
        background: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-header h3 { margin: 0; font-size: 1.1rem; color: #1e293b; font-weight: 700; }
    .close-modal-btn { font-size: 1.5rem; color: #94a3b8; cursor: pointer; line-height: 1; }
    .close-modal-btn:hover { color: #475569; }

    .modal-tabs {
        display: flex;
        border-bottom: 1px solid #e2e8f0;
        padding: 0 20px;
        margin-top: 12px;
    }

    .tab-btn {
        padding: 10px 16px;
        cursor: pointer;
        font-weight: 600;
        color: #64748b;
        border-bottom: 2px solid transparent;
        font-size: 0.85rem;
        transition: all 0.2s;
    }

    .tab-btn:hover { color: #319B72; }
    .tab-btn.active { color: #319B72; border-bottom-color: #319B72; }

    .modal-content-wrapper {
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        flex: 1;
    }
    /* #tab-general { overflow-y: auto; height: 52vh;} */

    .modal-form-content {
        padding: 16px 20px;
        flex: 1;
    }

    .tab-pane { display: none; animation: fadeIn 0.2s ease-in-out; }
    .tab-pane.active { display: block; }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

    .modal-actions {
        padding: 16px 20px;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .input-group { margin-bottom: 14px; }
    .input-group:last-child { margin-bottom: 0; }
    .input-group label {
        display: block; margin-bottom: 5px; font-weight: 500; color: #475569; font-size: 0.85em;
    }
    .input-group input, .input-group textarea, .input-group select {
        width: 100%; padding: 9px 11px; 
        border: 1px solid #cbd5e1; border-radius: 6px; 
        font-size: 0.9em; background-color: #fff;
        transition: border-color 0.2s;
    }
    .input-group input:focus, .input-group textarea:focus, .input-group select:focus {
        border-color: #319B72; outline: none;
    }
    .input-group input[readonly] { background-color: #f1f5f9; color: #64748b; cursor: default; }
    
    /* Tom Select locked state */
    .ts-wrapper.locked {
        pointer-events: none;
        opacity: 0.7;
    }
    
    .ts-wrapper.locked .ts-control {
        background-color: #f1f5f9 !important;
        color: #64748b !important;
        cursor: default !important;
        border-color: #cbd5e1 !important;
    }

    .compact-row {
        display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
    }

    .quote-select-container {
        background: #f0fdf4; border: 1px solid #bbf7d0; 
        padding: 12px; border-radius: 8px; margin-bottom: 16px;
    }
    .quote-label { color: #166534; font-weight: 600; font-size: 0.85em; margin-bottom: 8px; display: block; }

    .btn-cancel {
        background: white; 
        border: 1px solid #cbd5e1; 
        color: #475569; 
        padding: 8px 16px; 
        border-radius: 6px; 
        cursor: pointer;
        font-size: 0.9em;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-cancel:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }

    .input-invalid {
        border: 2px solid #dc3545 !important;
    }

    .ts-wrapper.input-invalid {
        border: 0 !important;
    }

    .ts-wrapper.input-invalid .ts-control {
        border: 2px solid #dc3545 !important;
    }

    .field-error {
        color: #dc3545;
        font-size: 0.78rem;
        margin-top: 6px;
        display: block;
        min-height: 18px;
    }

    .btn-loading {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .shake {
        animation: shakeX 0.25s ease-in-out;
    }

    @keyframes shakeX {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h2>Projects</h2>
        <p>Manage all projects and their details</p>
    </div>

    <div style="display: flex; gap: 10px; align-items: center;">
        <form action="{{ route('projects') }}" method="GET" style="position: relative; margin: 0;">
            <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Search projects..." style="padding: 10px 10px 10px 35px; border: 1px solid #ddd; border-radius: 6px; outline: none; width: 250px;">
            <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
        </form>

        <button class="btn-primary" id="addProjectBtn">
            <i class="fas fa-plus"></i> Add Project
        </button>
    </div>
</div>

<div class="table-container loading-scope table-loading-scope" data-loading-scope="network">
    <div class="skeleton-overlay" aria-hidden="true">
        <div class="skeleton table-skeleton-row"></div>
        <div class="skeleton table-skeleton-row"></div>
        <div class="skeleton table-skeleton-row"></div>
        <div class="skeleton table-skeleton-row"></div>
        <div class="skeleton table-skeleton-row"></div>
    </div>
    <div class="loading-content">
    <table class="data-table">
        <thead>
            <tr>
                <th>Project Name</th>
                <th>Client</th>
                <th>Head Landscaper</th>
                <th>Crew</th>
                <th>Status</th>
                <th>Deadline</th>
                <th>Budget</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="projectsTableBody">
            @forelse($projects as $project)
            <tr data-href="{{ route('projects.panel', $project->id) }}">
                <td>{{ $project->project_name }}</td>
                <td>{{ $project->client->name ?? 'N/A' }}</td>
                <td>{{ $project->headLandscaper->name ?? 'Unassigned' }}</td>
                <td>
                    <span class="badge" style="background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 6px; font-size: 0.8em;">
                        {{ $project->fieldCrew->count() }} Members
                    </span>
                </td>
                <td>
                    <span class="status-badge {{ $project->is_active ? 'active' : 'completed' }}">
                        {{ $project->is_active ? 'Active' : 'Done' }}
                    </span>
                </td>
                <td>{{ $project->project_end_date }}</td>
                <td>₱{{ number_format($project->project_budget, 2) }}</td>
                <td>
                    <button class="btn-primary edit-btn"
                        data-id="{{ $project->id }}"
                        data-name="{{ $project->project_name }}"
                        data-client-id="{{ $project->client_id }}"
                        data-quote-id="{{ $project->quote_id }}"
                        data-start-date="{{ \Illuminate\Support\Carbon::parse($project->project_start_date)->format('Y-m-d') }}"
                        data-deadline="{{ $project->project_end_date }}"
                        data-budget="{{ $project->project_budget }}"
                        data-location="{{ $project->project_location }}"
                        data-description="{{ $project->project_description }}"
                        data-head-id="{{ $project->head_landscaper_id }}"
                        data-crew="{{ $project->fieldCrew->pluck('id') }}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-danger delete-btn" data-id="{{ $project->id }}"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align: center; padding: 40px; color: #64748b;">No projects found.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

@include('partials.pagination', ['data' => $projects->appends(request()->query())])

<div class="modal-overlay" id="addProjectModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modalTitle">Add New Project</h3>
            <span class="close-modal-btn" id="closeProjectModal">&times;</span>
        </div>

        <div class="modal-tabs">
            <div class="tab-btn active" onclick="switchTab('general')">General Info</div>
            <div class="tab-btn" onclick="switchTab('team')">Team Assignment</div>
        </div>
        
        <form id="projectForm" novalidate style="display: flex; flex-direction: column; flex: 1; overflow: hidden;">
            <input type="hidden" id="project_id">

            <div class="modal-content-wrapper">
                <div class="modal-form-content">
                    <div id="tab-general" class="tab-pane active">
                        <div id="quoteContainer" class="quote-select-container">
                            <label class="quote-label">Link to Quote (Optional)</label>
                            <select id="quote_id" placeholder="Search for a quote..." data-error-target="quoteError">
                                <option value="">-- No Quote (Manual Entry) --</option>
                                @foreach($pendingQuotes as $quote)
                                <option value="{{ $quote->id }}"
                                    data-client="{{ $quote->client_id }}"
                                    data-budget="{{ $quote->total_amount }}"
                                    data-subject="{{ $quote->subject }}">
                                    #{{ $quote->id }} — {{ $quote->subject }} (₱{{ number_format($quote->total_amount) }})
                                </option>
                                @endforeach
                            </select>
                            <span id="quoteError" class="field-error"></span>
                        </div>

                        <div class="input-group">
                            <label for="project_name">Project Name</label>
                            <input type="text" id="project_name" placeholder="e.g. Backyard Renovation" maxlength="255" data-error-target="projectNameError" required>
                            <span id="projectNameError" class="field-error"></span>
                        </div>

                        <div class="compact-row">
                            <div class="input-group">
                                <label for="client_id">Client</label>
                                <select id="client_id" data-error-target="clientError" required>
                                    <option value="">Select Client...</option>
                                    @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                                <span id="clientError" class="field-error"></span>
                            </div>
                            <div class="input-group">
                                <label for="project_location">Location</label>
                                <input type="text" id="project_location" placeholder="Address" maxlength="255" data-error-target="locationError" required>
                                <span id="locationError" class="field-error"></span>
                            </div>
                        </div>

                        <div class="compact-row">
                            <div class="input-group">
                                <label for="project_start_date">Start Date</label>
                                <input type="date" id="project_start_date" data-error-target="startDateError" required>
                                <span id="startDateError" class="field-error"></span>
                            </div>
                            <div class="input-group">
                                <label for="project_end_date">End Date</label>
                                <input type="date" id="project_end_date" data-error-target="endDateError" required>
                                <span id="endDateError" class="field-error"></span>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="project_budget">Budget (₱)</label>
                            <input type="text" id="project_budget" inputmode="decimal" placeholder="0.00" data-error-target="budgetError" required>
                            <span id="budgetError" class="field-error"></span>
                        </div>

                        <div class="input-group">
                            <label for="project_description">Description</label>
                            <textarea id="project_description" rows="2" placeholder="Brief scope of work..." data-error-target="descriptionError"></textarea>
                            <span id="descriptionError" class="field-error"></span>
                        </div>
                    </div>

                    <div id="tab-team" class="tab-pane">
                        <div class="input-group">
                            <label for="head_landscaper_id">Head Landscaper</label>
                            <select id="head_landscaper_id" data-error-target="headError">
                                <option value="">Select Head...</option>
                                @foreach($workers->where('role', 'Head Landscaper') as $worker)
                                <option value="{{ $worker->id }}">{{ $worker->name }}</option>
                                @endforeach
                            </select>
                            <span id="headError" class="field-error"></span>
                        </div>

                        <div class="input-group">
                            <label for="crew_ids">Field Crew</label>
                            <select id="crew_ids" multiple placeholder="Select Crew..." data-error-target="crewError">
                                @foreach($workers->where('role', 'Field Crew') as $worker)
                                <option value="{{ $worker->id }}">{{ $worker->name }}</option>
                                @endforeach
                            </select>
                            <span id="crewError" class="field-error"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-primary" id="saveProjectBtn">
                    <span class="btn-label">Save Project</span>
                    <span class="btn-spinner" style="display:none;">Saving...</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    let clientSelect, quoteSelect, headSelect, crewSelect;

    function switchTab(tabName) {
        document.querySelectorAll('.tab-pane').forEach((el) => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach((el) => el.classList.remove('active'));

        document.getElementById('tab-' + tabName).classList.add('active');
        const tabBtn = document.querySelector(`.tab-btn[onclick="switchTab('${tabName}')"]`);
        if (tabBtn) {
            tabBtn.classList.add('active');
        }

        document.querySelector('.modal-content-wrapper').style.overflow = tabName === 'team' ? 'visible' : 'auto';
    }

    window.switchTab = switchTab;

    function lockClientSelect() {
        // Lock the client select dropdown
        clientSelect.lock();
        clientSelect.wrapper.classList.add('locked');
    }

    function unlockClientSelect() {
        // Unlock the client select dropdown
        clientSelect.unlock();
        clientSelect.wrapper.classList.remove('locked');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('addProjectModal');
        const form = document.getElementById('projectForm');
        const saveProjectBtn = document.getElementById('saveProjectBtn');
        const saveBtnLabel = saveProjectBtn.querySelector('.btn-label');
        const saveBtnSpinner = saveProjectBtn.querySelector('.btn-spinner');

        const projectIdField = document.getElementById('project_id');
        const projectNameField = document.getElementById('project_name');
        const clientField = document.getElementById('client_id');
        const startDateField = document.getElementById('project_start_date');
        const endDateField = document.getElementById('project_end_date');
        const budgetField = document.getElementById('project_budget');
        const locationField = document.getElementById('project_location');
        const descriptionField = document.getElementById('project_description');
        const quoteField = document.getElementById('quote_id');
        const headField = document.getElementById('head_landscaper_id');
        const crewField = document.getElementById('crew_ids');

        const todayIso = new Date().toISOString().split('T')[0];

        const setLoadingState = (isLoading) => {
            saveProjectBtn.disabled = isLoading;
            saveProjectBtn.classList.toggle('btn-loading', isLoading);
            saveBtnLabel.style.display = isLoading ? 'none' : 'inline';
            saveBtnSpinner.style.display = isLoading ? 'inline' : 'none';
        };

        const getRenderedControl = (field) => {
            if (field.tomselect) {
                return field.tomselect.wrapper;
            }

            return field;
        };

        const setFieldError = (field, message) => {
            const control = getRenderedControl(field);
            control.classList.add('input-invalid');

            const errorElement = document.getElementById(field.dataset.errorTarget);
            if (errorElement) {
                errorElement.textContent = message;
            }
        };

        const clearFieldError = (field) => {
            const control = getRenderedControl(field);
            control.classList.remove('input-invalid');

            const errorElement = document.getElementById(field.dataset.errorTarget);
            if (errorElement) {
                errorElement.textContent = '';
            }
        };

        const clearAllFieldErrors = () => {
            [projectNameField, clientField, startDateField, endDateField, budgetField, locationField, descriptionField, quoteField, headField, crewField]
                .forEach(clearFieldError);
        };

        const focusFirstError = (field) => {
            if (!field) {
                return;
            }

            if (field.closest('#tab-team')) {
                switchTab('team');
            } else {
                switchTab('general');
            }

            const control = getRenderedControl(field);
            control.scrollIntoView({ behavior: 'smooth', block: 'center' });
            control.classList.add('shake');
            setTimeout(() => control.classList.remove('shake'), 300);
            if (!field.tomselect) {
                field.focus();
            }
        };

        const validateForm = () => {
            clearAllFieldErrors();

            const projectName = projectNameField.value.trim();
            const clientId = clientField.value.trim();
            const endDate = endDateField.value.trim();
            const budget = budgetField.value.trim();
            const location = locationField.value.trim();

            let firstInvalidField = null;

            const flag = (field, message) => {
                setFieldError(field, message);
                if (!firstInvalidField) {
                    firstInvalidField = field;
                }
            };

            if (!projectName) {
                flag(projectNameField, 'Project name is required.');
            } else if (projectName.length > 255) {
                flag(projectNameField, 'Project name must not exceed 255 characters.');
            }

            if (!clientId) {
                flag(clientField, 'Client is required.');
            } else if (!/^\d+$/.test(clientId)) {
                flag(clientField, 'Client value must be numeric.');
            }

            if (!endDate) {
                flag(endDateField, 'End date is required.');
            } else if (Number.isNaN(new Date(endDate).getTime())) {
                flag(endDateField, 'End date must be a valid date.');
            } else if (endDate <= todayIso) {
                flag(endDateField, 'End date must be after today.');
            }

            if (!budget) {
                flag(budgetField, 'Budget is required.');
            } else if (!/^\d+(\.\d{1,2})?$/.test(budget)) {
                flag(budgetField, 'Budget must be a valid amount (e.g. 1000 or 1000.50).');
            } else if (Number.parseFloat(budget) < 0) {
                flag(budgetField, 'Budget must be at least 0.');
            }

            if (!location) {
                flag(locationField, 'Location is required.');
            } else if (location.length > 255) {
                flag(locationField, 'Location must not exceed 255 characters.');
            }

            return {
                isValid: !firstInvalidField,
                firstInvalidField,
            };
        };

        clientSelect = new TomSelect("#client_id", { placeholder: "Select Client" });
        headSelect = new TomSelect("#head_landscaper_id", { placeholder: "Select Head" });
        crewSelect = new TomSelect("#crew_ids", { plugins: ['remove_button'], placeholder: "Add Crew Members" });

        quoteSelect = new TomSelect("#quote_id", {
            placeholder: "Select a quote...",
            onChange: function(value) {
                const opt = document.querySelector(`#quote_id option[value="${value}"]`);
                if (value && opt) {
                    projectNameField.value = opt.dataset.subject;
                    budgetField.value = opt.dataset.budget;
                    clientSelect.setValue(opt.dataset.client);

                    projectNameField.readOnly = true;
                    budgetField.readOnly = true;
                    lockClientSelect();
                } else {
                    projectNameField.readOnly = false;
                    budgetField.readOnly = false;
                    unlockClientSelect();
                    clientSelect.clear();
                }

                clearFieldError(quoteField);
                clearFieldError(projectNameField);
                clearFieldError(budgetField);
                clearFieldError(clientField);
            }
        });

        [projectNameField, startDateField, endDateField, budgetField, locationField, descriptionField].forEach((field) => {
            field.addEventListener('input', () => clearFieldError(field));
        });

        [clientField, quoteField, headField, crewField].forEach((field) => {
            field.addEventListener('change', () => clearFieldError(field));
            if (field.tomselect) {
                field.tomselect.on('change', () => clearFieldError(field));
            }
        });

        document.getElementById('addProjectBtn').addEventListener('click', () => {
            form.reset();
            projectIdField.value = '';

            quoteSelect.clear();
            unlockClientSelect();
            clientSelect.clear();
            headSelect.clear();
            crewSelect.clear();
            clearAllFieldErrors();

            projectNameField.readOnly = false;
            budgetField.readOnly = false;

            startDateField.value = todayIso;
            endDateField.value = '';

            document.getElementById('quoteContainer').style.display = 'block';
            document.getElementById('modalTitle').innerText = "Add New Project";
            switchTab('general');
            setLoadingState(false);
            modal.style.display = 'flex';
        });

        document.querySelector('.data-table').addEventListener('click', (e) => {
        const editBtn = e.target.closest('.edit-btn');
        if (editBtn) {
            e.stopPropagation();
            const data = editBtn.dataset;

            projectIdField.value = data.id;
            projectNameField.value = data.name;
            startDateField.value = data.startDate || todayIso;
            endDateField.value = data.deadline;
            budgetField.value = data.budget;
            locationField.value = data.location;
            descriptionField.value = data.description;
            clearAllFieldErrors();
            
            // Set client and always lock it in edit mode
            clientSelect.setValue(data.clientId);
            lockClientSelect();
            
            headSelect.setValue(data.headId);
            
            if (data.crew) {
                crewSelect.setValue(JSON.parse(data.crew));
            }

            document.getElementById('quoteContainer').style.display = 'none';
            document.getElementById('modalTitle').innerText = "Edit Project";

            const hasQuote = data.quoteId && data.quoteId !== "" && data.quoteId !== "null";

            if (hasQuote) {
                // If project has a quote, lock project name and budget
                projectNameField.readOnly = true;
                budgetField.readOnly = true;
            } else {
                // If no quote, allow editing project name and budget
                projectNameField.readOnly = false;
                budgetField.readOnly = false;
            }

            switchTab('general');
            setLoadingState(false);
            modal.style.display = 'flex';
        }

        const deleteBtn = e.target.closest('.delete-btn');
        if (deleteBtn) {
            e.stopPropagation();
            const id = deleteBtn.getAttribute('data-id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    await fetch(`/projects/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    window.location.reload();
                }
            });
        }
        
        const row = e.target.closest('tr[data-href]');
        if (row && !e.target.closest('button')) {
            window.location.href = row.dataset.href;
        }
        });

        form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const validation = validateForm();
        if (!validation.isValid) {
            focusFirstError(validation.firstInvalidField);
            return;
        }

        setLoadingState(true);

        const projectName = projectNameField.value.trim();
        const clientId = clientField.value.trim();
        const deadline = endDateField.value.trim();
        const budget = budgetField.value.trim();
        const location = locationField.value.trim();
        const id = projectIdField.value;
    
    // If editing, check if any changes were made
    if (id) {
        const editBtn = document.querySelector(`.edit-btn[data-id="${id}"]`);
        if (editBtn) {
            const originalData = {
                project_name: editBtn.dataset.name,
                client_id: editBtn.dataset.clientId,
                project_end_date: editBtn.dataset.deadline,
                project_budget: editBtn.dataset.budget,
                project_location: editBtn.dataset.location,
                project_description: editBtn.dataset.description || '',
                head_landscaper_id: editBtn.dataset.headId || '',
                crew_ids: editBtn.dataset.crew ? JSON.parse(editBtn.dataset.crew) : []
            };

            const currentCrew = crewSelect.getValue();
            const currentData = {
                project_name: projectName,
                client_id: clientId,
                project_end_date: deadline,
                project_budget: budget,
                project_location: location,
                project_description: descriptionField.value || '',
                head_landscaper_id: headField.value || '',
                crew_ids: Array.isArray(currentCrew) ? currentCrew : []
            };

            // Check if any field changed
            const hasChanges = 
                originalData.project_name !== currentData.project_name ||
                originalData.client_id !== currentData.client_id ||
                originalData.project_end_date !== currentData.project_end_date ||
                originalData.project_budget !== currentData.project_budget ||
                originalData.project_location !== currentData.project_location ||
                originalData.project_description !== currentData.project_description ||
                originalData.head_landscaper_id !== currentData.head_landscaper_id ||
                JSON.stringify(originalData.crew_ids.sort()) !== JSON.stringify(currentData.crew_ids.sort());

            if (!hasChanges) {
                Swal.fire({
                    title: 'Success!',
                    html: '<div class="swal2-html-container">No changes were made.</div>',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    modal.style.display = 'none';
                    window.location.reload();
                });
                setLoadingState(false);
                return;
            }
        }
    }
    
    const formData = {
        project_name: projectName,
        client_id: clientId,
        project_end_date: deadline,
        project_budget: budget,
        project_location: location,
        project_description: descriptionField.value,
        head_landscaper_id: headField.value,
        crew_ids: crewSelect.getValue(),
        quote_id: id ? null : quoteField.value
    };

    const url = id ? `/projects/${id}` : "{{ route('projects.create') }}";
    const method = id ? 'PUT' : 'POST';

    try {
        Swal.fire({
            title: 'Processing...',
            didOpen: () => Swal.showLoading()
        });

        const response = await fetch(url, {
            method: method,
            headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                'Accept': 'application/json' 
            },
            body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (response.ok) {
            modal.style.display = 'none';
            Swal.fire({
                title: 'Success!',
                text: result.message,
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => window.location.reload());
        } else {
            if (response.status === 422 && result.errors) {
                const errorMap = {
                    project_name: projectNameField,
                    project_budget: budgetField,
                    project_end_date: endDateField,
                    client_id: clientField,
                    project_location: locationField,
                    quote_id: quoteField,
                    head_landscaper_id: headField,
                    crew_ids: crewField,
                };

                let firstBackendErrorField = null;
                Object.keys(result.errors).forEach((key) => {
                    const field = errorMap[key];
                    if (field && result.errors[key]?.length) {
                        setFieldError(field, result.errors[key][0]);
                        if (!firstBackendErrorField) {
                            firstBackendErrorField = field;
                        }
                    }
                });

                focusFirstError(firstBackendErrorField);
            }

            let msg = result.message || 'Validation Failed';
            if (result.errors) msg = Object.values(result.errors).flat().join('\n');
            Swal.fire('Error', msg, 'error');
        }
    } catch (error) {
        Swal.fire('Error', 'System error occurred', 'error');
    } finally {
        setLoadingState(false);
    }
    });

        const closeModal = () => {
            modal.style.display = 'none';
            clearAllFieldErrors();
            setLoadingState(false);
        };
        document.querySelectorAll('.close-modal-btn, .btn-cancel').forEach((b) => {
            b.onclick = closeModal;
        });

        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('projectsTableBody');
        if (searchInput && tableBody) {
            searchInput.addEventListener('keyup', function() {
                const filter = searchInput.value.toLowerCase();
                const rows = tableBody.getElementsByTagName('tr');
                for (let i = 0; i < rows.length; i++) {
                    const textContent = rows[i].innerText.toLowerCase();
                    rows[i].style.display = textContent.includes(filter) ? "" : "none";
                }
            });
        }
    });
</script>
@endpush
