@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('styles')
{{-- External CSS for Dropdowns --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

<style>
    /* --- GENERAL MODAL STYLES --- */
    .swal2-container {
        z-index: 30000 !important;
    }

    .lock-bg {
        background-color: #f8fafc !important;
        cursor: not-allowed;
        color: #64748b;
    }

    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 20000;
        justify-content: center;
        align-items: flex-start;
        /* Align to top for scrolling */
        padding-top: 50px;
        overflow-y: auto;
    }

    .modal-box {
        background: white;
        width: 500px;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        margin-bottom: 50px;
        position: relative;
    }

    /* Close Button (Top Right X) */
    .close-modal-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 1.25rem;
        color: #64748b;
        cursor: pointer;
        line-height: 1;
    }

    .close-modal-btn:hover {
        color: #334155;
    }

    /* Header Title */
    .modal-title {
        margin: 0 0 20px 0;
        color: #064e3b;
        /* Dark Green */
        font-size: 1.25rem;
        font-weight: 700;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 15px;
    }

    /* Form Elements */
    .input-group {
        margin-bottom: 15px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .input-group label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #475569;
    }

    .input-group input,
    .input-group select,
    .tom-select .ts-control {
        padding: 10px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        outline: none;
        font-size: 0.95rem;
        color: #334155;
        width: 100%;
    }

    .input-group input:focus {
        border-color: #319B72;
    }

    /* --- SPECIFIC: DASHED QUOTE BOX --- */
    .quote-link-box {
        border: 1px dashed #94a3b8;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .quote-link-label {
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
        display: block;
        font-size: 0.9rem;
    }

    .quote-link-help {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 5px;
        display: block;
    }

    /* --- SPECIFIC: LINE ITEMS (Quote Style) --- */
    .line-items-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .line-items-header h4 {
        margin: 0;
        font-size: 1rem;
        color: #334155;
        font-weight: 700;
    }

    .add-item-link {
        color: #10b981;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.9rem;
        text-decoration: none;
    }

    .add-item-link:hover {
        text-decoration: underline;
    }

    .item-row {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
        align-items: center;
    }

    .item-row input {
        padding: 8px 10px;
    }

    .delete-row-btn {
        color: #ef4444;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 1.1rem;
        padding: 5px;
        display: flex;
        align-items: center;
    }

    .delete-row-btn:hover {
        color: #b91c1c;
    }

    /* Total Amount Input Styling */
    .total-input-group input {
        background-color: #f8fafc;
        font-weight: 700;
        color: #334155;
    }

    /* Actions Footer */
    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 25px;
    }

    .btn-cancel {
        background: white;
        border: 1px solid #cbd5e1;
        color: #475569;
        padding: 10px 18px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
    }

    .btn-save {
        background: #319B72;
        border: none;
        color: white;
        padding: 10px 18px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
    }

    .btn-save:hover {
        background: #059669;
    }
</style>
@endpush

@section('content')

<header class="content-header">
    <h2>Dashboard</h2>
    <p>Welcome back, here's what's happening today.</p>
</header>

<div class="stats-grid">
    {{-- ... Stats Cards ... --}}
    <div class="stat-card">
        <div class="stat-text">
            <h3>Total Clients</h3>
            <p class="number">{{ $totalClients }}</p><a href="{{ route('clients') }}" class="details-link">View details</a>
        </div>
        <div class="stat-icon icon-bg-blue"><i class="fas fa-user-friends"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-text">
            <h3>Active Projects</h3>
            <p class="number">{{ $totalActiveProjects }}</p><a href="{{ route('projects') }}" class="details-link">View details</a>
        </div>
        <div class="stat-icon icon-bg-green"><i class="fas fa-briefcase"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-text">
            <h3>Employees</h3>
            <p class="number">{{ $totalEmployees }}</p><a href="{{ route('employees') }}" class="details-link">View details</a>
        </div>
        <div class="stat-icon icon-bg-purple"><i class="fas fa-user-check"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-text">
            <h3>Quotes Accepted</h3>
            <p class="number">{{ $totalAcceptedQuotes }}</p><a href="{{ route('quotes') }}" class="details-link">View details</a>
        </div>
        <div class="stat-icon icon-bg-orange"><i class="fas fa-file-alt"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-text">
            <h3>Invoices Sent</h3>
            <p class="number">{{ $totalSentInvoices }}</p><a href="{{ route('invoices') }}" class="details-link">View details</a>
        </div>
        <div class="stat-icon icon-bg-teal"><i class="fas fa-file-invoice-dollar"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-text">
            <h3>Inventory Items</h3>
            <p class="number">{{ $totalInventoryItems }}</p><a href="{{ route('inventory') }}" class="details-link">View details</a>
        </div>
        <div class="stat-icon icon-bg-red"><i class="fas fa-boxes"></i></div>
    </div>
