@extends('layouts.modern')

@section('title', __('messages.add_employee'))

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>{{ isset($employee) ? __('messages.edit_employee') : __('messages.add_employee') }}</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
        </a>
    </div>
</div>

<div class="card">
    <form method="POST" action="{{ isset($employee) ? route('employees.update', $employee->id) : route('employees.store') }}">
        @csrf
        @if(isset($employee))
            @method('PUT')
        @endif

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">{{ __('messages.name') }} *</label>
                        <input 
                            type="text" 
                            class="form-control @error('name') is-invalid @enderror" 
                            id="name" 
                            name="name" 
                            value="{{ old('name', $employee->name ?? '') }}"
                            required
                        >
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="position" class="form-label">{{ __('messages.position') }} *</label>
                        <input 
                            type="text" 
                            class="form-control @error('position') is-invalid @enderror" 
                            id="position" 
                            name="position" 
                            value="{{ old('position', $employee->position ?? '') }}"
                            required
                        >
                        @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="base_salary" class="form-label">{{ __('messages.salary') }} *</label>
                        <input 
                            type="number" 
                            class="form-control @error('base_salary') is-invalid @enderror" 
                            id="base_salary" 
                            name="base_salary" 
                            step="0.01"
                            value="{{ old('base_salary', $employee->base_salary ?? '') }}"
                            required
                        >
                        @error('base_salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="form-check mb-3">
                <input 
                    type="checkbox" 
                    class="form-check-input" 
                    id="is_active" 
                    name="is_active" 
                    value="1"
                    {{ old('is_active', isset($employee) && $employee ? $employee->is_active : true) ? 'checked' : '' }}
                >
                <label class="form-check-label" for="is_active">
                    {{ __('messages.is_active') }}
                </label>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> {{ __('messages.save') }}
            </button>
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                {{ __('messages.cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection
