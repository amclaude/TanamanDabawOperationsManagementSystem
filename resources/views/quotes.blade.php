@extends('layouts.app')

@section('title', 'Quotes | Tanaman')

@section('content')

<style>
    /* Status Badges */
    .status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .status-badge.pending {
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #ffedd5;
    }

    .status-badge.accepted {
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #dcfce7;
    }

    .status-badge.rejected {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fee2e2;
    }

    /* Archived Status Design */
    .status-badge.archived {
        background-color: #f1f5f9;
        color: #64748b;
        border: 1px solid #cbd5e1;
    }

    /* Table & Actions */
    .item-row {
        display: flex;
        gap: 8px;
        margin-bottom: 8px;
        align-items: center;
    }

    .item-cell {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .item-cell-desc {
        flex: 2;
    }

    .item-cell-qty {
        flex: 0.8;
    }

    .item-cell-price {
        flex: 1;
    }

    .input-invalid,
    .input-invalid:focus {
        border: 2px solid #dc3545 !important;
        box-shadow: none !important;
    }

    .ts-wrapper.ts-invalid .ts-control {
        border: 2px solid #dc3545 !important;
        box-shadow: none !important;
    }

    .field-error {
        color: #dc3545;
        font-size: 0.78rem;
        margin-top: 4px;
        display: block;
        min-height: 16px;
    }

    .ts-control {
        border-radius: 6px;
        padding: 10px;
        border: 1px solid #ddd;
    }

    .ts-dropdown {
        z-index: 99999 !important;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        cursor: pointer;
        transition: opacity 0.2s;
        margin-right: 6px;
    }

    .edit-btn {
        background-color: #10b981;
    }

    .edit-btn:hover {
        background-color: #059669;
    }

    .delete-btn {
        background-color: #ef4444;
    }

    .delete-btn:hover {
        background-color: #dc2626;
    }


    .view-btn {
        background-color: #10b981;
        color: #fff;
        border: 1px solid #dbeafe;
    }

    .view-btn:hover {
        background-color: #059669;
    }

    .view-mode-active .btn-remove,
    .view-mode-active #addItemBtn,
    .view-mode-active #saveQuoteBtn {
        display: none !important;
    }

    .view-mode-active input {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        color: #64748b;
    }

    /* Tabs Container */
    .tabs-container {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 20px;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 10px;
    }

    /* Status Tabs -*/
    .status-tabs {
        display: flex;
        gap: 6px;
        align-items: center;
    }

    .status-tab {
        padding: 6px 14px;
        cursor: pointer;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        transition: all 0.3s ease;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    /* Pending Tab - Orange Theme */
    .status-tab.pending-tab {
        background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
        color: #c2410c;
        border: 1px solid #fed7aa;
    }

    .status-tab.pending-tab:hover {
        background: linear-gradient(135deg, #ffedd5 0%, #fed7aa 100%);
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(194, 65, 12, 0.2);
    }

    .status-tab.pending-tab.active {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: white;
        border-color: #c2410c;
        box-shadow: 0 3px 8px rgba(234, 88, 12, 0.4);
    }

    /* Accepted Tab - Green Theme */
    .status-tab.accepted-tab {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .status-tab.accepted-tab:hover {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(21, 128, 61, 0.2);
    }

    .status-tab.accepted-tab.active {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        color: white;
        border-color: #15803d;
        box-shadow: 0 3px 8px rgba(22, 163, 74, 0.4);
    }

    /* All Tab - Blue Theme */
    .status-tab.all-tab {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        color: #1e40af;
        border: 1px solid #bfdbfe;
    }

    .status-tab.all-tab:hover {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%    );
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(30, 64, 175, 0.2);
    }

    .status-tab.all-tab.active {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border-color: #1e40af;
        box-shadow: 0 3px 8px rgba(37, 99, 235, 0.4);
    }

    .status-tab .count {
        background: rgba(255,255,255,0.9);
        color: inherit;
        padding: 1px 6px;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 700;
        min-width: 18px;
        text-align: center;
    }

    .status-tab.active .count {
        background: rgba(255,255,255,0.95);
        color: #333;
    }

    .table-section {
        display: none;
        animation: fadeIn 0.3s ease;
    }

    .table-section.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="page-header">
    <div>
        <h2>Quotes</h2>
        <p>View and manage quotes</p>
    </div>

    <div style="display: flex; gap: 10px; align-items: center;">
        <form action="{{ route('quotes') }}" method="GET" style="position: relative; margin: 0;">
            <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Search quotes..." style="padding: 10px 10px 10px 35px; border: 1px solid #ddd; border-radius: 6px; outline: none; width: 250px;">
            <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
        </form>

        <button class="btn-primary" id="addQuoteBtn">
            <i class="fas fa-plus"></i> Create Quote
        </button>
    </div>
</div>

<!-- Tabs Container: Below header, aligned right -->
<div class="tabs-container">
    <div class="status-tabs">
        <button class="status-tab all-tab active" data-status="all" onclick="switchStatusTab('all')">
            <i class="fas fa-list"></i>
            All
            <span class="count">{{ $quoteCounts['all'] }}</span>
        </button>
        <button class="status-tab pending-tab" data-status="pending" onclick="switchStatusTab('pending')">
            <i class="fas fa-clock"></i>
            Pending
            <span class="count">{{ $quoteCounts['pending'] }}</span>
        </button>
        <button class="status-tab accepted-tab" data-status="accepted" onclick="switchStatusTab('accepted')">
            <i class="fas fa-check-circle"></i>
            Accepted
            <span class="count">{{ $quoteCounts['accepted'] }}</span>
        </button>
    </div>
</div>

<!-- All Quotes Table -->
<div class="table-section active" id="all-section">
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
                    <th>Client / Subject</th>
                    <th>Total</th>
                    <th>Created</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="quotes-tbody" data-status="all">
                @forelse($quotes as $quote)
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #334155;">{{ $quote->subject ?? 'Unknown Subject' }}</div>
                        <div style="font-size: 0.85rem; color: #64748b;">{{ $quote->client->name ?? 'Unknown Client' }}</div>
                    </td>
                    <td>₱{{ number_format($quote->total_amount, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($quote->quote_date)->format('M d, Y') }}</td>
                    <td>
                        <span class="status-badge {{ strtolower($quote->status) }}">{{ ucfirst($quote->status) }}</span>
                    </td>
                    <td>
                        @if(strtolower($quote->status) === 'archived')
                        <button class="btn-action view-btn" data-quote="{{ json_encode($quote) }}" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        @else
                        <button class="btn-action edit-btn" data-quote="{{ json_encode($quote) }}" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-action delete-btn" data-id="{{ $quote->id }}" title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px; color: #64748b;">No quotes found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>

<!-- Pending Quotes Table -->
<div class="table-section" id="pending-section">
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
                    <th>Client / Subject</th>
                    <th>Total</th>
                    <th>Created</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="quotes-tbody" data-status="pending">
                @forelse($pendingQuotes as $quote)
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #334155;">{{ $quote->subject ?? 'Unknown Subject' }}</div>
                        <div style="font-size: 0.85rem; color: #64748b;">{{ $quote->client->name ?? 'Unknown Client' }}</div>
                    </td>
                    <td>₱{{ number_format($quote->total_amount, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($quote->quote_date)->format('M d, Y') }}</td>
                    <td>
                        <span class="status-badge {{ strtolower($quote->status) }}">{{ ucfirst($quote->status) }}</span>
                    </td>
                    <td>
                        @if(strtolower($quote->status) === 'archived')
                        <button class="btn-action view-btn" data-quote="{{ json_encode($quote) }}" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        @else
                        <button class="btn-action edit-btn" data-quote="{{ json_encode($quote) }}" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-action delete-btn" data-id="{{ $quote->id }}" title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px; color: #64748b;">No pending quotes found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>

<!-- Accepted Quotes Table -->
<div class="table-section" id="accepted-section">
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
                    <th>Client / Subject</th>
                    <th>Total</th>
                    <th>Created</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="quotes-tbody" data-status="accepted">
                @forelse($acceptedQuotes as $quote)
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #334155;">{{ $quote->subject ?? 'Unknown Subject' }}</div>
                        <div style="font-size: 0.85rem; color: #64748b;">{{ $quote->client->name ?? 'Unknown Client' }}</div>
                    </td>
                    <td>₱{{ number_format($quote->total_amount, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($quote->quote_date)->format('M d, Y') }}</td>
                    <td>
                        <span class="status-badge {{ strtolower($quote->status) }}">{{ ucfirst($quote->status) }}</span>
                    </td>
                    <td>
                        @if(strtolower($quote->status) === 'archived')
                        <button class="btn-action view-btn" data-quote="{{ json_encode($quote) }}" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        @else
                        <button class="btn-action edit-btn" data-quote="{{ json_encode($quote) }}" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-action delete-btn" data-id="{{ $quote->id }}" title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px; color: #64748b;">No accepted quotes found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>

@include('partials.pagination', ['data' => $quotes->appends(request()->query())])

<div class="modal-overlay" id="addQuoteModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modalTitle">Create New Quote</h3>
            <span class="close-modal-btn">&times;</span>
        </div>

        <form class="modal-form" id="createQuoteForm" novalidate>
            <div class="input-group">
                <label>Client</label>
                <select id="clientId" required>
                    <option value="">Select a Client...</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
                <span class="field-error" id="clientId-error"></span>
            </div>

            <div class="input-group">
                <label>Subject</label>
                <input type="text" id="quoteSubject" placeholder="e.g. Garden Maintenance" required>
                <span class="field-error" id="quoteSubject-error"></span>
            </div>

            <div style="display: flex; gap: 10px;">
                <div class="input-group" style="flex:1;">
                    <label>Quote Date</label>
                    <input type="date" id="quoteDate" required>
                    <span class="field-error" id="quoteDate-error"></span>
                </div>
                <div class="input-group" style="flex:1;">
                    <label>Valid Until</label>
                    <input type="date" id="validUntil" required>
                    <span class="field-error" id="validUntil-error"></span>
                </div>
            </div>

            <div class="form-group">
                <div class="line-items-header" style="display:flex; justify-content:space-between; margin: 10px 0 5px 0;">
                    <label>Line Items</label>
                    <span class="add-item-link" id="addItemBtn" style="cursor:pointer; color:#319B72;">+ Add item</span>
                </div>
                <div id="itemsContainer"></div>
                <span class="field-error" id="items-error"></span>
            </div>

            <div class="input-group" style="margin-top: 10px;">
                <label>Total Amount (₱)</label>
                <input type="text" id="displayTotal" placeholder="0.00" readonly style="font-weight: bold; background-color: #f8fafc;">
                <span class="field-error" id="displayTotal-error"></span>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel">Close</button>
                <button type="submit" class="btn-save" id="saveQuoteBtn">Create Quote</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11 "></script>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.default.min.css " rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js "></script>

<script>
    // Tab switching function
    function switchStatusTab(status) {
        // Update tab buttons
        document.querySelectorAll('.status-tab').forEach(tab => {
            tab.classList.remove('active');
            if (tab.dataset.status === status) {
                tab.classList.add('active');
            }
        });

        // Update table sections
        document.querySelectorAll('.table-section').forEach(section => {
            section.classList.remove('active');
        });
        document.getElementById(status + '-section').classList.add('active');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Initialize TomSelect
        let clientSelect;
        if (document.getElementById('clientId')) {
            clientSelect = new TomSelect("#clientId", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "Select a Client..."
            });
        }

        const modal = document.getElementById('addQuoteModal');
        const modalTitle = document.getElementById('modalTitle');
        const form = document.getElementById('createQuoteForm');
        const itemsContainer = document.getElementById('itemsContainer');
        const totalInput = document.getElementById('displayTotal');
        const saveBtn = document.getElementById('saveQuoteBtn');
        const addItemBtn = document.getElementById('addItemBtn');
        const clientInput = document.getElementById('clientId');
        const subjectInput = document.getElementById('quoteSubject');
        const quoteDateInput = document.getElementById('quoteDate');
        const validUntilInput = document.getElementById('validUntil');

        let isEditMode = false;
        let editId = null;
        let submitOriginalText = saveBtn.innerText;
        let itemRowCounter = 0;
        let hasTriedSubmit = false;

        const toggleTomSelectInvalid = (isInvalid) => {
            if (!clientSelect) return;
            const wrapper = clientSelect.wrapper;
            if (!wrapper) return;
            if (isInvalid) {
                wrapper.classList.add('ts-invalid');
            } else {
                wrapper.classList.remove('ts-invalid');
            }
        };

        const setFieldError = (input, errorId, message, isTomSelect = false) => {
            if (input) {
                if (isTomSelect) {
                    toggleTomSelectInvalid(true);
                } else {
                    input.classList.add('input-invalid');
                }
            }
            const errorEl = document.getElementById(errorId);
            if (errorEl) errorEl.textContent = message;
        };

        const clearFieldError = (input, errorId, isTomSelect = false) => {
            if (input) {
                if (isTomSelect) {
                    toggleTomSelectInvalid(false);
                } else {
                    input.classList.remove('input-invalid');
                }
            }
            const errorEl = document.getElementById(errorId);
            if (errorEl) errorEl.textContent = '';
        };

        const clearAllFormErrors = () => {
            clearFieldError(clientInput, 'clientId-error', true);
            clearFieldError(subjectInput, 'quoteSubject-error');
            clearFieldError(quoteDateInput, 'quoteDate-error');
            clearFieldError(validUntilInput, 'validUntil-error');
            clearFieldError(totalInput, 'displayTotal-error');
            const itemsError = document.getElementById('items-error');
            if (itemsError) itemsError.textContent = '';
            itemsContainer.querySelectorAll('.item-desc, .item-qty, .item-price').forEach(el => el.classList.remove('input-invalid'));
            itemsContainer.querySelectorAll('.field-error[data-item-error]').forEach(el => el.textContent = '');
        };

        // --- Helper Functions ---

        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
                const price = parseFloat(row.querySelector('.item-price').value) || 0;
                total += (qty * price);
            });
            totalInput.value = '₱ ' + total.toLocaleString('en-US', {
                minimumFractionDigits: 2
            });
            return total;
        }

        function createItemRow(desc = '', qty = 1, price = '') {
            const div = document.createElement('div');
            div.classList.add('item-row');
            const rowIndex = itemRowCounter++;
            div.innerHTML = `
                <div class="item-cell item-cell-desc">
                    <input type="text" class="input-desc item-desc" id="item-description-${rowIndex}" placeholder="Description" value="${desc}" required style="min-width:0;">
                    <span class="field-error" data-item-error="desc"></span>
                </div>
                <div class="item-cell item-cell-qty">
                    <input type="number" class="input-small item-qty" id="item-quantity-${rowIndex}" placeholder="Qty" value="${qty}" min="1" required style="min-width:0;">
                    <span class="field-error" data-item-error="qty"></span>
                </div>
                <div class="item-cell item-cell-price">
                    <input type="number" class="input-small item-price" id="item-price-${rowIndex}" placeholder="Price" step="0.01" value="${price}" min="0" required style="min-width:0;">
                    <span class="field-error" data-item-error="price"></span>
                </div>
                <button type="button" class="btn-remove" style="background:none; color:#ef4444; border:none; cursor:pointer; padding:0 5px; font-size: 1.2rem;">
                    <i class="far fa-trash-alt"></i>
                </button>
            `;

            // Add event listeners for calculation
            div.querySelectorAll('input').forEach(input => {
                input.addEventListener('input', () => {
                    calculateTotal();
                    validateQuoteForm(hasTriedSubmit);
                });
            });

            // Remove functionality
            div.querySelector('.btn-remove').addEventListener('click', () => {
                div.remove();
                calculateTotal();
                validateQuoteForm(hasTriedSubmit);
            });

            itemsContainer.appendChild(div);
            calculateTotal();
        }

        function validateRow(row, showErrors = false) {
            let valid = true;
            const descInput = row.querySelector('.item-desc');
            const qtyInput = row.querySelector('.item-qty');
            const priceInput = row.querySelector('.item-price');

            const descError = row.querySelector('[data-item-error="desc"]');
            const qtyError = row.querySelector('[data-item-error="qty"]');
            const priceError = row.querySelector('[data-item-error="price"]');

            descInput.classList.remove('input-invalid');
            qtyInput.classList.remove('input-invalid');
            priceInput.classList.remove('input-invalid');
            if (descError) descError.textContent = '';
            if (qtyError) qtyError.textContent = '';
            if (priceError) priceError.textContent = '';

            if (!descInput.value.trim()) {
                if (showErrors) {
                    descInput.classList.add('input-invalid');
                    if (descError) descError.textContent = 'Description is required';
                }
                valid = false;
            }

            const qty = parseFloat(qtyInput.value);
            if (Number.isNaN(qty) || qty < 1) {
                if (showErrors) {
                    qtyInput.classList.add('input-invalid');
                    if (qtyError) qtyError.textContent = 'Quantity must be at least 1';
                }
                valid = false;
            }

            const price = parseFloat(priceInput.value);
            if (Number.isNaN(price) || price < 0) {
                if (showErrors) {
                    priceInput.classList.add('input-invalid');
                    if (priceError) priceError.textContent = 'Price must be 0 or greater';
                }
                valid = false;
            }

            return valid;
        }

        function validateQuoteForm(showErrors = false) {
            let valid = true;
            const today = new Date().toISOString().split('T')[0];

            if (!clientInput.value) {
                if (showErrors) {
                    setFieldError(clientInput, 'clientId-error', 'Client is required', true);
                }
                valid = false;
            } else {
                clearFieldError(clientInput, 'clientId-error', true);
            }

            const subjectValue = subjectInput.value.trim();
            if (!subjectValue) {
                if (showErrors) {
                    setFieldError(subjectInput, 'quoteSubject-error', 'Subject is required');
                }
                valid = false;
            } else if (subjectValue.length > 255) {
                if (showErrors) {
                    setFieldError(subjectInput, 'quoteSubject-error', 'Subject must not exceed 255 characters');
                }
                valid = false;
            } else {
                clearFieldError(subjectInput, 'quoteSubject-error');
            }

            if (!quoteDateInput.value) {
                if (showErrors) {
                    setFieldError(quoteDateInput, 'quoteDate-error', 'Quote date is required');
                }
                valid = false;
            } else {
                clearFieldError(quoteDateInput, 'quoteDate-error');
            }

            if (!validUntilInput.value) {
                if (showErrors) {
                    setFieldError(validUntilInput, 'validUntil-error', 'Valid until date is required');
                }
                valid = false;
            } else if (validUntilInput.value < today) {
                if (showErrors) {
                    setFieldError(validUntilInput, 'validUntil-error', 'Valid until date must be today or a future date');
                }
                valid = false;
            } else if (quoteDateInput.value && validUntilInput.value < quoteDateInput.value) {
                if (showErrors) {
                    setFieldError(validUntilInput, 'validUntil-error', 'Valid until must be on or after quote date');
                }
                valid = false;
            } else {
                clearFieldError(validUntilInput, 'validUntil-error');
            }

            const rows = itemsContainer.querySelectorAll('.item-row');
            const itemsError = document.getElementById('items-error');
            if (!rows.length) {
                if (showErrors && itemsError) itemsError.textContent = 'At least one line item is required';
                valid = false;
            } else {
                if (itemsError) itemsError.textContent = '';
                rows.forEach(row => {
                    if (!validateRow(row, showErrors)) valid = false;
                });
            }

            const total = calculateTotal();
            if (total < 0) {
                if (showErrors) {
                    setFieldError(totalInput, 'displayTotal-error', 'Total amount must be valid');
                }
                valid = false;
            } else {
                clearFieldError(totalInput, 'displayTotal-error');
            }

            saveBtn.disabled = hasTriedSubmit ? !valid : false;
            return valid;
        }

        function applyBackendErrors(errors = {}) {
            if (errors.client_id?.[0]) setFieldError(clientInput, 'clientId-error', errors.client_id[0], true);
            if (errors.subject?.[0]) setFieldError(subjectInput, 'quoteSubject-error', errors.subject[0]);
            if (errors.quote_date?.[0]) setFieldError(quoteDateInput, 'quoteDate-error', errors.quote_date[0]);
            if (errors.valid_until?.[0]) setFieldError(validUntilInput, 'validUntil-error', errors.valid_until[0]);
            if (errors.items?.[0]) {
                const itemsError = document.getElementById('items-error');
                if (itemsError) itemsError.textContent = errors.items[0];
            }

            itemsContainer.querySelectorAll('.item-row').forEach((row, index) => {
                const descInput = row.querySelector('.item-desc');
                const qtyInput = row.querySelector('.item-qty');
                const priceInput = row.querySelector('.item-price');

                const descError = errors[`items.${index}.description`]?.[0];
                const qtyError = errors[`items.${index}.quantity`]?.[0];
                const priceError = errors[`items.${index}.price`]?.[0];

                if (descError) {
                    descInput.classList.add('input-invalid');
                    row.querySelector('[data-item-error="desc"]').textContent = descError;
                }
                if (qtyError) {
                    qtyInput.classList.add('input-invalid');
                    row.querySelector('[data-item-error="qty"]').textContent = qtyError;
                }
                if (priceError) {
                    priceInput.classList.add('input-invalid');
                    row.querySelector('[data-item-error="price"]').textContent = priceError;
                }
            });
        }

        function setSubmittingState(isSubmitting, label = 'Processing...') {
            if (isSubmitting) {
                submitOriginalText = saveBtn.innerText;
                saveBtn.disabled = true;
                saveBtn.innerText = label;
                return;
            }
            saveBtn.innerText = submitOriginalText;
            validateQuoteForm(hasTriedSubmit);
        }

        function resetModalState() {
            // Unlock TomSelect
            if (clientSelect) clientSelect.unlock();

            // Enable all inputs
            form.querySelectorAll('input').forEach(input => input.disabled = false);

            // Remove View Mode class (shows buttons again)
            form.classList.remove('view-mode-active');

            // Show Save Button and reset text
            saveBtn.style.display = 'inline-block';
            saveBtn.disabled = false;

            form.reset();
            clearAllFormErrors();
            hasTriedSubmit = false;
        }

        // --- Event Listeners ---
        document.getElementById('addQuoteBtn').addEventListener('click', () => {
            resetModalState();
            isEditMode = false;
            editId = null;
            modalTitle.innerText = "Create New Quote";
            saveBtn.innerText = "Create Quote";

            if (clientSelect) clientSelect.clear();

            const today = new Date().toISOString().split('T')[0];
            document.getElementById('quoteDate').value = today;
            document.getElementById('validUntil').value = today;

            itemsContainer.innerHTML = '';
            createItemRow();
            validateQuoteForm(false);
            modal.style.display = 'flex';
        });

        document.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.edit-btn');
            if (editBtn) {
                resetModalState();
                isEditMode = true;
                const quote = JSON.parse(editBtn.dataset.quote);
                editId = quote.id;

                modalTitle.innerText = `Edit Quote`;
                saveBtn.innerText = "Update Quote";
                if (clientSelect) clientSelect.setValue(quote.client_id);

                document.getElementById('quoteSubject').value = quote.subject || '';
                document.getElementById('quoteDate').value = quote.quote_date;
                document.getElementById('validUntil').value = quote.valid_until;

                itemsContainer.innerHTML = '';
                if (quote.items && quote.items.length > 0) {
                    quote.items.forEach(item => {
                        createItemRow(item.description, item.quantity, item.price);
                    });
                } else {
                    createItemRow();
                }
                validateQuoteForm(false);
                modal.style.display = 'flex';
            }
        });

        document.addEventListener('click', function(e) {
            const viewBtn = e.target.closest('.view-btn');
            if (viewBtn) {
                resetModalState(); // Start clean

                const quote = JSON.parse(viewBtn.dataset.quote);

                // Set UI to "View Mode"
                modalTitle.innerText = "View Quote (Archived)";
                form.classList.add('view-mode-active'); // CSS handles hiding buttons/styling

                // Populate Data
                if (clientSelect) {
                    clientSelect.setValue(quote.client_id);
                    clientSelect.lock(); // Disable dropdown
                }

                document.getElementById('quoteSubject').value = quote.subject || '';
                document.getElementById('quoteDate').value = quote.quote_date;
                document.getElementById('validUntil').value = quote.valid_until;

                // Disable inputs
                form.querySelectorAll('input').forEach(input => input.disabled = true);

                // Populate Items (read-only)
                itemsContainer.innerHTML = '';
                if (quote.items && quote.items.length > 0) {
                    quote.items.forEach(item => {
                        createItemRow(item.description, item.quantity, item.price);
                    });
                } else {
                    // Show one empty row if none exist
                    createItemRow();
                }

                // Re-disable item inputs (since createItemRow makes them enabled by default)
                itemsContainer.querySelectorAll('input').forEach(input => input.disabled = true);
                saveBtn.disabled = true;

                modal.style.display = 'flex';
            }
        });

        // Close Modal Logic
        const closeModal = () => modal.style.display = 'none';
        document.querySelector('.close-modal-btn').addEventListener('click', closeModal);
        document.querySelector('.btn-cancel').addEventListener('click', closeModal);

        document.getElementById('addItemBtn').addEventListener('click', () => {
            // Only allow adding items if NOT in view mode
            if (!form.classList.contains('view-mode-active')) {
                createItemRow();
                validateQuoteForm(hasTriedSubmit);
            }
        });

        [subjectInput, quoteDateInput, validUntilInput, clientInput].forEach(input => {
            input.addEventListener('input', () => validateQuoteForm(hasTriedSubmit));
            input.addEventListener('change', () => validateQuoteForm(hasTriedSubmit));
        });

        if (clientSelect) {
            clientSelect.on('change', () => validateQuoteForm(hasTriedSubmit));
        }

        // Form Submit (Create/Update)
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Safety check: Do not submit if in view mode
            if (form.classList.contains('view-mode-active')) return;

            clearAllFormErrors();
            hasTriedSubmit = true;
            if (!validateQuoteForm(true)) {
                const firstInvalidField = form.querySelector('.input-invalid');
                if (firstInvalidField) {
                    firstInvalidField.focus();
                } else if (!clientInput.value && clientSelect) {
                    clientSelect.focus();
                }
                return;
            }

            const items = [];
            document.querySelectorAll('.item-row').forEach(row => {
                items.push({
                    description: row.querySelector('.item-desc').value,
                    quantity: row.querySelector('.item-qty').value,
                    price: row.querySelector('.item-price').value
                });
            });

            const payload = {
                client_id: document.getElementById('clientId').value,
                subject: document.getElementById('quoteSubject').value,
                quote_date: document.getElementById('quoteDate').value,
                valid_until: document.getElementById('validUntil').value,
                items: items
            };

            let url = "{{ route('quotes.store') }}";
            let method = "POST";

            if (isEditMode) {
                url = `/quotes/${editId}`;
                method = "PUT";
            }

            try {
                setSubmittingState(true);
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
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
                    if (result.errors) {
                        applyBackendErrors(result.errors);
                        validateQuoteForm(true);
                    }
                    Swal.fire('Error', result.message || 'Validation failed', 'error');
                }
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'System error occurred', 'error');
            } finally {
                setSubmittingState(false);
            }
        });

        // Delete Logic (Delegated)
        document.addEventListener('click', function(e) {
            const deleteBtn = e.target.closest('.delete-btn');
            if (deleteBtn) {
                const id = deleteBtn.dataset.id;
                Swal.fire({
                    title: 'Delete Quote?',
                    text: "You can restore this later.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const res = await fetch(`/quotes/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });
                            if (res.ok) window.location.reload();
                        } catch (err) {
                            console.error(err);
                        }
                    }
                });
            }
        });

        // Search Logic - works on active table only
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const filter = searchInput.value.toLowerCase();
                const activeSection = document.querySelector('.table-section.active');
                if (activeSection) {
                    const rows = activeSection.querySelectorAll('.quotes-tbody tr');
                    for (let i = 0; i < rows.length; i++) {
                        let textContent = rows[i].innerText.toLowerCase();
                        rows[i].style.display = textContent.includes(filter) ? "" : "none";
                    }
                }
            });
        }
    });
</script>
@endpush