</div>

<h3 class="section-title">Quick Actions</h3>
<div class="quick-actions-grid">
    <button class="action-card" id="addClientBtn">
        <div class="action-icon icon-bg-blue"><i class="fas fa-user-plus"></i></div>
        <div class="action-text"><span class="action-title">Add Client</span><span class="action-desc">Register new customer</span></div>
    </button>
    <button class="action-card" id="addProjectBtn">
        <div class="action-icon icon-bg-green"><i class="fas fa-briefcase"></i></div>
        <div class="action-text"><span class="action-title">New Project</span><span class="action-desc">Create & track job</span></div>
    </button>
    <button class="action-card" id="addEmployeeBtn">
        <div class="action-icon icon-bg-purple"><i class="fas fa-user-check"></i></div>
        <div class="action-text"><span class="action-title">Add Employee</span><span class="action-desc">Onboard team member</span></div>
    </button>
    <button class="action-card" id="createQuoteBtn">
        <div class="action-icon icon-bg-orange"><i class="fas fa-file-alt"></i></div>
        <div class="action-text"><span class="action-title">Create Quote</span><span class="action-desc">Estimate for client</span></div>
    </button>
    <button class="action-card" id="createInvoiceBtn">
        <div class="action-icon icon-bg-teal"><i class="fas fa-file-invoice-dollar"></i></div>
        <div class="action-text"><span class="action-title">New Invoice</span><span class="action-desc">Bill completed work</span></div>
    </button>
    <button class="action-card" id="addItemBtn">
        <div class="action-icon icon-bg-red"><i class="fas fa-boxes"></i></div>
        <div class="action-text"><span class="action-title">Add Item</span><span class="action-desc">Update stock levels</span></div>
    </button>
</div>

{{-- -------------------------- MODALS SECTION -------------------------- --}}
<div class="modal-overlay" id="projectModal">
    <div class="modal-box">
        <span class="close-modal-btn">&times;</span>
        <h3 id="modalTitle" class="modal-title">Add New Project</h3>

        <form id="projectForm">
            <input type="hidden" id="project_id" name="project_id">

            {{-- Dashed Quote Link Box --}}
            <div class="quote-link-box" id="quoteContainer">
                <label class="quote-link-label">Link to Quote (Optional)</label>
                <select id="p_quote" placeholder="Search for a quote...">
                    <option value="">Search for a quote...</option>
                    @foreach($pendingQuotes as $quote)
                    <option value="{{ $quote->id }}"
                        data-client="{{ $quote->client_id }}"
                        data-budget="{{ $quote->total_amount }}"
                        data-subject="{{ $quote->subject }}">
                        Quote #{{ str_pad($quote->id, 4, '0', STR_PAD_LEFT) }} — {{ $quote->subject }}
                    </option>
                    @endforeach
                </select>
                <span class="quote-link-help">Selecting a quote auto-fills Client & Budget</span>
            </div>

            <div class="input-group">
                <label>Project Name</label>
                <input type="text" id="p_name" placeholder="e.g. Garden Redesign" required>
            </div>

            <div class="input-group">
                <label>Client</label>
                <select id="p_client" placeholder="Select a Client...">
                    <option value="">Select a Client...</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="input-group">
                <label>Deadline</label>
                <input type="date" id="p_deadline" required>
            </div>

            <div class="input-group">
                <label>Budget (₱)</label>
                <input type="number" id="p_budget" placeholder="e.g. 5000" step="0.01" min="0" required>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save">Save Project</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="addQuoteModal" style="display: none;">
    <div class="modal-box large"> {{-- Added 'large' class if you have wide modal styles --}}
        <div class="modal-header">
            <h3 id="modalTitle">Create New Quote</h3>
            <span class="close-modal-btn">&times;</span>
        </div>

        <form class="modal-form" id="createQuoteForm">
            <div class="input-group">
                <label>Client</label>
                <select id="q_clientId" required placeholder="Select a Client...">
                    <option value="">Select a Client...</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="input-group">
                <label>Subject</label>
                <input type="text" id="quoteSubject" placeholder="e.g. Garden Maintenance" required>
            </div>

            <div style="display: flex; gap: 10px;">
                <div class="input-group" style="flex:1;">
                    <label>Quote Date</label>
                    <input type="date" id="quoteDate" required>
                </div>
                <div class="input-group" style="flex:1;">
                    <label>Valid Until</label>
                    <input type="date" id="validUntil" required>
                </div>
            </div>

            <div class="form-group" style="margin-top: 15px;">
                <div class="line-items-header" style="display:flex; justify-content:space-between; margin-bottom: 5px;">
                    <label style="font-weight: 600; color:#475569;">Line Items</label>
                    <span class="add-item-link" id="addQuoteItemBtn" style="cursor:pointer; color:#319B72;">+ Add item</span>
                </div>
                <div id="quoteItemsContainer">
                    {{-- Dynamic items will appear here --}}
                </div>
            </div>

            <div class="input-group" style="margin-top: 15px;">
                <label>Total Amount (₱)</label>
                <input type="text" id="displayQuoteTotal" placeholder="0.00" readonly style="font-weight: bold; background-color: #f8fafc;">
            </div>

            <div class="modal-actions" style="margin-top: 25px; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save" id="saveQuoteBtn">Create Quote</button>
            </div>
        </form>
    </div>
