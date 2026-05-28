@extends('layouts.super-admin')

@section('title', 'Feature Access Control')

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="page-title">
                <i class="bi bi-toggles2"></i>
                Feature Access Control
            </h1>
            <p class="page-subtitle">Manage feature availability per role and merchant</p>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-orange alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div id="featureAccessAlert" class="alert d-none alert-dismissible fade show" role="alert">
    <span class="feature-access-alert-message"></span>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<!-- Merchant Selection -->
<div class="form-section mb-4">
    <h5 class="mb-3">Select Merchant</h5>
    <div class="row g-3">
        <div class="col-md-8">
            <form id="merchantForm" method="GET" action="{{ route('super-admin.feature-access.index') }}">
                <select name="merchant_id" id="merchantSelect" class="form-select" required onchange="document.getElementById('merchantForm').submit();">
                    <option value="">-- Choose a Merchant --</option>
                    @foreach($merchants as $merchant)
                    <option value="{{ $merchant->id }}" {{ request('merchant_id') == $merchant->id ? 'selected' : '' }}>
                        {{ $merchant->name }}
                    </option>
                    @endforeach
                </select>
            </form>
        </div>
        @if($selectedMerchant)
        <div class="col-md-4 d-flex gap-2">
            <form action="{{ route('super-admin.feature-access.reset') }}" method="POST" class="feature-access-reset-form" style="display:inline;">
                @csrf
                <input type="hidden" name="merchant_id" value="{{ $selectedMerchant->id }}">
                <button type="submit" class="btn btn-outline-warning btn-sm" onclick="return confirm('Reset all features to package defaults?')">
                    <i class="icon icon-refresh-cw"></i> Reset to Package Defaults
                </button>
            </form>
            <a href="{{ route('super-admin.merchants.show', $selectedMerchant) }}" class="btn btn-outline-info btn-sm">
                <i class="icon icon-external-link"></i> View Merchant
            </a>
        </div>
        @endif
    </div>
