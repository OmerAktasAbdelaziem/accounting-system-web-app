@extends('layouts.modern')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h3>{{ __('messages.branches') }}</h3>
        <a href="{{ route('branches.create') }}" class="btn btn-primary">{{ __('messages.add_branch') }}</a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('branches.index') }}" class="row g-2 align-items-end mb-3">
                <div class="col-md-8">
                    <label for="branch-search" class="form-label">Search</label>
                    <input id="branch-search" type="text" name="q" value="{{ $search ?? request('q') }}" class="form-control" placeholder="Search by name, city or manager">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <a href="{{ route('branches.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card" id="branches-list-container">
        <div class="card-body">
            <table class="table table-striped">
                <thead class="bg-light text-dark">
                    <tr>
                        <th>#</th>
                        <th>{{ __('messages.branch_name') }}</th>
                        <th>{{ __('messages.branch_code') }}</th>
                        <th>{{ __('messages.city') }}</th>
                        <th>{{ __('messages.manager_name') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($branches as $branch)
                    <tr>
                        <td>{{ $branch->id }}</td>
                        <td>{{ $branch->name }}</td>
                        <td>{{ $branch->code }}</td>
                        <td>{{ $branch->city }}</td>
                        <td>{{ $branch->manager_name }}</td>
                        <td>
                            <span class="badge bg-{{ $branch->is_active ? 'success' : 'secondary' }}">
                                {{ $branch->is_active ? __('messages.active') : __('messages.inactive') }}
                            </span>
                        </td>
                        <td class="action-buttons">
                            <a href="{{ route('branches.show', $branch) }}" class="btn btn-sm btn-outline-secondary">{{ __('messages.view') }}</a>
                            <a href="{{ route('branches.edit', $branch) }}" class="btn btn-sm btn-outline-primary">{{ __('messages.edit') }}</a>
                            <form action="{{ route('branches.destroy', $branch) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('messages.confirm_delete') }}')">{{ __('messages.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $branches->links() }}
        </div>
    </div>
</div>

@include('components.ajax-list')
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    initAjaxList({ containerId: 'branches-list-container', searchSelector: '#branch-search', searchParam: 'q', debounceMs: 300 });
});
</script>
@endpush
@endsection