</div>


<div class="modal-overlay" id="clientModal">
    <div class="modal-box">
        <span class="close-modal-btn">&times;</span>
        <h3 class="modal-title">Add New Client</h3>
        <form id="clientForm">
            <div class="input-group"><label>Client Name</label><input type="text" id="c_name" required></div>
            <div class="input-group"><label>Email Address</label><input type="email" id="c_email" required></div>
            <div class="input-group"><label>Phone Number</label><input type="tel" id="c_phone" placeholder="09xxxxxxxxx" required></div>
            <div class="input-group"><label>Address</label><input type="text" id="c_address" required></div>
            <div class="modal-actions"><button type="button" class="btn-cancel">Cancel</button><button type="submit" class="btn-save">Save Client</button></div>
        </form>
    </div>
</div>


<div class="modal-overlay" id="employeeModal">
    <div class="modal-box">
        <span class="close-modal-btn">&times;</span>
        <h3 class="modal-title">Add New Employee</h3>
        <form id="employeeForm">
            <div class="input-group"><label>Name</label><input type="text" id="e_name" required></div>
            <div class="input-group"><label>Email</label><input type="email" id="e_email" required></div>
            <div class="modal-actions"><button type="button" class="btn-cancel">Cancel</button><button type="submit" class="btn-save">Save Employee</button></div>
        </form>
    </div>
</div>


