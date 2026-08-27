@extends('layouts.modern')

@section('content')
<div class="container">
    <style>
        @media (max-width: 768px) {
            .branches-hero {
                flex-direction: column;
                align-items: stretch !important;
                gap: 12px;
            }

            .branches-hero .btn {
                width: 100%;
            }

            .branches-desktop-table {
                display: none;
            }

            .branches-mobile-list {
                display: grid;
                gap: 12px;
            }

            .branch-mobile-card {
                background: rgba(255,255,255,.96);
                border: 1px solid rgba(226,232,240,.95);
                border-radius: 20px;
                padding: 14px;
                box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
            }

            .branch-mobile-card .top {
                display: flex;
                justify-content: space-between;
                gap: 10px;
                align-items: flex-start;
                margin-bottom: 10px;
            }

            .branch-mobile-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
                margin-bottom: 12px;
            }
        }

        @media (max-width: 576px) {
            .branches-hero h3 {
                font-size: 22px;
            }
        }
    </style>

    <div class="d-flex justify-content-between mb-3 branches-hero">
        <h3>{{ __('messages.branches') }}</h3>
        @feature('branches.create')
        <a href="{{ route('branches.create') }}" class="btn btn-primary">{{ __('messages.add_branch') }}</a>
        @endfeature
    </div>

    <div class="branches-mobile-list d-md-none mb-3">
        @foreach($branches as $branch)
            <div class="branch-mobile-card">
                <div class="top">
                    <div>
                        <strong>{{ $branch->name }}</strong>
                        <div class="small text-muted">{{ $branch->city }}</div>
                    </div>
                    <span class="badge bg-{{ $branch->is_active ? 'success' : 'secondary' }}">{{ $branch->is_active ? __('messages.active') : __('messages.inactive') }}</span>
                </div>
                <div class="branch-mobile-grid">
                    <div class="bg-light rounded-4 p-2"><div class="text-muted small">{{ __('Code') }}</div><strong>{{ $branch->code }}</strong></div>
                    <div class="bg-light rounded-4 p-2"><div class="text-muted small">{{ __('Manager') }}</div><strong>{{ $branch->manager_name }}</strong></div>
                </div>
                <div class="d-grid gap-2">
                    @feature('branches')
                    <a href="{{ route('branches.show', $branch) }}" class="btn btn-sm btn-outline-secondary">{{ __('messages.view') }}</a>
                    @endfeature
                    <button type="button" class="btn btn-sm btn-outline-danger" data-action="branch-debts" data-url="{{ route('branches.debts', $branch) }}">{{ __('messages.branch_debts') }}</button>
                    @feature('branches.edit')
                    <a href="{{ route('branches.edit', $branch) }}" class="btn btn-sm btn-outline-primary">{{ __('messages.edit') }}</a>
                    @endfeature
                    @feature('branches.delete')
                    <form action="{{ route('branches.destroy', $branch) }}" method="POST" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('{{ __('messages.confirm_delete') }}')">{{ __('messages.delete') }}</button>
                    </form>
                    @endfeature
                </div>
            </div>
        @endforeach
        {{ $branches->links() }}
    </div>

    <div class="card branches-desktop-table">
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
                            @feature('branches')
                            <a href="{{ route('branches.show', $branch) }}" class="btn btn-sm btn-outline-secondary">{{ __('messages.view') }}</a>
                            @endfeature

                            <button type="button" class="btn btn-sm btn-outline-danger" data-action="branch-debts" data-url="{{ route('branches.debts', $branch) }}">{{ __('messages.branch_debts') }}</button>

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

    @include('branches.partials.debts-modal')
</div>
@endsection
