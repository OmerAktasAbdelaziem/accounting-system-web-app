@extends('layouts.modern')

@section('title', isset($commission) ? __('messages.edit_commission') : __('messages.add_commission'))

@section('content')
<style>
    :root { --primary: #a855f7; --primary-dark: #9333ea; --text-dark: #1f2937; --text-light: #6b7280; --border-light: #e5e7eb; --bg-light: #f9fafb; }
    * { transition: border-color 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease; }
    .form-page { min-height: 100vh; padding: 40px 16px; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); }
    .container-lg { max-width: 1280px; margin: 0 auto; }
    .form-grid { display: grid; grid-template-columns: 1fr; gap: 32px; align-items: start; }
    .form-sidebar { display: none; }
    .sidebar-card { background: white; border-radius: 20px; padding: 28px; box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06); border: 1px solid var(--border-light); }
    .sidebar-card:hover { box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1); }
    .sidebar-title { font-size: 12px; font-weight: 800; color: #4b5563; text-transform: uppercase; letter-spacing: 1.2px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .sidebar-stat { display: flex; justify-content: space-between; align-items: center; padding: 14px 0; border-bottom: 1px solid #f3f4f6; font-size: 13px; }
    .sidebar-stat:last-child { border-bottom: none; }
    .sidebar-stat-label { color: #9ca3af; font-weight: 600; }
    .sidebar-stat-value { font-size: 15px; font-weight: 800; color: var(--primary); }
    .form-card { background: white; border-radius: 20px; box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06); border: 1px solid var(--border-light); overflow: hidden; }
    .form-header { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; padding: 40px; display: flex; justify-content: space-between; align-items: center; gap: 20px; }
    .form-header h1 { margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.5px; }
    .form-header .btn { transition: all 0.3s ease; }
    .form-header .btn:hover { transform: translateY(-2px); box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); }
    .form-body { padding: 48px; }
    .form-section { margin-bottom: 48px; }
    .form-section:last-child { margin-bottom: 0; }
    .form-section-title { font-size: 13px; font-weight: 800; color: #4b5563; text-transform: uppercase; letter-spacing: 1.2px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #f0f0f0; display: flex; align-items: center; gap: 8px; }
    .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px; margin-bottom: 24px; }
    .form-group { display: flex; flex-direction: column; gap: 8px; }
    .form-label { font-size: 13px; font-weight: 700; color: var(--text-dark); display: flex; align-items: center; gap: 4px; }
    .form-label .required { color: #ef4444; }
    .form-control, .form-select { height: 48px; border: 1.5px solid var(--border-light); border-radius: 12px; padding: 12px 16px; font-size: 14px; font-weight: 500; color: var(--text-dark); background: white; }
    .form-control::placeholder { color: #d1d5db; }
    .form-control:hover, .form-select:hover { border-color: #d1d5db; }
    .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(168, 85, 247, 0.12); outline: none; }
    .form-control.is-invalid, .form-select.is-invalid { border-color: #ef4444; }
    .form-control.is-invalid:focus { box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.12); }
    textarea.form-control { height: auto; min-height: 120px; resize: vertical; padding: 14px 16px; line-height: 1.6; }
    .invalid-feedback { font-size: 12px; color: #ef4444; margin-top: 6px; font-weight: 500; }
    .form-footer { background: var(--bg-light); padding: 32px 48px; display: flex; gap: 16px; border-top: 1px solid var(--border-light); flex-wrap: wrap; }
    .form-footer .btn { padding: 12px 32px; font-size: 14px; font-weight: 700; border-radius: 12px; cursor: pointer; transition: all 0.3s ease; }
    .form-footer .btn-primary { background: var(--primary); border: none; color: white; }
    .form-footer .btn-primary:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 8px 16px rgba(168, 85, 247, 0.3); }
    .form-footer .btn-secondary { background: white; color: var(--text-dark); border: 1.5px solid var(--border-light); }
    .form-footer .btn-secondary:hover { border-color: var(--primary); color: var(--primary); }
    @media (max-width: 1024px) { .form-grid { grid-template-columns: 1fr; gap: 24px; } .form-sidebar { position: static; } .form-header { padding: 32px; } .form-body { padding: 32px; } .form-footer { padding: 24px 32px; } }
    @media (max-width: 640px) { .form-page { padding: 24px 12px; } .form-header { padding: 24px; flex-direction: column; text-align: center; } .form-header h1 { font-size: 24px; } .form-row { grid-template-columns: 1fr; } .form-body { padding: 24px; } .form-footer { padding: 16px 24px; gap: 12px; } .form-footer .btn { width: 100%; } }
</style>

<div class="form-page">
    <div class="container-lg">
        <div class="form-grid">
            <div class="form-sidebar">
                @if(isset($commission) && $commission)
                <div class="sidebar-card">
                    <div class="sidebar-title">📊 Summary</div>
                    <div class="sidebar-stat">
                        <span class="sidebar-stat-label">Commission</span>
                        <span class="sidebar-stat-value">${{ number_format($commission->sale_amount * ($commission->commission_rate / 100), 2) }}</span>
                    </div>
                    <div class="sidebar-stat">
                        <span class="sidebar-stat-label">Sale Amount</span>
                        <span class="sidebar-stat-value">${{ number_format($commission->sale_amount ?? 0, 2) }}</span>
                    </div>
                    <div class="sidebar-stat">
                        <span class="sidebar-stat-label">Rate</span>
                        <span class="sidebar-stat-value">{{ $commission->commission_rate ?? 0 }}%</span>
                    </div>
                </div>
                @endif
            </div>

            <div class="form-card">
                <div class="form-header">
                    <div>
                        <h1><i class="bi bi-percent" style="margin-right: 12px; font-size: 36px;"></i>{{ isset($commission) ? __('messages.edit_commission') : __('messages.add_commission') }}</h1>
                    </div>
                    <a href="{{ route('commissions.index') }}" class="btn btn-outline-light btn-sm" style="padding: 10px 20px; border-radius: 10px; border: 1.5px solid rgba(255, 255, 255, 0.4); color: white; text-decoration: none;">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>

                <form method="POST" action="{{ isset($commission) ? route('commissions.update', $commission->id) : route('commissions.store') }}">
                    @csrf
                    @if(isset($commission))
                        @method('PUT')
                    @endif

                    <div class="form-body">
                        <div class="form-section">
                            <div class="form-section-title">👤 Commission Details</div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">{{ __('messages.employee') }} <span class="required">*</span></label>
                                    <select class="form-select @error('employee_id') is-invalid @enderror" name="employee_id" required>
                                        <option value="">Select Employee</option>
                                        @foreach($employees ?? [] as $emp)
                                            <option value="{{ $emp->id }}" {{ old('employee_id', $commission->employee_id ?? '') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('messages.commission_date') }} <span class="required">*</span></label>
                                    <input type="date" class="form-control @error('commission_date') is-invalid @enderror" name="commission_date" value="{{ old('commission_date', $commission->commission_date ?? '') }}" required>
                                    @error('commission_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-section-title">💰 Financial Details</div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">{{ __('messages.sale_amount') }} <span class="required">*</span></label>
                                    <input type="text" inputmode="decimal" class="form-control @error('sale_amount') is-invalid @enderror" name="sale_amount" step="0.01" min="0" placeholder="0.00" value="{{ old('sale_amount', $commission->sale_amount ?? '') }}" required>
                                    @error('sale_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('messages.commission_rate') }} (%) <span class="required">*</span></label>
                                    <input type="text" inputmode="decimal" class="form-control @error('commission_rate') is-invalid @enderror" name="commission_rate" step="0.01" min="0" max="100" placeholder="0" value="{{ old('commission_rate', $commission->commission_rate ?? '') }}" required>
                                    @error('commission_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-section-title">📝 Additional Information</div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">{{ __('messages.reference_type') }}</label>
                                    <input type="text" class="form-control @error('reference_type') is-invalid @enderror" name="reference_type" placeholder="Enter reference type" value="{{ old('reference_type', $commission->reference_type ?? '') }}">
                                    @error('reference_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="form-row" style="margin-bottom: 0;">
                                <div class="form-group">
                                    <label class="form-label">{{ __('messages.notes') }}</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" placeholder="Enter notes">{{ old('notes', $commission->notes ?? '') }}</textarea>
                                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-section-title">🏢 Branches</div>
                            @include('branches.partials.multi-select', ['branches' => $branches ?? [], 'selectedBranchIds' => $selectedBranchIds ?? []])
                        </div>
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle" style="margin-right: 8px;"></i>{{ isset($commission) ? __('messages.update') : __('messages.save') }}
                        </button>
                        <a href="{{ route('commissions.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle" style="margin-right: 8px;"></i>{{ __('messages.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="form-page">
    <div class="container-lg">
        <div class="form-grid">
            <!-- Sidebar -->
            <div class="form-sidebar">
                @if(isset($commission) && $commission)
                <div class="sidebar-card">
                    <div class="sidebar-title">📊 Summary</div>
                    <div class="sidebar-stat">
                        <span class="sidebar-stat-label">Commission</span>
                        <span class="sidebar-stat-value">${{ number_format($commission->sale_amount * ($commission->commission_rate / 100), 2) }}</span>
                    </div>
                    <div class="sidebar-stat">
                        <span class="sidebar-stat-label">Sale Amount</span>
                        <span class="sidebar-stat-value">${{ number_format($commission->sale_amount ?? 0, 2) }}</span>
                    </div>
                    <div class="sidebar-stat">
                        <span class="sidebar-stat-label">Rate</span>
                        <span class="sidebar-stat-value">{{ $commission->commission_rate ?? 0 }}%</span>
                    </div>
                </div>
                @endif
            </div>

            <!-- Main Form -->
            <div class="form-card">
                <div class="form-header">
                    <div>
                        <h1><i class="bi bi-percent" style="margin-right: 10px;"></i>{{ isset($commission) ? __('messages.edit_commission') : __('messages.add_commission') }}</h1>
                    </div>
                    <a href="{{ route('commissions.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>

                <form method="POST" action="{{ isset($commission) ? route('commissions.update', $commission->id) : route('commissions.store') }}">
                    @csrf
                    @if(isset($commission))
                        @method('PUT')
                    @endif

                    <div class="form-body">
                        <!-- Employee & Details -->
                        <div class="form-section">
                            <div class="form-section-title">👤 Commission Details</div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">{{ __('messages.employee') }} *</label>
                                    <select class="form-select @error('employee_id') is-invalid @enderror" name="employee_id" required>
                                        <option value="">Select Employee</option>
                                        @foreach($employees ?? [] as $emp)
                                            <option value="{{ $emp->id }}" {{ old('employee_id', $commission->employee_id ?? '') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('messages.commission_date') }} *</label>
                                    <input type="date" class="form-control @error('commission_date') is-invalid @enderror" name="commission_date" value="{{ old('commission_date', $commission->commission_date ?? '') }}" required>
                                    @error('commission_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Financial -->
                        <div class="form-section">
                            <div class="form-section-title">💰 Financial Details</div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">{{ __('messages.sale_amount') }} *</label>
                                    <input type="text" inputmode="decimal" class="form-control @error('sale_amount') is-invalid @enderror" name="sale_amount" step="0.01" min="0" value="{{ old('sale_amount', $commission->sale_amount ?? '') }}" required>
                                    @error('sale_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('messages.commission_rate') }} (%) *</label>
                                    <input type="text" inputmode="decimal" class="form-control @error('commission_rate') is-invalid @enderror" name="commission_rate" step="0.01" min="0" max="100" value="{{ old('commission_rate', $commission->commission_rate ?? '') }}" required>
                                    @error('commission_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Additional -->
                        <div class="form-section">
                            <div class="form-section-title">📝 Additional Information</div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">{{ __('messages.reference_type') }}</label>
                                    <input type="text" class="form-control @error('reference_type') is-invalid @enderror" name="reference_type" value="{{ old('reference_type', $commission->reference_type ?? '') }}">
                                    @error('reference_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="form-row" style="margin-bottom: 0;">
                                <div class="form-group">
                                    <label class="form-label">{{ __('messages.notes') }}</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="4">{{ old('notes', $commission->notes ?? '') }}</textarea>
                                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Branches -->
                        <div class="form-section">
                            <div class="form-section-title">🏢 Branches</div>
                            @include('branches.partials.multi-select', ['branches' => $branches ?? [], 'selectedBranchIds' => $selectedBranchIds ?? []])
                        </div>
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> {{ isset($commission) ? __('messages.update') : __('messages.save') }}
                        </button>
                        <a href="{{ route('commissions.index') }}" class="btn btn-secondary">
                            {{ __('messages.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
