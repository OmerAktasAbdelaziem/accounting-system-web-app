@extends('layouts.modern')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h3>{{ __('messages.branches') }}</h3>
        @feature('branches.create')
        <a href="{{ route('branches.create') }}" class="btn btn-primary">{{ __('messages.add_branch') }}</a>
        @endfeature
    </div>

    <div class="card">
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
                            @feature('branches.view')
                            <a href="{{ route('branches.show', $branch) }}" class="btn btn-sm btn-outline-secondary">{{ __('messages.view') }}</a>
                            @endfeature

                            @feature('branches.edit')
                            <a href="{{ route('branches.edit', $branch) }}" class="btn btn-sm btn-outline-primary">{{ __('messages.edit') }}</a>
                            @endfeature

                            @feature('branches.delete')
                            <form action="{{ route('branches.destroy', $branch) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('messages.confirm_delete') }}')">{{ __('messages.delete') }}</button>
                            </form>
                            @endfeature
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $branches->links() }}
        </div>
    </div>
</div>
@endsection
