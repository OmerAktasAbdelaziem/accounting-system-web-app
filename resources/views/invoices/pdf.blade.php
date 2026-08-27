<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    {{ __('<style>
        body { font-family: DejaVu Sans, Arial, sans-serif; }
        .invoice { width: 100%; }
        .header { text-align: center; margin-bottom: 20px; }
        .details { margin-bottom: 10px; }
    </style>') }}
</head>
<body>
    <div class="invoice">
        <div class="header">
            <h2>{{ __('messages.invoice') }} - {{ $invoice->invoice_number }}</h2>
        </div>
        <div class="details">
            <p><strong>{{ __('messages.customer') }}:</strong> {{ $invoice->customer?->name }}</p>
            <p><strong>{{ __('messages.date') }}:</strong> {{ $invoice->date }}</p>
        </div>
        <div class="totals">
            <p>{{ __('messages.sub_total') }}: {{ number_format($invoice->sub_total,2) }}</p>
            <p>{{ __('messages.tax') }}: {{ number_format($invoice->tax,2) }}</p>
            <p><strong>{{ __('messages.total') }}: {{ number_format($invoice->total,2) }}</strong></p>
        </div>
    </div>
</body>
</html>
