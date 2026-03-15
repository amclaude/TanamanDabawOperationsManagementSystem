@extends('layouts.app')

@section('title', 'Inventory | Tanaman')

@section('content')

<style>
    /* Reuse styles from your Client System */
    .swal2-container {
        z-index: 20000 !important;
    }

    .btn-secondary {
        background-color: #64748b;
        color: white;
        padding: 8px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-secondary:hover {
        background-color: #475569;
    }

    .btn-primary {
        background-color: #319B72;
        color: white;
        padding: 8px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    /* Inventory Specific Styles */
    .btn-stock-in {
        background: #dcfce7;
        color: #16a34a;
        border: 1px solid #bbf7d0;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.8rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .btn-stock-in:hover {
        background: #bbf7d0;
    }

    .btn-stock-out {
        background: #fee2e2;
        color: #ef4444;
        border: 1px solid #fecaca;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.8rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .btn-stock-out:hover {
        background: #fecaca;
    }

    .stock-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-red {
        background-color: #fef2f2;
        border-bottom: 1px solid #fee2e2;
    }

    .bg-green {
        background-color: #d1fae5;
        color: #047857;
        padding: 10px;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .bg-red-soft {
        background-color: #fee2e2;
        color: #b91c1c;
        padding: 10px;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stock-info-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .stock-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #334155;
    }

    .sku-value {
        font-size: 1.1rem;
        font-weight: 600;
        color: #64748b;
    }

    .input-invalid,
    .input-invalid:focus {
        border: 2px solid #1a4d32 !important;
        background-color: #f0f9f4;
        box-shadow: none !important;
    }

    #itemForm .input-invalid,
    #itemForm .input-invalid:focus {
        border: 2px solid #dc3545 !important;
        background-color: #fef2f2;
    }

    .field-error {
        color: #dc3545;
        font-size: 0.78rem;
        margin-top: 4px;
        display: block;
        min-height: 16px;
    }

    .required-reason-highlight {
        border: 1px solid #e2e8f0 !important;
        background-color: #fff;
    }

    .required-quantity-highlight:placeholder-shown:focus {
        border: 1px solid #0f172a !important;
        box-shadow: none !important;
    }

    .required-quantity-highlight.input-invalid,
    .required-quantity-highlight.input-invalid:focus,
    .required-reason-highlight.input-invalid,
    .required-reason-highlight.input-invalid:focus {
        border: 2px solid #dc3545 !important;
        background-color: #fef2f2;
    }

    .projected-balance {
        font-size: 0.9rem;
        font-weight: 600;
        color: #334155;
    }

    .projected-balance.is-negative {
        color: #dc3545;
    }

    .btn-save:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
</style>

<div class="page-header">
    <div>
        <h2>Inventory</h2>
        <p>Stock levels and product catalog</p>
    </div>

    <div style="display: flex; gap: 10px; align-items: center;">
        {{-- Search Form --}}
        <form action="{{ route('inventory') }}" method="GET" style="position: relative; margin: 0;">
            <input type="text"
                name="search"
                value="{{ request('search') }}"
                id="searchInput"
                placeholder="Search inventory..."
                style="padding: 10px 10px 10px 35px; border: 1px solid #ddd; border-radius: 6px; outline: none; width: 250px;">
            <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
        </form>
        <button class="btn-secondary" id="viewHistoryBtn">
            <i class="fas fa-history"></i>
        </button>
        <button class="btn-primary" id="addItemBtn">
            <i class="fas fa-plus"></i> Add Item
        </button>
    </div>
</div>

<div class="table-container table-fixed-layout loading-scope table-loading-scope" data-loading-scope="network">
    <div class="skeleton-overlay" aria-hidden="true">
        <div class="skeleton table-skeleton-row"></div>
        <div class="skeleton table-skeleton-row"></div>
        <div class="skeleton table-skeleton-row"></div>
        <div class="skeleton table-skeleton-row"></div>
        <div class="skeleton table-skeleton-row"></div>
    </div>
    <div class="loading-content">
    <div class="table-scroll">
    <table class="data-table">
        <thead>
            <tr>
                <th>Item Name</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock Level</th>
                <th>Adjustment</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="inventoryTableBody">
            @forelse($inventoryItems as $item)
            <tr>
                <td>
                    <div style="font-weight: 600; color: #334155;">{{ $item->item_name }}</div>
                </td>
                <td>
                    <div style="color: #64748b; font-weight: 500;">{{ $item->sku }}</div>
                </td>
                <td>
                    {{ $item->category->name ?? 'Uncategorized' }}
                </td>
                <td>₱{{ number_format($item->price, 2) }}</td>
                <td>
                    <span
                        @style([ 'font-weight: 700' , 'font-size: 1.1rem' , 'color: #ef4444'=> $item->stock_level < 5, 'color: #334155'=> $item->stock_level >= 5,
                            ])>
                            {{ $item->stock_level }}
                    </span>
                </td>
                <td>
                    <div style="display: flex; gap: 5px;">
                        <button class="btn-stock-in"
                            data-id="{{ $item->id }}"
                            data-sku="{{ $item->sku }}"
                            data-stock="{{ $item->stock_level }}">
                            <i class="fas fa-arrow-up" style="font-size: 0.7rem;"></i> In
                        </button>
                        <button class="btn-stock-out"
                            data-id="{{ $item->id }}"
                            data-sku="{{ $item->sku }}"
                            data-stock="{{ $item->stock_level }}">
                            <i class="fas fa-arrow-down" style="font-size: 0.7rem;"></i> Out
                        </button>
                    </div>
                </td>
                <td class="actions-cell-nowrap">
                    <div class="d-flex flex-row justify-content-start gap-2 table-action-buttons">
                        {{-- Edit Button --}}
                        <button class="action-btn edit-btn"
                            data-id="{{ $item->id }}"
                            data-name="{{ $item->item_name }}"
                            data-sku="{{ $item->sku }}"
                            data-category="{{ $item->category_id }}"
                            data-price="{{ $item->price }}"
                            data-stock="{{ $item->stock_level }}">
                            <i class="fas fa-pen"></i>
                        </button>

                        {{-- Delete Button --}}
                        <button class="action-btn delete delete-btn"
                            data-id="{{ $item->id }}">
                            <i class="far fa-trash-alt"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 30px; color: #64748b;">No inventory items found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    <div class="table-pagination-sticky">
        @include('partials.pagination', ['data' => $inventoryItems->appends(request()->query())])
    </div>
    </div>
</div>

<div class="modal-overlay" id="historyModal">
    {{-- Made this modal wider (max-width: 800px) so the table fits well --}}
    <div class="modal-box" style="max-width: 800px; width: 90%;">
        <div class="modal-header">
            <h3>Transaction History</h3>
            <span class="close-modal-btn" id="closeHistory">&times;</span>
        </div>

        <div style="padding: 20px; max-height: 60vh; overflow-y: auto;">
            <table class="data-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="background: #f8fafc;">Date & Time</th>
                        <th style="background: #f8fafc;">Item Name</th>
                        <th style="background: #f8fafc;">Type</th>
                        <th style="background: #f8fafc;">Qty</th>
                        <th style="background: #f8fafc;">Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($transactions) && $transactions->count() > 0)
                    @foreach($transactions as $log)
                    <tr>
                        <td style="color: #64748b; font-size: 0.9rem;">{{ $log->created_at->format('M d, h:i A') }}</td>
                        <td style="font-weight: 600;">{{ $log->inventory->item_name ?? 'Deleted Item' }}</td>
                        <td>
                            @if($log->type === 'IN')
                            <span class="badge-in">IN</span>
                            @else
                            <span class="badge-out">OUT</span>
                            @endif
                        </td>
                        <td style="font-weight: 700;">{{ $log->quantity }}</td>
                        <td style="color: #475569; font-size: 0.9rem;">{{ $log->reason ?? '--' }}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px; color: #64748b;">No history available.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="modal-actions" style="border-top: 1px solid #eee; margin-top: 0; padding-top: 20px;">
            <button class="btn-cancel" id="closeHistoryBtn">Close</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="addItemModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="itemModalTitle">Add New Item</h3>
            <span class="close-modal-btn" id="closeAddItem">&times;</span>
        </div>

        <form class="modal-form" id="itemForm" novalidate>
            <input type="hidden" id="item_id">

            <div class="input-group">
                <label>Item Name</label>
                <input type="text" id="itemName" placeholder="e.g. Fertilizer" data-error-target="itemNameError">
                <span class="field-error" id="itemNameError"></span>
            </div>
            <div style="display: flex; gap: 15px;">
                <div class="input-group" style="flex:1;">
                    <label>SKU</label>
                    <input type="text" id="itemSku" placeholder="e.g. FUR-001" data-error-target="itemSkuError">
                    <span class="field-error" id="itemSkuError"></span>
                </div>
                <div class="input-group" style="flex:1;">
                    <label>Category</label>
                    <select id="itemCategory" data-error-target="itemCategoryError" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px;">
                        @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <span class="field-error" id="itemCategoryError"></span>
                </div>
            </div>
            <div style="display: flex; gap: 15px;">
                <div class="input-group" style="flex:1;">
                    <label>Price (₱)</label>
                    <input type="number" id="itemPrice" step="0.01" min="0" placeholder="0.00" data-error-target="itemPriceError">
                    <span class="field-error" id="itemPriceError"></span>
                </div>
                {{-- Initial Stock is only visible on CREATE, hidden on UPDATE --}}
                <div class="input-group" style="flex:1;" id="initialStockGroup">
                    <label>Initial Stock</label>
                    <input type="number" id="itemStock" min="0" placeholder="0" data-error-target="itemStockError">
                    <span class="field-error" id="itemStockError"></span>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" id="cancelAddItem">Cancel</button>
                <button type="submit" class="btn-save" id="itemSaveBtn">Save Item</button>
            </div>
        </form>
    </div>
</div>


<div class="modal-overlay" id="stockInModal">
    <div class="modal-box" style="max-width: 450px;">
        <div class="stock-header">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div class="header-icon-box bg-green"><i class="fas fa-arrow-up"></i></div>
                <h3 style="margin: 0; color: #064e3b;">Stock In</h3>
            </div>
            <span class="close-modal-btn" id="closeStockIn">&times;</span>
        </div>

        <div style="padding: 0 20px 20px 20px;">
            <div class="stock-info-card">
                <div>
                    <label>CURRENT STOCK</label>
                    <div class="stock-value" id="inModalCurrentStock">--</div>
                </div>
                <div style="text-align: right;">
                    <label>ITEM SKU</label>
                    <div class="sku-value" id="inModalSku">--</div>
                </div>
            </div>

            <form class="stock-form" id="stockInForm" novalidate>
                <input type="hidden" id="inItemId">
                <div class="input-group">
                    <label>QUANTITY TO ADD</label>
                    <input type="number" id="inQuantity" class="dark-input required-quantity-highlight" placeholder="0" min="1" data-error-target="inQuantityError">
                    <span class="field-error" id="inQuantityError"></span>
                </div>
                <div class="input-group">
                    <label>REASON / REFERENCE</label>
                    <div class="input-with-icon">
                        <input type="text" id="inReason" class="dark-input" placeholder="e.g. Shipment #1234" data-error-target="inReasonError" style="width:100%; padding: 10px;">
                    </div>
                    <span class="field-error" id="inReasonError"></span>
                </div>
                <div class="modal-actions" style="margin-top: 25px; align-items: center;">
                    <button type="button" class="btn-cancel" id="cancelStockIn">Cancel</button>
                    <button type="submit" class="btn-save" id="stockInSubmitBtn" style="background-color: #1a4d32;">Confirm Stock In</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal-overlay" id="stockOutModal">
    <div class="modal-box" style="max-width: 450px;">
        <div class="stock-header header-red">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div class="header-icon-box bg-red-soft"><i class="fas fa-arrow-down"></i></div>
                <h3 style="margin: 0; color: #7f1d1d;">Stock Out</h3>
            </div>
            <span class="close-modal-btn" id="closeStockOut">&times;</span>
        </div>

        <div style="padding: 0 20px 20px 20px;">
            <div class="stock-info-card">
                <div>
                    <label>CURRENT STOCK</label>
                    <div class="stock-value" id="outModalCurrentStock">--</div>
                </div>
                <div style="text-align: right;">
                    <label>ITEM SKU</label>
                    <div class="sku-value" id="outModalSku">--</div>
                </div>
            </div>

            <form class="stock-form" id="stockOutForm" novalidate>
                <input type="hidden" id="outItemId">
                <div class="input-group">
                    <label>QUANTITY TO REMOVE</label>
                    <input type="number" id="outQuantity" class="dark-input required-quantity-highlight" placeholder="0" min="1" data-error-target="outQuantityError">
                    <span class="field-error" id="outQuantityError"></span>
                </div>
                <div class="input-group">
                    <label>PROJECTED BALANCE</label>
                    <div class="projected-balance" id="projectedBalanceDisplay">--</div>
                </div>
                <div class="input-group">
                    <label style="color: #1a4d32; font-weight: 700;">REASON / REFERENCE (REQUIRED)</label>
                    <div class="input-with-icon">
                        <input type="text" id="outReason" class="dark-input required-reason-highlight" placeholder="e.g. Sales Order #9988" data-error-target="outReasonError" style="width:100%; padding: 10px;">
                    </div>
                    <span class="field-error" id="outReasonError"></span>
                </div>
                <div class="modal-actions" style="margin-top: 25px; align-items: center;">
                    <button type="button" class="btn-cancel" id="cancelStockOut">Cancel</button>
                    <button type="submit" class="btn-save btn-red" id="stockOutSubmitBtn" style="background-color: #ef4444; color:white;">Confirm Stock Out</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const validation = window.TanamanValidation;

        const setFieldError = validation.setFieldError;
        const clearFieldError = validation.clearFieldError;
        const clearFormErrors = validation.clearFormErrors;
        const focusFirstInvalidField = validation.focusFirstInvalidField;
        const setSubmittingState = validation.setSubmittingState;
        const lockSubmitUntilFieldChange = validation.lockSubmitUntilFieldChange;
        const setValidationLock = validation.setValidationLock;
        const setRuleLock = validation.setRuleLock;
        const bindClearOnInput = validation.bindClearOnInput;

        function showSuccess(message) {
            Swal.fire({
                title: 'Success!',
                text: message,
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => location.reload());
        }

        function showError(message) {
            Swal.fire('Error', message || 'Something went wrong', 'error');
        }

        const historyModal = document.getElementById('historyModal');
        const viewHistoryBtn = document.getElementById('viewHistoryBtn');
        const closeHistory = document.getElementById('closeHistory');
        const closeHistoryBtn = document.getElementById('closeHistoryBtn');

        // History Modal
        if (viewHistoryBtn) viewHistoryBtn.addEventListener('click', () => historyModal.style.display = 'flex');
        if (closeHistory) closeHistory.addEventListener('click', () => historyModal.style.display = 'none');
        if (closeHistoryBtn) closeHistoryBtn.addEventListener('click', () => historyModal.style.display = 'none');

        // Add / Edit Item Logic
        const addItemModal = document.getElementById('addItemModal');
        const itemForm = document.getElementById('itemForm');
        const itemSaveBtn = document.getElementById('itemSaveBtn');
        const itemIdField = document.getElementById('item_id');
        const itemNameField = document.getElementById('itemName');
        const itemSkuField = document.getElementById('itemSku');
        const itemCategoryField = document.getElementById('itemCategory');
        const itemPriceField = document.getElementById('itemPrice');
        const itemStockField = document.getElementById('itemStock');
        const initialStockGroup = document.getElementById('initialStockGroup');
        const itemFields = [itemNameField, itemSkuField, itemCategoryField, itemPriceField, itemStockField];

        const validateItemForm = () => {
            clearFormErrors(itemFields);

            const isEdit = Boolean(itemIdField.value);
            const name = itemNameField.value.trim();
            const sku = itemSkuField.value.trim();
            const categoryId = itemCategoryField.value;
            const priceRaw = itemPriceField.value.trim();
            const stockRaw = itemStockField.value.trim();
            let isValid = true;

            if (!name) {
                setFieldError(itemNameField, 'Item name is required.');
                isValid = false;
            } else if (name.length > 255) {
                setFieldError(itemNameField, 'Item name must not exceed 255 characters.');
                isValid = false;
            }

            if (!sku) {
                setFieldError(itemSkuField, 'SKU is required.');
                isValid = false;
            }

            if (!categoryId) {
                setFieldError(itemCategoryField, 'Category is required.');
                isValid = false;
            }

            if (!priceRaw) {
                setFieldError(itemPriceField, 'Price is required.');
                isValid = false;
            } else if (Number.isNaN(Number(priceRaw)) || Number(priceRaw) < 0) {
                setFieldError(itemPriceField, 'Price must be a valid number greater than or equal to 0.');
                isValid = false;
            }

            if (!isEdit) {
                if (!stockRaw) {
                    setFieldError(itemStockField, 'Initial stock is required.');
                    isValid = false;
                } else if (!Number.isInteger(Number(stockRaw)) || Number(stockRaw) < 0) {
                    setFieldError(itemStockField, 'Initial stock must be a whole number greater than or equal to 0.');
                    isValid = false;
                }
            }

            return isValid;
        };

        bindClearOnInput(itemFields, clearFieldError);

        // Buttons
        document.getElementById('addItemBtn').addEventListener('click', () => {
            itemIdField.value = '';
            itemForm.reset();
            clearFormErrors(itemFields);
            setValidationLock(itemSaveBtn, false);
            setRuleLock(itemSaveBtn, false);
            setSubmittingState(itemSaveBtn, false);
            document.getElementById('itemModalTitle').innerText = 'Add New Item';
            initialStockGroup.style.display = 'block';
            addItemModal.style.display = 'flex';
        });

        // Close/Cancel
        const closeItem = () => {
            addItemModal.style.display = 'none';
            clearFormErrors(itemFields);
            setValidationLock(itemSaveBtn, false);
            setRuleLock(itemSaveBtn, false);
            setSubmittingState(itemSaveBtn, false);
        };

        document.getElementById('closeAddItem').addEventListener('click', closeItem);
        document.getElementById('cancelAddItem').addEventListener('click', closeItem);

        // Edit Button Click
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                itemIdField.value = btn.dataset.id;
                itemNameField.value = btn.dataset.name;
                itemSkuField.value = btn.dataset.sku;
                itemCategoryField.value = btn.dataset.category;
                itemPriceField.value = btn.dataset.price;
                itemStockField.value = '';
                clearFormErrors(itemFields);
                setValidationLock(itemSaveBtn, false);
                setRuleLock(itemSaveBtn, false);
                setSubmittingState(itemSaveBtn, false);
                initialStockGroup.style.display = 'none';

                document.getElementById('itemModalTitle').innerText = 'Edit Item';
                addItemModal.style.display = 'flex';
            });
        });

        // Save Item (Create or Update)
        itemForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!validateItemForm()) {
                lockSubmitUntilFieldChange(itemSaveBtn, itemFields);
                focusFirstInvalidField(itemFields);
                return;
            }

            const id = itemIdField.value;
            const isEdit = !!id;

            const url = isEdit ? `/inventory/${id}` : "{{ route('inventory') }}";
            const method = isEdit ? 'PUT' : 'POST';

            const payload = {
                name: itemNameField.value.trim(),
                sku: itemSkuField.value.trim(),
                category_id: itemCategoryField.value,
                price: itemPriceField.value.trim(),
                stock: itemStockField.value.trim() || 0,
            };

            try {
                setSubmittingState(itemSaveBtn, true);

                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (res.ok) {
                    closeItem();
                    showSuccess(data.message);
                } else {
                    if (res.status === 422 && data.errors) {
                        if (data.errors.name?.length) setFieldError(itemNameField, data.errors.name[0]);
                        if (data.errors.sku?.length) setFieldError(itemSkuField, data.errors.sku[0]);
                        if (data.errors.category_id?.length) setFieldError(itemCategoryField, data.errors.category_id[0]);
                        if (data.errors.price?.length) setFieldError(itemPriceField, data.errors.price[0]);
                        if (data.errors.stock?.length) setFieldError(itemStockField, data.errors.stock[0]);
                        lockSubmitUntilFieldChange(itemSaveBtn, itemFields);
                        focusFirstInvalidField(itemFields);
                    }
                    showError(data.message || 'Validation failed');
                }
            } catch (err) {
                showError('System error occurred');
            } finally {
                setSubmittingState(itemSaveBtn, false);
            }
        });

        // Delete Item Logic
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const res = await fetch(`/inventory/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });
                            const data = await res.json();
                            if (res.ok) showSuccess(data.message);
                            else showError(data.message);
                        } catch (err) {
                            showError('System error');
                        }
                    }
                });
            });
        });

        // Stock In Logic
        const stockInModal = document.getElementById('stockInModal');
        const stockInForm = document.getElementById('stockInForm');
        const stockInSubmitBtn = document.getElementById('stockInSubmitBtn');
        const inItemIdField = document.getElementById('inItemId');
        const inQuantityField = document.getElementById('inQuantity');
        const inReasonField = document.getElementById('inReason');
        const stockInFields = [inQuantityField, inReasonField];

        const validateStockInForm = () => {
            clearFormErrors(stockInFields);

            const quantity = inQuantityField.value.trim();
            const reason = inReasonField.value.trim();
            let isValid = true;

            if (!quantity) {
                setFieldError(inQuantityField, 'Quantity is required.');
                isValid = false;
            } else if (!Number.isInteger(Number(quantity)) || Number(quantity) < 1) {
                setFieldError(inQuantityField, 'Quantity must be a whole number greater than or equal to 1.');
                isValid = false;
            }

            if (reason.length > 255) {
                setFieldError(inReasonField, 'Reason must not exceed 255 characters.');
                isValid = false;
            }

            return isValid;
        };

        bindClearOnInput(stockInFields, clearFieldError);

        const closeStockIn = () => {
            stockInModal.style.display = 'none';
            clearFormErrors(stockInFields);
            setValidationLock(stockInSubmitBtn, false);
            setRuleLock(stockInSubmitBtn, false);
            setSubmittingState(stockInSubmitBtn, false);
        };

        document.getElementById('closeStockIn').addEventListener('click', closeStockIn);
        document.getElementById('cancelStockIn').addEventListener('click', closeStockIn);

        document.querySelectorAll('.btn-stock-in').forEach(btn => {
            btn.addEventListener('click', () => {
                inItemIdField.value = btn.dataset.id;
                document.getElementById('inModalSku').innerText = btn.dataset.sku;
                document.getElementById('inModalCurrentStock').innerText = btn.dataset.stock;
                stockInForm.reset();
                clearFormErrors(stockInFields);
                setValidationLock(stockInSubmitBtn, false);
                setRuleLock(stockInSubmitBtn, false);
                setSubmittingState(stockInSubmitBtn, false);
                stockInModal.style.display = 'flex';
            });
        });

        stockInForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!validateStockInForm()) {
                lockSubmitUntilFieldChange(stockInSubmitBtn, stockInFields);
                focusFirstInvalidField(stockInFields);
                return;
            }

            const id = inItemIdField.value;

            try {
                setSubmittingState(stockInSubmitBtn, true);

                const res = await fetch(`/inventory/${id}/stock-in`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        quantity: inQuantityField.value.trim(),
                        reason: inReasonField.value.trim(),
                    })
                });
                const data = await res.json();
                if (res.ok) showSuccess(data.message);
                else {
                    if (res.status === 422 && data.errors) {
                        if (data.errors.quantity?.length) setFieldError(inQuantityField, data.errors.quantity[0]);
                        if (data.errors.reason?.length) setFieldError(inReasonField, data.errors.reason[0]);
                        lockSubmitUntilFieldChange(stockInSubmitBtn, stockInFields);
                        focusFirstInvalidField(stockInFields);
                    }
                    showError(data.message || 'Validation failed');
                }
            } catch (err) {
                showError('System Error');
            } finally {
                setSubmittingState(stockInSubmitBtn, false);
            }
        });

        // Stock Out Logic
        const stockOutModal = document.getElementById('stockOutModal');
        const stockOutForm = document.getElementById('stockOutForm');
        const stockOutSubmitBtn = document.getElementById('stockOutSubmitBtn');
        const outItemIdField = document.getElementById('outItemId');
        const outQuantityField = document.getElementById('outQuantity');
        const outReasonField = document.getElementById('outReason');
        const projectedBalanceDisplay = document.getElementById('projectedBalanceDisplay');
        const stockOutFields = [outQuantityField, outReasonField];
        let currentStockForOut = 0;

        const updateProjectedBalance = () => {
            const quantityValue = Number.parseInt(outQuantityField.value, 10);
            const deduction = Number.isNaN(quantityValue) ? 0 : quantityValue;
            const projected = currentStockForOut - deduction;
            const exceedsStock = !Number.isNaN(quantityValue) && quantityValue > currentStockForOut;

            projectedBalanceDisplay.textContent = `${currentStockForOut} - ${deduction} = ${projected}`;
            projectedBalanceDisplay.classList.toggle('is-negative', exceedsStock);

            if (exceedsStock) {
                setFieldError(outQuantityField, 'Deduction cannot be greater than current stock.');
            }

            setRuleLock(stockOutSubmitBtn, exceedsStock);
        };

        const validateStockOutForm = () => {
            clearFormErrors(stockOutFields);

            const quantity = outQuantityField.value.trim();
            const reason = outReasonField.value.trim();
            let isValid = true;

            if (!quantity) {
                setFieldError(outQuantityField, 'Quantity is required.');
                isValid = false;
            } else if (!Number.isInteger(Number(quantity)) || Number(quantity) < 1) {
                setFieldError(outQuantityField, 'Quantity must be a whole number greater than or equal to 1.');
                isValid = false;
            } else if (Number(quantity) > currentStockForOut) {
                setFieldError(outQuantityField, 'Deduction cannot be greater than current stock.');
                isValid = false;
            }

            if (!reason) {
                setFieldError(outReasonField, 'Stocking out items should have a reason.');
                isValid = false;
            } else if (reason.length > 255) {
                setFieldError(outReasonField, 'Reason must not exceed 255 characters.');
                isValid = false;
            }

            if (!isValid) {
                lockSubmitUntilFieldChange(stockOutSubmitBtn, stockOutFields);
            }
            return isValid;
        };

        bindClearOnInput(stockOutFields, clearFieldError);
        outQuantityField.addEventListener('input', updateProjectedBalance);

        const closeStockOut = () => {
            stockOutModal.style.display = 'none';
            clearFormErrors(stockOutFields);
            setRuleLock(stockOutSubmitBtn, false);
            setSubmittingState(stockOutSubmitBtn, false);
            projectedBalanceDisplay.textContent = '--';
            projectedBalanceDisplay.classList.remove('is-negative');
        };

        document.getElementById('closeStockOut').addEventListener('click', closeStockOut);
        document.getElementById('cancelStockOut').addEventListener('click', closeStockOut);

        document.querySelectorAll('.btn-stock-out').forEach(btn => {
            btn.addEventListener('click', () => {
                outItemIdField.value = btn.dataset.id;
                document.getElementById('outModalSku').innerText = btn.dataset.sku;
                document.getElementById('outModalCurrentStock').innerText = btn.dataset.stock;
                currentStockForOut = Number.parseInt(btn.dataset.stock, 10) || 0;
                stockOutForm.reset();
                clearFormErrors(stockOutFields);
                setRuleLock(stockOutSubmitBtn, false);
                setSubmittingState(stockOutSubmitBtn, false);
                updateProjectedBalance();
                stockOutModal.style.display = 'flex';
            });
        });

        // Search logic
        const searchInput = document.getElementById('searchInput');

        const tableBody = document.getElementById('inventoryTableBody');

        if (searchInput && tableBody) {
            searchInput.addEventListener('keyup', function() {
                const filter = searchInput.value.toLowerCase();
                const rows = tableBody.getElementsByTagName('tr');

                for (let i = 0; i < rows.length; i++) {
                    let textContent = rows[i].innerText.toLowerCase();

                    // Toggle visibility based on search match
                    if (textContent.includes(filter)) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            });
        }

        stockOutForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!validateStockOutForm()) {
                focusFirstInvalidField(stockOutFields);
                return;
            }

            const id = outItemIdField.value;

            try {
                setSubmittingState(stockOutSubmitBtn, true);

                const res = await fetch(`/inventory/${id}/stock-out`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        quantity: outQuantityField.value.trim(),
                        reason: outReasonField.value.trim(),
                    })
                });
                const data = await res.json();
                if (res.ok) showSuccess(data.message);
                else {
                    if (res.status === 422 && data.errors) {
                        if (data.errors.quantity?.length) setFieldError(outQuantityField, data.errors.quantity[0]);
                        if (data.errors.reason?.length) setFieldError(outReasonField, data.errors.reason[0]);
                        lockSubmitUntilFieldChange(stockOutSubmitBtn, stockOutFields);
                        focusFirstInvalidField(stockOutFields);
                    }
                    showError(data.message || 'Validation failed');
                }
            } catch (err) {
                showError('System Error');
            } finally {
                setSubmittingState(stockOutSubmitBtn, false);
                updateProjectedBalance();
            }
        });
    });
</script>
@endpush
