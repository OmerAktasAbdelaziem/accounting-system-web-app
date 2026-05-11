<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; }
        .invoice { width: 100%; }
        .header { text-align: center; margin-bottom: 20px; }
        .details { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="invoice">
        <div class="header">
            <h2>{{ __('Invoice') }} - {{ $invoice->invoice_number }}</h2>
        </div>
        <div class="details">
            <p><strong>{{ __('Customer') }}:</strong> {{ $invoice->customer?->name }}</p>
            <p><strong>{{ __('Date') }}:</strong> {{ $invoice->date }}</p>
        </div>
        <div class="totals">
            <p>{{ __('Sub Total') }}: {{ number_format($invoice->sub_total,2) }}</p>
            <p>{{ __('Tax') }}: {{ number_format($invoice->tax,2) }}</p>
            <p><strong>{{ __('Total') }}: {{ number_format($invoice->total,2) }}</strong></p>
        </div>
    </div>
</body>
</html>
