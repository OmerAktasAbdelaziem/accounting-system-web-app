@extends('layouts.modern')

@section('title', __('messages.employees'))

@section('content')
<style>
    @media (max-width: 768px) {
        .employees-hero {
            flex-direction: column;
            align-items: stretch !important;
            gap: 12px;
        }

        .employees-hero .btn {
            width: 100%;
        }

        .employees-desktop-table {
            display: none;
        }

        .employees-mobile-list {
            display: grid;
            gap: 12px;
        }

        .employee-mobile-card {
            background: rgba(255,255,255,.96);
            border: 1px solid rgba(226,232,240,.95);
            border-radius: 20px;
            padding: 14px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        }

        .employee-mobile-card .top {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .employee-mobile-card .meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 12px;
        }

        .btn-group {
            flex-wrap: wrap;
            gap: 6px;
        }

        .btn-group .btn {
            flex: 1 1 auto;
        }
    }

    @media (max-width: 576px) {
        .employees-hero h1 {
            font-size: 22px;
        }
    }
</style>

<style>
    @media (max-width: 768px) {
        .employees-mobile-list { display: block; }
        .table { display: none !important; }
    }
</style>
</style>

<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center employees-hero">
        <h1 style="font-weight: 900; color: #1a1a1a;">
            <i class="bi bi-people" style="color: #ff8c00;"></i> {{ __('messages.employees_management') }}
        </h1>
        @feature('employees.create')
            <a href="{{ route('employees.create') }}" class="btn btn-primary-modern">
                <i class="bi bi-plus-circle"></i> {{ __('messages.add_employee') }}
            </a>
        @endfeature
    </div>
</div>

<div class="card">
    <!-- Mobile employees cards -->
    <div class="employees-mobile-list d-md-none">
        @forelse($employees ?? [] as $employee)
            <div class="employee-mobile-card">
                <div class="top">
                        <div>
                            <strong>{{ $employee->name }}</strong>
                            <div class="small text-muted">{{ $employee->position }}</div>
                        </div>
                    <div class="text-end fw-bold">{{ $currencySymbol }}{{ number_format($employee->base_salary, 2) }}</div>
                    </div>
                <div class="meta">
                    <div class="bg-light rounded-4 p-2">
                        <div class="text-muted small">{{ __('messages.position') }}</div>
                        <strong>{{ $employee->position }}</strong>
                    </div>
                    <div class="bg-light rounded-4 p-2">
                        <div class="text-muted small">{{ __('messages.salary') }}</div>
                        <strong>{{ $currencySymbol }}{{ number_format($employee->base_salary, 2) }}</strong>
                    </div>
                </div>
                <div class="d-grid gap-2">
                        @feature('employees.view')
                            <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-sm btn-info">{{ __('messages.view') }}</a>
                        @endfeature
                        @feature('employees.edit')
                            <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-sm btn-warning">{{ __('messages.edit') }}</a>
                        @endfeature
                        @feature('employees.delete')
                            <button onclick="deleteEmployee({{ $employee->id }})" class="btn btn-sm btn-danger">{{ __('messages.delete') }}</button>
                        @endfeature
                </div>
            </div>
        @empty
            <div class="employee-mobile-card text-center text-muted">{{ __('messages.no_data') }}</div>
        @endforelse
    </div>

    <div class="table-responsive employees-desktop-table">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.position') }}</th>
                    <th>{{ __('messages.salary') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees ?? [] as $employee)
                    <tr>
                        <td><strong>{{ $employee->name }}</strong></td>
                        <td>{{ $employee->position }}</td>
                        <td>{{ $currencySymbol }}{{ number_format($employee->base_salary, 2) }}</td>
                        <td>
                            @feature('employees.view')
                                <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-sm btn-info me-1" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                            @endfeature

                            @feature('employees.edit')
                                <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endfeature

                            @feature('employees.delete')
                                <button onclick="deleteEmployee({{ $employee->id }})" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endfeature
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">{{ __('messages.no_data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($employees ?? false)
    <div class="mt-3">
        {{ $employees->links() }}
    </div>
@endif
@endsection

@section('js')
<script>
    function deleteEmployee(id) {
        if (confirm('{{ __("messages.confirm_delete") }}')) {
            const url = '{{ url("employees") }}/' + id;
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }
</script>
@endsection
