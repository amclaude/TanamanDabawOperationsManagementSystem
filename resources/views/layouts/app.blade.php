<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tanaman')</title>

    <script>
        // Apply persisted sidebar-collapsed state before styles render to avoid layout jump
        (function(){
            try {
                if (localStorage.getItem('sidebarCollapsed') === '1') {
                    document.documentElement.setAttribute('data-sidebar-collapsed', '1');
                }
            } catch (e) {}
        })();
    </script>

    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')
</head>

<body class="dashboard-body">

    @include('partials.navbar')

    <div class="dashboard-container">
        @if(in_array(auth()->user()->role, ['Admin', 'Operations Manager']))
            @include('partials.sidebar')
        @endif
        <main class="main-content" style="padding: 30px 25px;">
            @yield('content')
        </main>
    </div>

    @include('partials.footer')
    <!-- Global front-end input validation: ensures phone numbers are digits-only, quantities/stock are non-negative integers, and prices/budgets are non-negative decimals -->
    <script>
        (function() {
            const fieldButtonMap = new WeakMap();

            const isLockedByValidation = (button) => button?.dataset?.lockedByValidation === '1';
            const isLockedByRule = (button) => button?.dataset?.lockedByRule === '1';
            const isSubmitting = (button) => button?.dataset?.submitting === '1';

            const syncSubmitState = (button) => {
                if (!button) {
                    return;
                }

                button.disabled = isSubmitting(button) || isLockedByValidation(button) || isLockedByRule(button);
            };

            const setSubmittingState = (button, submitting, label = 'Processing...') => {
                if (!button) {
                    return;
                }

                if (!button.dataset.originalText) {
                    button.dataset.originalText = button.textContent.trim();
                }

                button.dataset.submitting = submitting ? '1' : '0';
                button.textContent = submitting ? label : button.dataset.originalText;
                syncSubmitState(button);
            };

            const unlockSubmitValidation = (button) => {
                if (!button) {
                    return;
                }

                button.dataset.lockedByValidation = '0';
                syncSubmitState(button);
            };

            const setValidationLock = (button, locked) => {
                if (!button) {
                    return;
                }

                button.dataset.lockedByValidation = locked ? '1' : '0';
                syncSubmitState(button);
            };

            const setRuleLock = (button, locked) => {
                if (!button) {
                    return;
                }

                button.dataset.lockedByRule = locked ? '1' : '0';
                syncSubmitState(button);
            };

            const lockSubmitUntilFieldChange = (button, fields) => {
                if (!button || !Array.isArray(fields)) {
                    return;
                }

                button.dataset.lockedByValidation = '1';
                syncSubmitState(button);

                fields.forEach((field) => {
                    if (!field) {
                        return;
                    }

                    if (!fieldButtonMap.has(field)) {
                        fieldButtonMap.set(field, new Set());

                        const unlockAll = () => {
                            const buttons = fieldButtonMap.get(field);
                            if (!buttons) {
                                return;
                            }

                            buttons.forEach((trackedButton) => unlockSubmitValidation(trackedButton));
                        };

                        field.addEventListener('input', unlockAll);
                        if (field.tagName === 'SELECT') {
                            field.addEventListener('change', unlockAll);
                        }
                    }

                    fieldButtonMap.get(field).add(button);
                });
            };

            const setFieldError = (field, message) => {
                if (!field) {
                    return;
                }

                field.classList.add('input-invalid');
                const errorTargetId = field.dataset.errorTarget;
                if (!errorTargetId) {
                    return;
                }

                const errorElement = document.getElementById(errorTargetId);
                if (errorElement) {
                    errorElement.textContent = message;
                }
            };

            const clearFieldError = (field) => {
                if (!field) {
                    return;
                }

                field.classList.remove('input-invalid');
                const errorTargetId = field.dataset.errorTarget;
                if (!errorTargetId) {
                    return;
                }

                const errorElement = document.getElementById(errorTargetId);
                if (errorElement) {
                    errorElement.textContent = '';
                }
            };

            const clearFormErrors = (fields) => {
                if (!Array.isArray(fields)) {
                    return;
                }

                fields.forEach((field) => clearFieldError(field));
            };

            const focusFirstInvalidField = (fields) => {
                if (!Array.isArray(fields)) {
                    return;
                }

                const invalidField = fields.find((field) => field && field.classList.contains('input-invalid'));
                if (invalidField) {
                    invalidField.focus();
                }
            };

            const bindClearOnInput = (fields, clearCallback) => {
                if (!Array.isArray(fields)) {
                    return;
                }

                fields.forEach((field) => {
                    if (!field) {
                        return;
                    }

                    const clearFn = typeof clearCallback === 'function' ? clearCallback : clearFieldError;
                    field.addEventListener('input', () => clearFn(field));
                    if (field.tagName === 'SELECT') {
                        field.addEventListener('change', () => clearFn(field));
                    }
                });
            };

            window.TanamanValidation = {
                bindClearOnInput,
                clearFieldError,
                clearFormErrors,
                focusFirstInvalidField,
                lockSubmitUntilFieldChange,
                setFieldError,
                setValidationLock,
                setRuleLock,
                setSubmittingState,
                syncSubmitState,
            };
        })();

        document.addEventListener('input', function(e) {
            const el = e.target;
            if (!el || el.tagName !== 'INPUT') return;

            // Phone inputs: digits only (no letters, no negatives)
            if (el.matches('input[type="tel"], #c_phone, .phone-input')) {
                const pos = el.selectionStart;
                el.value = (el.value || '').replace(/\D+/g, '');
                try { el.setSelectionRange(pos, pos); } catch (err) {}
                return;
            }

            // Integer inputs (quantities, stock): remove non-digits and disallow negative
            if (el.matches('#itemStock, #inQuantity, #outQuantity, .item-qty, .i-qty, input.integer')) {
                const pos = el.selectionStart;
                el.value = (el.value || '').replace(/\D+/g, '');
                try { el.setSelectionRange(pos, pos); } catch (err) {}
                return;
            }

            // Positive decimal inputs (prices, budgets): allow numbers and single dot, no negative
            if (el.matches('#itemPrice, #p_budget, .item-price, .i-price, input.positive')) {
                let v = el.value || '';
                v = v.replace(/[^0-9.]+/g, '');
                const parts = v.split('.');
                if (parts.length > 2) v = parts[0] + '.' + parts.slice(1).join('');
                el.value = v;
                return;
            }
        });

        // Disable hover on empty tables (tables with only "No data" message)
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.data-table').forEach(table => {
                const tbody = table.querySelector('tbody');
                if (tbody) {
                    const rows = tbody.querySelectorAll('tr');
                    // Check if table only has "empty" state row (single row with colspan)
                    if (rows.length === 1 && rows[0].querySelector('td[colspan]')) {
                        table.classList.add('empty-table');
                    }
                }
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
