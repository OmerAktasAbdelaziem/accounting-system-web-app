@extends('layouts.super-admin')

@section('title', 'Unassigned Users')

@section('content')
<div class="page-header">
    <div class="merchant-header-row d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="page-title">
                <i class="bi bi-people"></i>
                Users Without Merchant
            </h1>
            <p class="page-subtitle">Assign existing users to merchants</p>
        </div>
        <a href="{{ route('super-admin.merchants.index') }}" class="btn btn-outline-orange">
            <i class="bi bi-arrow-left"></i> Back to Merchants
        </a>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        .merchant-header-row {
            flex-direction: column;
            align-items: stretch !important;
            gap: 12px;
        }

        .merchant-header-row .btn,
        .merchant-header-row form,
        .merchant-header-row input,
        .merchant-header-row button {
            width: 100%;
        }

        .merchant-header-row form {
            display: grid !important;
            gap: 8px;
        }

        .card-body .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .card-body .table {
            min-width: 860px;
        }

        .card-body .d-flex[style*="gap:8px"] {
            flex-direction: column;
            align-items: stretch !important;
        }

        .card-body .d-flex[style*="gap:8px"] .form-select,
        .card-body .d-flex[style*="gap:8px"] .btn {
            width: 100%;
        }

        .d-flex.align-items-center.gap-2.mt-3 {
            flex-direction: column;
            align-items: stretch !important;
        }

        .d-flex.align-items-center.gap-2.mt-3 .form-select,
        .d-flex.align-items-center.gap-2.mt-3 .btn {
            width: 100%;
            max-width: none !important;
        }
    }

    @media (max-width: 576px) {
        .page-title {
            font-size: 22px;
        }

        .page-title i {
            font-size: 22px;
        }

        .page-subtitle {
            font-size: 12px;
        }
    }
</style>

@if (session('success'))
    <div class="alert alert-orange alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <strong>Merchants</strong>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($merchants as $merchant)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $merchant->business_name ?? $merchant->name }}</strong>
                                <div class="text-muted small">{{ $merchant->admin_email }}</div>
                            </div>
                            <a href="{{ route('super-admin.merchants.show', $merchant) }}" class="btn btn-sm btn-outline-orange">View</a>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">No merchants found</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Unassigned Users</strong>
                <form method="GET" class="d-flex" style="gap:8px;">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search users..." value="{{ request('search') }}">
                    <button class="btn btn-sm btn-outline-orange">Search</button>
                </form>
            </div>
            <div class="card-body">
                <form action="{{ route('super-admin.merchants.assignUser') }}" method="POST" id="bulk-assign-form">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover">
                        <thead>
                            <tr>
                                    <th style="width:40px;"><input type="checkbox" id="select-all"></th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th style="width:320px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($unassignedUsers as $user)
                                <tr>
                                    <td><input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox"></td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->user_type ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex" style="gap:8px; align-items:center;">
                                            <select name="row_merchant_{{ $user->id }}" class="form-select form-select-sm row-merchant-select">
                                                <option value="">Select merchant...</option>
                                                @foreach($merchants as $merchant)
                                                    <option value="{{ $merchant->id }}">{{ $merchant->business_name ?? $merchant->name }} ({{ $merchant->admin_email }})</option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn btn-sm btn-outline-orange assign-single" data-user-id="{{ $user->id }}">Assign</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No unassigned users found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                    <div class="d-flex align-items-center gap-2 mt-3">
                        <select name="merchant_id" class="form-select form-select-sm" style="max-width:320px;" required>
                            <option value="">Assign selected to merchant...</option>
                            @foreach($merchants as $merchant)
                                <option value="{{ $merchant->id }}">{{ $merchant->business_name ?? $merchant->name }} ({{ $merchant->admin_email }})</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary-orange">Assign selected</button>
                        <small class="text-muted ms-2">You can also assign individual users using the row controls.</small>
                    </div>

                    <div class="mt-3 d-flex justify-content-center">
                        {{ $unassignedUsers->links() }}
                    </div>
                </form>

                <script>
                    // Select all toggle
                    document.getElementById('select-all')?.addEventListener('change', function(e){
                        document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = e.target.checked);
                    });

                    // Assign single row button: submits a small form via fetch
                    document.querySelectorAll('.assign-single').forEach(btn => {
                        btn.addEventListener('click', function(){
                            const userId = this.dataset.userId;
                            const select = document.querySelector('select[name="row_merchant_' + userId + '"]');
                            const merchantId = select?.value;
                            if (!merchantId) {
                                alert('Please select a merchant to assign');
                                return;
                            }
                            // create and submit a form
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '{{ route('super-admin.merchants.assignUser') }}';
                            form.style.display = 'none';
                            const token = document.createElement('input'); token.name = '_token'; token.value = '{{ csrf_token() }}';
                            const mid = document.createElement('input'); mid.name = 'merchant_id'; mid.value = merchantId;
                            const uid = document.createElement('input'); uid.name = 'user_id'; uid.value = userId;
                            form.appendChild(token); form.appendChild(mid); form.appendChild(uid);
                            document.body.appendChild(form);
                            form.submit();
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</div>
@endsection
