@extends('layouts.super-admin')

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <h1 class="page-title">Cancel Subscription</h1>
    </div>

    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">⚠️ Confirm Subscription Cancellation</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-4">
                        <strong>Warning:</strong> This action will cancel the subscription and may impact the merchant's access to the system.
                    </div>

                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Merchant:</strong><br>
                                    <span class="fs-5">{{ $subscription->merchant->name }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Package:</strong><br>
                                    <span class="fs-5">{{ $subscription->package->name }}</span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Subscription Expires:</strong><br>
                                    {{ $subscription->expires_at->format('M d, Y H:i') }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Status:</strong><br>
                                    <span class="badge {{ $subscription->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $subscription->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Price:</strong><br>
                                    ${{ number_format($subscription->package->price, 2) }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Duration:</strong><br>
                                    {{ $subscription->package->duration_days }} days
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6>Impact of Cancellation:</h6>
                    <ul class="mb-4">
                        <li>All features included in this package will be disabled</li>
                        <li>The merchant will lose access to advanced features</li>
                        <li>Current data will remain but limited functionality will be available</li>
                        <li>To restore service, a new subscription must be created</li>
                    </ul>

                    <form action="{{ route('super-admin.subscriptions.destroy', $subscription) }}" method="POST">
                        @csrf @method('DELETE')

                        <div class="mb-3">
                            <label class="form-label">Cancellation Reason (optional)</label>
                            <textarea name="cancellation_reason" class="form-control" rows="3" placeholder="Document why this subscription is being cancelled..."></textarea>
                        </div>

                        <div class="form-check mb-4">
                            <input type="checkbox" name="send_notification" class="form-check-input" id="sendNotif" value="1" checked>
                            <label class="form-check-label" for="sendNotif">Send cancellation notification email to merchant admin</label>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('super-admin.subscriptions.show', $subscription) }}" class="btn btn-outline-secondary">Back</a>
                            <button type="submit" class="btn btn-danger ms-auto">
                                <i class="icon icon-trash"></i> Confirm Cancellation
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-light"><h5 class="mb-0">Alternative Actions</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Pause Instead:</strong>
                            <p class="text-muted small">Deactivate subscription temporarily without full cancellation</p>
                            <form action="{{ route('super-admin.subscriptions.update', $subscription) }}" method="POST" style="display:inline;">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="pause">
                                <button type="submit" class="btn btn-outline-warning btn-sm">Pause</button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <strong>Schedule Cancellation:</strong>
                            <p class="text-muted small">Cancel subscription at specific date</p>
                            <a href="{{ route('super-admin.subscriptions.show', $subscription) }}" class="btn btn-outline-info btn-sm">Schedule</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
