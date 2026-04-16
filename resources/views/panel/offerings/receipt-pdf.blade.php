<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1b1714; font-size: 12px; }
        .topbar { height: 10px; background: linear-gradient(90deg, #c00000 0%, #ffd700 100%); border-radius: 999px; }
        .sheet { padding: 24px 18px 12px; }
        .brand { width: 100%; border-bottom: 1px solid #e7d8a3; padding-bottom: 14px; }
        .brand-table { width: 100%; border-collapse: collapse; }
        .brand-table td { vertical-align: top; }
        .logo { width: 74px; }
        .logo img { width: 62px; height: auto; }
        .kicker { font-size: 10px; font-weight: bold; letter-spacing: .14em; text-transform: uppercase; color: #8f0000; }
        h1 { margin: 8px 0 4px; font-size: 23px; }
        .muted { color: #685d56; font-size: 11px; }
        .chips { margin-top: 12px; }
        .chip { display: inline-block; margin-right: 6px; margin-bottom: 6px; padding: 6px 10px; border-radius: 999px; background: #f3ece0; font-size: 10px; font-weight: bold; }
        .grid { width: 100%; border-collapse: separate; border-spacing: 12px; margin: 14px -12px 0; }
        .panel { border: 1px solid #eee2c3; border-radius: 16px; padding: 14px; }
        .panel h2 { margin: 0 0 10px; font-size: 15px; }
        .dl-row { margin-bottom: 10px; }
        .dt { font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: #70655e; font-weight: bold; }
        .dd { margin-top: 4px; font-weight: bold; }
        .signature-block { margin-top: 18px; border-top: 1px solid #eadcae; padding-top: 18px; }
        .signature-label { font-size: 9px; text-transform: uppercase; letter-spacing: .14em; color: #8f0000; font-weight: bold; }
        .signature-line { width: 210px; border-bottom: 1px solid #1b1714; margin: 20px 0 8px; }
        .signature-name { font-weight: bold; font-size: 12px; }
        .signature-role { margin-top: 4px; color: #685d56; font-size: 10px; }
        .footer { margin-top: 16px; padding-top: 10px; border-top: 1px solid #f1ebdf; font-size: 10px; color: #685d56; text-align: right; }
    </style>
</head>
<body>
<div class="topbar"></div>
<div class="sheet">
    <div class="brand">
        <table class="brand-table">
            <tr>
                <td class="logo">
                    @if($logoDataUri)
                        <img src="{{ $logoDataUri }}" alt="{{ __('RGC Logo') }}">
                    @endif
                </td>
                <td>
                    <div class="kicker">RGC</div>
                    <h1>{{ __('Offering Payment Receipt') }}</h1>
                    <div class="muted">{{ __('Redeemed Gospel Church Inc. Tanzania') }}</div>
                    <div class="muted">{{ __('Official church giving confirmation generated from the Redeemed Gospel Church Tanzania system.') }}</div>
                </td>
                <td style="text-align:right;" class="muted">
                    <div>{{ __('Generated on :date', ['date' => now()->translatedFormat('d M Y H:i')]) }}</div>
                    <div>{{ __('Receipt reference: :reference', ['reference' => $payment->public_reference]) }}</div>
                    <div>{{ __('Provider reference: :reference', ['reference' => $payment->provider_reference]) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="chips">
        <span class="chip">{{ __('Payment status') }}: {{ $payment->statusLabel() }}</span>
        <span class="chip">{{ __('Amount') }}: TZS {{ number_format((float) $payment->amount, 2) }}</span>
        <span class="chip">{{ __('Branch') }}: {{ $payment->branch?->name ?? __('Unknown branch') }}</span>
    </div>

    <table class="grid">
        <tr>
            <td class="panel" width="50%">
                <h2>{{ __('Receipt Details') }}</h2>
                <div class="dl-row"><div class="dt">{{ __('Payer') }}</div><div class="dd">{{ $payment->payer_name ?: __('Walk-in giver') }}</div></div>
                <div class="dl-row"><div class="dt">{{ __('Payer email') }}</div><div class="dd">{{ $payment->payer_email ?: __('Not provided') }}</div></div>
                <div class="dl-row"><div class="dt">{{ __('Payer phone') }}</div><div class="dd">{{ $payment->payer_phone ?: __('Not provided') }}</div></div>
                <div class="dl-row"><div class="dt">{{ __('Purpose') }}</div><div class="dd">{{ $payment->description ?: __('Offering payment') }}</div></div>
                <div class="dl-row"><div class="dt">{{ __('Offering date') }}</div><div class="dd">{{ optional($payment->offering_date)->translatedFormat('d M Y') ?: __('Not set') }}</div></div>
            </td>
            <td class="panel" width="50%">
                <h2>{{ __('Confirmation') }}</h2>
                <div class="dl-row"><div class="dt">{{ __('Paid at') }}</div><div class="dd">{{ optional($payment->paid_at)->translatedFormat('d M Y H:i') ?: __('Waiting for confirmation') }}</div></div>
                <div class="dl-row"><div class="dt">{{ __('Branch') }}</div><div class="dd">{{ $payment->branch?->name ?? __('Unknown branch') }}</div></div>
                <div class="dl-row"><div class="dt">{{ __('Region') }}</div><div class="dd">{{ $payment->branch?->region?->name ?? __('Not set') }}</div></div>
                <div class="dl-row"><div class="dt">{{ __('District') }}</div><div class="dd">{{ $payment->branch?->district?->name ?? __('Not set') }}</div></div>
                <div class="dl-row"><div class="dt">{{ __('Ledger record') }}</div><div class="dd">{{ $payment->offering ? __('Offering #:id', ['id' => $payment->offering->id]) : __('Pending ledger entry') }}</div></div>
            </td>
        </tr>
    </table>

    <div class="signature-block">
        <div class="signature-label">{{ __('Authorized by') }}</div>
        <div class="signature-line"></div>
        <div class="signature-name">{{ __('Office of the Super Admin') }}</div>
        <div class="signature-role">{{ __('Redeemed Gospel Church Inc. Tanzania') }}</div>
    </div>

    <div class="footer">
        {{ __('Generated through the RGC Tanzania system.') }}<br>
        {{ __('Generated on :date', ['date' => now()->translatedFormat('d M Y H:i')]) }}
    </div>
</div>
</body>
</html>
