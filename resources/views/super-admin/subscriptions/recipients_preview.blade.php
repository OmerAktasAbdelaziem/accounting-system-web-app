@extends('layouts.super-admin')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Preview Recipients for {{ $merchant->business_name }}</h4>
                    @if(session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif
                    <p>{{ __('Recipients (merchant admins):') }}</p>
                    <table class="table table-striped">
                        <thead><tr><th>{{ __('Name') }}</th><th>{{ __('Email') }}</th><th>{{ __('Role') }}</th></tr></thead>
                        <tbody>
                            @foreach($admins as $a)
                                <tr>
                                    <td>{{ $a->name }}</td>
                                    <td>{{ $a->email }}</td>
                                    <td>{{ optional($a->role)->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <form method="POST" action="{{ route('super-admin.subscriptions.recipients_preview.send', $merchant->{{ __('id) }}">
                        @csrf') }}
                        <div class="mb-3">
                            <label class="form-label">{{ __('Preview Message') }}</label>
                            <textarea name="message" class="form-control" rows="3">{{ __('This is a preview of subscription-related notifications.') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('Send Preview Notification') }}</button>
                        <a href="{{ route('super-admin.subscriptions.index') }}" class="btn btn-secondary ms-2">{{ __('Back') }}</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