<div class="modal-overlay" id="invoiceModal">
    <div class="modal-box">
        <span class="close-modal-btn">&times;</span>
        <h3 class="modal-title">Create New Invoice</h3>
        <form id="invoiceForm">
            <div class="input-group">
                <label>Select Project</label>
                <select id="inv_project" required>
                    <option value="">-- Choose a Project --</option>@foreach($projects as $proj)<option value="{{ $proj->id }}">{{ $proj->project_name }}</option>@endforeach
                </select>
                <small style="color:#64748b; margin-top:5px; display:block;">Selecting a project will auto-fill the client and items.</small>
            </div>
            <div class="input-group" style="margin-top:15px;">
                <label>Client</label>
                <input type="text" id="inv_clientDisplay" readonly class="lock-bg" placeholder="Auto-filled from Project">
                <input type="hidden" id="inv_clientId">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="input-group"><label>Issue Date</label><input type="date" id="inv_issue" required></div>
                <div class="input-group"><label>Due Date</label><input type="date" id="inv_due" required></div>
            </div>
            <div class="line-items-header" style="margin-top:20px;">
                <h4>Billable Items</h4>
            </div>
            <div id="invItemsContainer" style="margin-bottom:15px;">
                <div style="text-align:center; color:#94a3b8; padding:10px; font-style:italic;">Select a project to load items...</div>
            </div>
            <div class="input-group total-input-group">
                <label>Total Amount</label>
                <input type="text" id="inv_displayTotal" value="₱ 0.00" readonly>
            </div>
            <div class="modal-actions"><button type="button" class="btn-cancel">Cancel</button><button type="submit" class="btn-save" id="btnSaveInvoice">Create Invoice</button></div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="inventoryModal">
    <div class="modal-box">
        <span class="close-modal-btn">&times;</span>
        <h3 class="modal-title">Add New Item</h3>
        <form id="inventoryForm">
            <div class="input-group">
                <label>Item Name</label>
                <input type="text" id="i_name" placeholder="e.g. Fertilizer" required>
            </div>
            <div style="display: flex; gap: 15px;">
                <div class="input-group" style="flex:1;">
                    <label>SKU</label>
                    <input type="text" id="i_sku" placeholder="e.g. FUR-001" required>
                </div>
                <div class="input-group" style="flex:1;">
                    <label>Category</label>
                    <select id="i_category">
                        @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="display: flex; gap: 15px;">
                <div class="input-group" style="flex:1;">
                    <label>Price (₱)</label>
                    <input type="number" id="i_price" step="0.01" placeholder="0.00" required>
                </div>
                <div class="input-group" style="flex:1;">
                    <label>Initial Stock</label>
                    <input type="number" id="i_stock" value="0" placeholder="0">
                </div>
            </div>
            <div class="modal-actions"><button type="button" class="btn-cancel">Cancel</button><button type="submit" class="btn-save">Save Item</button></div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const initTS = (id, placeholder, extraOptions = {}) => {
            const el = document.getElementById(id);
            return el ? new TomSelect(el, {
                create: false,
                placeholder: placeholder,
                sortField: { field: "text", direction: "asc" },
                ...extraOptions
            }) : null;
        };

        const tsProjClient = initTS('p_client', 'Select a Client...');
        const tsInvCategory = initTS('i_category', 'Select Category...');
        const quoteClientSelect = initTS('q_clientId', 'Select a Client...');

        const tsInvProject = initTS('inv_project', '-- Choose a Project --', {
            onChange: async function(val) {
                const container = document.getElementById('invItemsContainer');
                const clientDisplay = document.getElementById('inv_clientDisplay');
                const clientIdInput = document.getElementById('inv_clientId');

                if (!val) {
                    container.innerHTML = '<div style="text-align:center; color:#94a3b8; padding:10px; font-style:italic;">Select a project to load items...</div>';
                    if (clientDisplay) clientDisplay.value = '';
                    calculateInvoiceTotal();
                    return;
                }

                container.innerHTML = '<div style="text-align:center; padding:20px;"><i class="fas fa-spinner fa-spin"></i> Loading items...</div>';

                try {
                    const res = await fetch(`/projects/${val}/invoice-data`);
                    const data = await res.json();

                    if (clientDisplay) clientDisplay.value = data.client_name || '';
                    if (clientIdInput) clientIdInput.value = data.client_id || '';

                    container.innerHTML = '';
                    if (data.items && data.items.length > 0) {
                        data.items.forEach(item => {
                            createInvoiceRow(item.description, item.quantity, item.price);
                        });
                    } else {
                        createInvoiceRow('Consultation Service', 1, 0);
                    }
                    calculateInvoiceTotal();
                } catch (e) {
                    container.innerHTML = '<div style="color:#ef4444; padding:10px;">Error loading project data.</div>';
                }
            }
        });

        const tsQuoteLink = initTS('p_quote', 'Search for a quote...', {
            allowEmptyOption: true,
            onChange: function(value) {
                const nameInput = document.getElementById('p_name');
                const budgetInput = document.getElementById('p_budget');
                const originalOption = document.querySelector(`#p_quote option[value="${value}"]`);

                if (value && originalOption) {
                    const clientID = originalOption.getAttribute('data-client');
                    const budgetAmount = originalOption.getAttribute('data-budget');
                    const subject = originalOption.getAttribute('data-subject');

                    if (nameInput) {
                        nameInput.value = subject;
                        nameInput.readOnly = true;
                        nameInput.style.backgroundColor = "#f1f5f9";
                    }
                    if (budgetInput) {
                        budgetInput.value = budgetAmount;
                        budgetInput.readOnly = true;
                        budgetInput.style.backgroundColor = "#f1f5f9";
                    }
                    if (tsProjClient) {
                        tsProjClient.setValue(clientID);
                        tsProjClient.lock();
                    }
                } else {
                    if (nameInput) {
                        nameInput.readOnly = false;
                        nameInput.style.backgroundColor = "#fff";
                    }
                    if (budgetInput) {
                        budgetInput.readOnly = false;
                        budgetInput.style.backgroundColor = "#fff";
                    }
                    if (tsProjClient) {
                        tsProjClient.unlock();
                        tsProjClient.clear();
                    }
                }
            }
        });

        const modalMap = {
            'addClientBtn': 'clientModal',
            'addProjectBtn': 'projectModal',
            'addEmployeeBtn': 'employeeModal',
            'createQuoteBtn': 'addQuoteModal',
            'createInvoiceBtn': 'invoiceModal',
            'addItemBtn': 'inventoryModal'
        };

        const closeModal = (modal) => {
            modal.style.display = 'none';
            const form = modal.querySelector('form');
            if (form) form.reset();

            if (modal.id === 'invoiceModal') {
                document.getElementById('invItemsContainer').innerHTML = '<div style="text-align:center; color:#94a3b8; padding:10px; font-style:italic;">Select a project to load items...</div>';
                if (tsInvProject) tsInvProject.clear();
                document.getElementById('inv_displayTotal').value = '₱ 0.00';
            }
        };

        function calculateInvoiceTotal() {
            let total = 0;
            document.querySelectorAll('#invItemsContainer .item-row').forEach(row => {
                const q = parseFloat(row.querySelector('.i-qty').value) || 0;
                const p = parseFloat(row.querySelector('.i-price').value) || 0;
                total += (q * p);
            });
            const display = document.getElementById('inv_displayTotal');
            if (display) display.value = '₱ ' + total.toLocaleString('en-US', { minimumFractionDigits: 2 });
        }

        function createInvoiceRow(desc = '', qty = 1, price = 0) {
            const container = document.getElementById('invItemsContainer');
            const div = document.createElement('div');
            div.classList.add('item-row');
            div.innerHTML = `
                <input type="text" class="i-desc" value="${desc}" placeholder="Description" style="flex:2; padding:8px; border:1px solid #ddd; border-radius:4px;" required>
                <input type="number" class="i-qty" value="${qty}" min="1" style="flex:0.5; padding:8px; border:1px solid #ddd; border-radius:4px;" required>
                <input type="number" class="i-price" value="${price}" step="0.01" style="flex:1; padding:8px; border:1px solid #ddd; border-radius:4px;" required>
                <button type="button" class="btn-remove" style="color:#ef4444; background:none; border:none; cursor:pointer;"><i class="fas fa-trash"></i></button>
            `;
            div.querySelectorAll('input').forEach(i => i.addEventListener('input', calculateInvoiceTotal));
            div.querySelector('.btn-remove').addEventListener('click', () => { div.remove(); calculateInvoiceTotal(); });
            container.appendChild(div);
        }

        const quoteItemsContainer = document.getElementById('quoteItemsContainer');
        const displayQuoteTotal = document.getElementById('displayQuoteTotal');

        function calculateQuoteTotal() {
            let total = 0;
            document.querySelectorAll('#quoteItemsContainer .item-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
                const price = parseFloat(row.querySelector('.item-price').value) || 0;
                total += (qty * price);
            });
            if (displayQuoteTotal) {
                displayQuoteTotal.value = '₱ ' + total.toLocaleString('en-US', { minimumFractionDigits: 2 });
            }
        }

        function createQuoteItemRow(desc = '', qty = 1, price = '') {
            const div = document.createElement('div');
            div.classList.add('item-row');
            div.innerHTML = `
                <input type="text" class="input-desc item-desc" placeholder="Description" value="${desc}" required style="flex:2; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                <input type="number" class="input-small item-qty" placeholder="Qty" value="${qty}" min="1" required style="flex:0.8; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                <input type="number" class="input-small item-price" placeholder="Price" step="0.01" value="${price}" min="0" required style="flex:1; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                <button type="button" class="btn-remove" style="background:none; border:none; color:#ef4444; cursor:pointer;">
                    <i class="far fa-trash-alt"></i>
                </button>
            `;
            div.querySelectorAll('input').forEach(input => input.addEventListener('input', calculateQuoteTotal));
            div.querySelector('.btn-remove').addEventListener('click', () => { div.remove(); calculateQuoteTotal(); });
            quoteItemsContainer.appendChild(div);
            calculateQuoteTotal();
        }

        const table = document.querySelector('.data-table');
        if (table) {
            table.addEventListener('click', (e) => {
                const editBtn = e.target.closest('.edit-btn');
                if (editBtn) {
                    e.stopPropagation();
                    const pId = editBtn.getAttribute('data-id');
                    const pName = editBtn.getAttribute('data-name');
                    const pDeadline = editBtn.getAttribute('data-deadline');
                    const pBudget = editBtn.getAttribute('data-budget');
                    const pClientId = editBtn.getAttribute('data-client-id');
                    const linkedQuoteId = editBtn.getAttribute('data-quote-id');

                    document.getElementById('project_id').value = pId;
                    document.getElementById('p_deadline').value = pDeadline;
                    document.getElementById('modalTitle').innerText = "Edit Project";
                    if (document.getElementById('quoteContainer')) document.getElementById('quoteContainer').style.display = 'none';

                    const nameField = document.getElementById('p_name');
                    const budgetField = document.getElementById('p_budget');

                    if (linkedQuoteId && linkedQuoteId !== "null" && linkedQuoteId !== "") {
                        nameField.value = pName;
                        nameField.readOnly = true;
                        nameField.style.backgroundColor = "#f1f5f9";
                        budgetField.value = pBudget;
                        budgetField.readOnly = true;
                        budgetField.style.backgroundColor = "#f1f5f9";
                        if (tsProjClient) {
                            tsProjClient.setValue(pClientId);
                            tsProjClient.lock();
                        }
                    } else {
                        nameField.value = pName;
                        nameField.readOnly = false;
                        nameField.style.backgroundColor = "#fff";
                        budgetField.value = pBudget;
                        budgetField.readOnly = false;
                        budgetField.style.backgroundColor = "#fff";
                        if (tsProjClient) {
                            tsProjClient.unlock();
                            tsProjClient.setValue(pClientId);
                        }
                    }
                    document.getElementById('projectModal').style.display = 'flex';
                    return;
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
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            try {
                                const response = await fetch(`/projects/${id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken,
                                        'Accept': 'application/json'
                                    }
                                });
                                if (response.ok) {
                                    Swal.fire('Deleted!', 'Project has been deleted.', 'success').then(() => window.location.reload());
                                } else {
                                    Swal.fire('Error', 'Could not delete project.', 'error');
                                }
                            } catch (error) {
                                console.error(error);
                            }
                        }
                    });
                    return;
                }

                const row = e.target.closest('tr[data-href]');
                if (row && !e.target.closest('button') && !e.target.closest('.btn-action')) {
                    window.location.href = row.dataset.href;
                }
            });
        }

        Object.keys(modalMap).forEach(btnId => {
            document.getElementById(btnId)?.addEventListener('click', (e) => {
                e.preventDefault();
                const m = document.getElementById(modalMap[btnId]);
                if (!m) return;

                m.style.display = 'flex';
                const today = new Date().toISOString().split('T')[0];

                if (btnId === 'addProjectBtn') {
                    document.getElementById('project_id').value = '';
                    const nameField = document.getElementById('p_name');
                    const budgetField = document.getElementById('p_budget');
                    nameField.value = '';
                    nameField.readOnly = false;
                    nameField.style.backgroundColor = "#fff";
                    budgetField.value = '';
                    budgetField.readOnly = false;
                    budgetField.style.backgroundColor = "#fff";
                    document.getElementById('p_deadline').value = '';
                    if (document.getElementById('quoteContainer')) document.getElementById('quoteContainer').style.display = 'block';
                    if (tsQuoteLink) tsQuoteLink.clear();
                    if (tsProjClient) { tsProjClient.unlock(); tsProjClient.clear(); }
                    document.getElementById('modalTitle').innerText = "Add New Project";
                }

                if (btnId === 'createQuoteBtn') {
                    const form = document.getElementById('createQuoteForm');
                    if (form) form.reset();
                    if (quoteClientSelect) quoteClientSelect.clear();
                    quoteItemsContainer.innerHTML = '';
                    document.getElementById('quoteDate').value = today;
                    document.getElementById('validUntil').value = today;
                    createQuoteItemRow();
                }

                if (btnId === 'createInvoiceBtn') {
                    document.getElementById('inv_issue').value = today;
                    const due = new Date();
                    due.setDate(new Date().getDate() + 14);
                    document.getElementById('inv_due').value = due.toISOString().split('T')[0];
                }
            });
        });

        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.querySelectorAll('.close-modal-btn, .btn-cancel').forEach(btn =>
                btn.addEventListener('click', () => closeModal(overlay))
            );
        });

        document.getElementById('addQuoteItemBtn')?.addEventListener('click', () => createQuoteItemRow());
        document.getElementById('addInvItemBtn')?.addEventListener('click', () => createInvoiceRow());

        const sendData = async (url, data, modalId, method = 'POST') => {
            try {
                const res = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(data)
                });
                const result = await res.json();
                if (res.ok) {
                    document.getElementById(modalId).style.display = 'none';
                    Swal.fire({ icon: 'success', title: 'Saved!', timer: 1000, showConfirmButton: false }).then(() => location.reload());
                } else {
                    Swal.fire('Error', result.message || 'Validation Failed', 'error');
                }
            } catch (e) { Swal.fire('Error', 'System Error', 'error'); }
        };

        document.getElementById('invoiceForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const items = [];
            document.querySelectorAll('#invItemsContainer .item-row').forEach(row => {
                items.push({
                    desc: row.querySelector('.i-desc').value,
                    qty: row.querySelector('.i-qty').value,
                    price: row.querySelector('.i-price').value
                });
            });
            sendData("{{ route('invoices.store') }}", {
                project_id: document.getElementById('inv_project').value,
                client_id: document.getElementById('inv_clientId').value,
                issue_date: document.getElementById('inv_issue').value,
                due_date: document.getElementById('inv_due').value,
                items: items
            }, 'invoiceModal');
        });

        document.getElementById('createQuoteForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const items = [];
            document.querySelectorAll('#quoteItemsContainer .item-row').forEach(row => {
                items.push({
                    description: row.querySelector('.item-desc').value,
                    quantity: row.querySelector('.item-qty').value,
                    price: row.querySelector('.item-price').value
                });
            });
            sendData("{{ route('quotes.store') }}", {
                client_id: document.getElementById('q_clientId').value,
                subject: document.getElementById('quoteSubject').value,
                quote_date: document.getElementById('quoteDate').value,
                valid_until: document.getElementById('validUntil').value,
                items: items
            }, 'addQuoteModal');
        });

        document.getElementById('projectForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            const id = document.getElementById('project_id').value;
            const method = id ? 'PUT' : 'POST';
            const url = id ? `/projects/${id}` : "{{ route('projects.create') }}";
            const formData = {
                project_name: document.getElementById('p_name').value,
                client_id: document.getElementById('p_client').value,
                project_end_date: document.getElementById('p_deadline').value,
                project_budget: document.getElementById('p_budget').value,
            };
            if (!id) formData.quote_id = document.getElementById('p_quote')?.value || null;
            sendData(url, formData, 'projectModal', method);
        });

        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('projectsTableBody');
        if (searchInput && tableBody) {
            searchInput.addEventListener('keyup', function() {
                const filter = searchInput.value.toLowerCase();
                const rows = tableBody.getElementsByTagName('tr');
                for (let i = 0; i < rows.length; i++) {
                    let textContent = rows[i].innerText.toLowerCase();
                    rows[i].style.display = textContent.includes(filter) ? "" : "none";
                }
            });
        }
    });
</script>
@endpush