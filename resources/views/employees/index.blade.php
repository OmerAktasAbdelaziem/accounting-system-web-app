@extends('layouts.modern')

@section('title', __('messages.employees'))

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 style="font-weight: 900; color: #1a1a1a;">
            <i class="bi bi-people" style="color: #ff8c00;"></i> {{ __('messages.employees_management') }}
        </h1>
        <a href="{{ route('employees.create') }}" class="btn btn-primary-modern">
            <i class="bi bi-plus-circle"></i> {{ __('messages.add_employee') }}
        </a>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
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
                            <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-sm btn-info me-1" title="View Details">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button onclick="deleteEmployee({{ $employee->id }})" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </button>
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