</div>

    @if($selectedMerchant && $rows)
    <div class="card">
        <div class="card-header bg-light">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">{{ $selectedMerchant->name }} - Feature Access Matrix</h5>
                </div>
                <div class="col-auto">
                    @php
                        $activeSubscription = $selectedMerchant->subscription()->where('is_active', true)->first();
                        $packageName = $activeSubscription?->package?->name ?? 'None';
                    @endphp
                    <small class="text-muted">Current Subscription: {{ $packageName }}</small>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0" style="table-layout: fixed;">
                <thead class="table-light sticky-top">
                    <tr>
                        <th style="width: 200px;">Role / Feature</th>
                        @foreach($roles as $role)
                        <th class="text-center" style="width: 120px;">
                            <div class="text-truncate" title="{{ $role->name }}">{{ $role->name }}</div>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $feature)
                    <tr>
                        <td class="fw-bold bg-light">
                            {{ ucfirst(str_replace('_', ' ', $feature)) }}
                        </td>
                        @foreach($roles as $role)
                        <td class="text-center">
                            @php
                                $access = $featureAccess[$feature][$role->id] ?? false;
                                $canEdit = true; // Check authorization if needed
                            @endphp
                            <form method="POST" action="{{ route('super-admin.feature-access.update') }}" class="feature-access-toggle-form" data-role-id="{{ $role->id }}" data-feature-key="{{ $feature }}" style="display:inline;">
                                @csrf
                                <input type="hidden" name="merchant_id" value="{{ $selectedMerchant->id }}">
                                <input type="hidden" name="role_id" value="{{ $role->id }}">
                                <input type="hidden" name="feature" value="{{ $feature }}">
                                <input type="hidden" name="action" value="{{ $access ? 'disable' : 'enable' }}">

                                <button type="submit"
                                        class="btn btn-sm {{ $access ? 'btn-success' : 'btn-outline-secondary' }} toggle-btn"
                                    data-enabled-label="Enabled"
                                    data-disabled-label="Disabled"
                                        data-enabled-class="btn btn-sm btn-success toggle-btn"
                                        data-disabled-class="btn btn-sm btn-outline-secondary toggle-btn"
                                        title="Click to toggle"
                                        {{ !$canEdit ? 'disabled' : '' }}>
                                    @if($access)
                                    <i class="icon icon-check"></i> Enabled
                                    @else
                                    <i class="icon icon-x"></i> Disabled
                                    @endif
                                </button>
                            </form>
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-light">
            <div class="row align-items-center">
                <div class="col">
                    <strong>Legend:</strong>
                    <span class="badge bg-success ms-2">Enabled</span>
                    <span class="badge bg-secondary ms-2">Disabled</span>
                </div>
                <div class="col-auto">
                    <small class="text-muted">
                        Matrix updated: {{ now()->format('M d, Y H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Merchant Employees</h5>
            <small class="text-muted">Special access overrides for individual employees</small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Special Access</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        @php
                            $linkedUser = $employeeUserMap[$employee->email] ?? null;
                            $employeeGrantedFeatureKeys = $linkedUser ? ($employeeOverrides[$linkedUser->id] ?? collect())->where('is_enabled', true)->pluck('feature_key')->toArray() : [];
                            $employeeDeniedFeatureKeys = $linkedUser ? ($employeeOverrides[$linkedUser->id] ?? collect())->where('is_enabled', false)->pluck('feature_key')->toArray() : [];
                        @endphp
                        <tr data-employee-row="{{ $linkedUser?->id ?? '' }}">
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->email }}</td>
                            <td>{{ $linkedUser?->role?->name ?? $employee->position ?? '-' }}</td>
                            <td class="employee-special-access-cell">
                                @if(!empty($employeeGrantedFeatureKeys) || !empty($employeeDeniedFeatureKeys))
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($employeeGrantedFeatureKeys as $featureKey)
                                            <span class="badge bg-info text-dark">{{ ucfirst(str_replace('_', ' ', $featureKey)) }}</span>
                                        @endforeach
                                        @foreach($employeeDeniedFeatureKeys as $featureKey)
                                            <span class="badge bg-danger">Denied: {{ ucfirst(str_replace('_', ' ', $featureKey)) }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">No special access</span>
                                @endif
                            </td>
                            <td>
                                @if($linkedUser)
                                    <button type="button"
                                            class="btn btn-sm btn-outline-orange edit-employee-access-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#employeeAccessModal"
                                            data-employee-id="{{ $linkedUser->id }}"
                                            data-employee-name="{{ $employee->name }}"
                                            data-granted-feature-keys='@json($employeeGrantedFeatureKeys)'
                                            data-denied-feature-keys='@json($employeeDeniedFeatureKeys)'>
                                        Manage Access
                                    </button>
                                @else
                                    <button type="button"
                                            class="btn btn-sm btn-outline-success create-employee-login-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#employeeLoginModal"
                                            data-employee-id="{{ $employee->id }}"
                                            data-employee-name="{{ $employee->name }}"
                                            data-employee-email="{{ $employee->email }}">
                                        Create Login User
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No employees found for this merchant</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header bg-light"><h5 class="mb-0">Feature Definitions</h5></div>
        <div class="card-body">
            <div class="alert alert-warning py-2">
                <strong>Note:</strong> employee-specific denies override role access for the selected pages.
            </div>
            <div class="row">
                <div class="col-md-6">
                    <ul class="mb-0">
                        <li><strong>invoicing</strong> - Create and manage invoices</li>
                        <li><strong>payroll</strong> - Employee payroll management</li>
                        <li><strong>inventory</strong> - Inventory tracking and management</li>
                        <li><strong>basic_reporting</strong> - Basic financial reports</li>
                        <li><strong>advanced_reporting</strong> - Advanced analytics and custom reports</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="mb-0">
                        <li><strong>multi_branch</strong> - Support for multiple branches</li>
                        <li><strong>api_access</strong> - REST API access</li>
                        <li><strong>custom_integration</strong> - Third-party integrations</li>
                        <li><strong>dedicated_support</strong> - Priority support</li>
                        <li><strong>backup_restore</strong> - Automated backups</li>
                        <li><strong>live_chat_floating</strong> - Live chat floating launcher (show/hide floating chat widget)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @else
    <div class="alert alert-info">
        <strong>Select a merchant to view their feature access matrix</strong>
    </div>
    @endif
</div>

<div class="modal fade" id="employeeAccessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('super-admin.feature-access.employee.update') }}" id="employeeAccessForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Manage Employee Special Access</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="merchant_id" value="{{ $selectedMerchant?->id }}">
                    <input type="hidden" name="user_id" id="employeeAccessUserId">
                    <div class="mb-3">
                        <strong id="employeeAccessName">Employee</strong>
                        <div class="text-muted small">Select the pages this employee should access in addition to their role access.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Action</label>
                        <select name="decision" id="employeeAccessDecision" class="form-select" required>
                            <option value="grant">Grant selected pages</option>
                            <option value="deny">Deny selected pages</option>
                        </select>
                    </div>

                    <div class="row">
                        @foreach($availableFeatures as $featureKey => $featureLabel)
                            <div class="col-md-6 mb-2">
                                <div class="form-check border rounded p-2">
                                    <input class="form-check-input employee-feature-checkbox" type="checkbox" name="features[]" value="{{ $featureKey }}" id="employee_feature_{{ $featureKey }}">
                                    <label class="form-check-label" for="employee_feature_{{ $featureKey }}">
                                        <strong>{{ $featureLabel }}</strong>
                                        <div class="small text-muted">{{ $featureKey }}</div>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-orange">Save Access</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="employeeLoginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('super-admin.feature-access.employee.login') }}" id="employeeLoginForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create Login User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="merchant_id" value="{{ $selectedMerchant?->id }}">
                    <input type="hidden" name="employee_id" id="employeeLoginEmployeeId">
                    <div class="mb-3">
                        <strong id="employeeLoginName">Employee</strong>
                        <div class="text-muted small">This will create a login account for the selected employee.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="employeeLoginEmail" required>
                        <small class="text-muted">This email will also be saved on the employee record so the account can be linked later.</small>
                        <div id="employeeEmailWarning" class="small text-danger mt-1 d-none">This email already exists in the system.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control" id="employeeLoginPassword" required>
                            <button type="button" class="btn btn-outline-secondary" id="toggleEmployeePassword">Show</button>
                        </div>
                        <small class="text-muted">Enter the password manually for the new login account.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" class="form-control" id="employeeLoginPasswordConfirmation" required>
                            <button type="button" class="btn btn-outline-secondary" id="toggleEmployeePasswordConfirmation">Show</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Create Login User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .toggle-btn {
        transition: all 0.3s ease;
        min-width: 100px;
    }
    .toggle-btn:hover {
        transform: scale(1.05);
    }
    .table-responsive {
        max-height: 70vh;
        overflow: auto;
    }
    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const alertBox = document.getElementById('featureAccessAlert');
    const alertMessage = alertBox ? alertBox.querySelector('.feature-access-alert-message') : null;
    const featureLabels = @json($availableFeatures ?? []);

    function showAlert(message, type) {
        if (!alertBox || !alertMessage) {
            return;
        }

        alertBox.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');
        alertBox.classList.add(type === 'success' ? 'alert-success' : 'alert-danger');
        alertMessage.textContent = message;
    }

    function getCsrfToken(form) {
        const tokenInput = form.querySelector('input[name="_token"]');
        return tokenInput ? tokenInput.value : '';
    }

    function getFormActionUrl(form) {
        return form.getAttribute('action') || '';
    }

    function renderFeatureBadge(featureKey, type) {
        const label = featureLabels[featureKey] || featureKey.replace(/_/g, ' ');
        const safeLabel = label.charAt(0).toUpperCase() + label.slice(1);

        if (type === 'deny') {
            return '<span class="badge bg-danger">Denied: ' + safeLabel + '</span>';
        }

        return '<span class="badge bg-info text-dark">' + safeLabel + '</span>';
    }

    function renderToggleButton(enabled, button) {
        const label = enabled ? (button?.dataset.enabledLabel || 'Enabled') : (button?.dataset.disabledLabel || 'Disabled');
        const icon = enabled ? 'icon-check' : 'icon-x';

        return '<i class="icon ' + icon + '"></i> ' + label;
    }

    function updateMatrixButtonState(button, enabled) {
        if (!button) return;

        button.className = enabled ? button.dataset.enabledClass : button.dataset.disabledClass;
        button.innerHTML = renderToggleButton(enabled, button);

        const form = button.closest('form');
        const actionInput = form ? form.querySelector('input[name="action"]') : null;
        if (actionInput) {
            actionInput.value = enabled ? 'disable' : 'enable';
        }
    }

    function updateMatrixFromReset(payload) {
        const enabledRoles = Array.isArray(payload.enabled_role_ids) ? payload.enabled_role_ids.map(String) : [];
        const packageFeatures = Array.isArray(payload.package_features) ? payload.package_features : [];

        document.querySelectorAll('.feature-access-toggle-form').forEach(function (form) {
            const button = form.querySelector('.toggle-btn');
            const roleId = String(form.dataset.roleId || '');
            const featureKey = form.dataset.featureKey || '';
            const enabled = enabledRoles.includes(roleId) && packageFeatures.includes(featureKey);
            updateMatrixButtonState(button, enabled);
        });
    }

    function updateEmployeeRow(userId, decision, features) {
        const row = document.querySelector('[data-employee-row="' + userId + '"]');
        if (!row) return;

        const cell = row.querySelector('.employee-special-access-cell');
        if (!cell) return;

        if (!features || !features.length) {
            cell.innerHTML = '<span class="text-muted">No special access</span>';
            return;
        }

        const badges = features.map(function (featureKey) {
            return renderFeatureBadge(featureKey, decision);
        }).join('');

        cell.innerHTML = '<div class="d-flex flex-wrap gap-1">' + badges + '</div>';
    }

    document.querySelectorAll('.feature-access-toggle-form').forEach(function (form) {
        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            const button = form.querySelector('.toggle-btn');
            if (!button || button.disabled) {
                return;
            }

            const actionInput = form.querySelector('input[name="action"]');
            const originalHtml = button.innerHTML;
            const originalClassName = button.className;

            button.disabled = true;
            button.innerHTML = '<i class="icon icon-loader"></i> Updating...';

            try {
                const response = await fetch(form.action, {
                const response = await fetch(getFormActionUrl(form), {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(form),
                    },
                    body: new FormData(form),
                });

                const payload = await response.json().catch(() => ({}));

                if (!response.ok || !payload.success) {
                    throw new Error(payload.message || 'Unable to update feature access.');
                }

                updateMatrixButtonState(button, !!payload.enabled);
                showAlert(payload.message || 'Feature access updated', 'success');
            } catch (error) {
                button.className = originalClassName;
                button.innerHTML = originalHtml;
                showAlert(error.message || 'Unable to update feature access.', 'danger');
            } finally {
                button.disabled = false;
            }
        });
    });

    document.querySelectorAll('.feature-access-reset-form').forEach(function (form) {
        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            if (!confirm('Reset all features to package defaults?')) {
                return;
            }

            const button = form.querySelector('button[type="submit"]');
            const originalHtml = button ? button.innerHTML : '';

            if (button) {
                button.disabled = true;
                button.innerHTML = '<i class="icon icon-loader"></i> Resetting...';
            }

            try {
                const response = await fetch(getFormActionUrl(form), {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(form),
                    },
                    body: new FormData(form),
                });

                const payload = await response.json().catch(() => ({}));

                if (!response.ok || !payload.success) {
                    throw new Error(payload.message || 'Unable to reset feature access.');
                }

                updateMatrixFromReset(payload);
                showAlert(payload.message || 'Feature access reset to package defaults', 'success');
            } catch (error) {
                showAlert(error.message || 'Unable to reset feature access.', 'danger');
            } finally {
                if (button) {
                    button.disabled = false;
                    button.innerHTML = originalHtml;
                }
            }
        });
    });

    const employeeAccessModal = document.getElementById('employeeAccessModal');
    if (employeeAccessModal) {
        employeeAccessModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) {
                return;
            }

            const employeeId = button.getAttribute('data-employee-id');
            const employeeName = button.getAttribute('data-employee-name');
            const grantedFeatureKeys = JSON.parse(button.getAttribute('data-granted-feature-keys') || '[]');
            const deniedFeatureKeys = JSON.parse(button.getAttribute('data-denied-feature-keys') || '[]');

            document.getElementById('employeeAccessUserId').value = employeeId;
            document.getElementById('employeeAccessName').textContent = employeeName;

            const decisionSelect = document.getElementById('employeeAccessDecision');
            decisionSelect.value = deniedFeatureKeys.length > 0 && grantedFeatureKeys.length === 0 ? 'deny' : 'grant';

            document.querySelectorAll('.employee-feature-checkbox').forEach(function (checkbox) {
                checkbox.checked = (decisionSelect.value === 'grant' ? grantedFeatureKeys : deniedFeatureKeys).includes(checkbox.value);
            });

            decisionSelect.addEventListener('change', function () {
                const activeKeys = this.value === 'grant' ? grantedFeatureKeys : deniedFeatureKeys;
                document.querySelectorAll('.employee-feature-checkbox').forEach(function (checkbox) {
                    checkbox.checked = activeKeys.includes(checkbox.value);
                });
            }, { once: true });
        });
    }

    const employeeAccessForm = document.getElementById('employeeAccessForm');
    if (employeeAccessForm) {
        employeeAccessForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            const button = employeeAccessForm.querySelector('button[type="submit"]');
            const originalHtml = button ? button.innerHTML : '';

            if (button) {
                button.disabled = true;
                button.innerHTML = '<i class="icon icon-loader"></i> Saving...';
            }

            try {
                const response = await fetch(employeeAccessForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(employeeAccessForm),
                    },
                    body: new FormData(employeeAccessForm),
                });

                const payload = await response.json().catch(() => ({}));

                if (!response.ok || !payload.success) {
                    throw new Error(payload.message || 'Unable to save employee access.');
                }

                updateEmployeeRow(payload.user_id, payload.decision, payload.features);
                showAlert(payload.message || 'Employee access updated', 'success');

                const modalElement = document.getElementById('employeeAccessModal');
                const modalInstance = modalElement && window.bootstrap ? bootstrap.Modal.getInstance(modalElement) : null;
                if (modalInstance) {
                    modalInstance.hide();
                }
            } catch (error) {
                showAlert(error.message || 'Unable to save employee access.', 'danger');
            } finally {
                if (button) {
                    button.disabled = false;
                    button.innerHTML = originalHtml;
                }
            }
        });
    }

    const employeeLoginModal = document.getElementById('employeeLoginModal');
    if (employeeLoginModal) {
        employeeLoginModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) {
                return;
            }

            document.getElementById('employeeLoginEmployeeId').value = button.getAttribute('data-employee-id');
            document.getElementById('employeeLoginName').textContent = button.getAttribute('data-employee-name');
            document.getElementById('employeeLoginEmail').value = button.getAttribute('data-employee-email') || '';
            document.getElementById('employeeEmailWarning').classList.add('d-none');
            document.getElementById('employeeLoginPassword').value = '';
            document.getElementById('employeeLoginPasswordConfirmation').value = '';
        });
    }

    const passwordInput = document.getElementById('employeeLoginPassword');
    const passwordConfirmationInput = document.getElementById('employeeLoginPasswordConfirmation');
    const emailInput = document.getElementById('employeeLoginEmail');
    const emailWarning = document.getElementById('employeeEmailWarning');
    const togglePassword = document.getElementById('toggleEmployeePassword');
    const togglePasswordConfirmation = document.getElementById('toggleEmployeePasswordConfirmation');
    const existingEmails = @json($existingEmails ?? []);

    function updateEmailWarning() {
        if (!emailInput || !emailWarning) return;
        const current = (emailInput.value || '').trim().toLowerCase();
        const exists = existingEmails.map(email => String(email).toLowerCase()).includes(current);
        emailWarning.classList.toggle('d-none', !exists || !current);
    }

    if (emailInput) {
        emailInput.addEventListener('input', updateEmailWarning);
        emailInput.addEventListener('blur', updateEmailWarning);
    }

    function toggleVisibility(input) {
        if (!input) return;
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            toggleVisibility(passwordInput);
            this.textContent = passwordInput.type === 'password' ? 'Show' : 'Hide';
        });
    }

    if (togglePasswordConfirmation && passwordConfirmationInput) {
        togglePasswordConfirmation.addEventListener('click', function () {
            toggleVisibility(passwordConfirmationInput);
            this.textContent = passwordConfirmationInput.type === 'password' ? 'Show' : 'Hide';
        });
    }
});
</script>
@endsection
