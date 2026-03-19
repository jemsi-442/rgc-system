<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; color: #1b1714; background: #f7f4ee; margin: 0; padding: 24px; }
        .shell { max-width: 640px; margin: 0 auto; background: #ffffff; border: 1px solid #eadcae; border-radius: 24px; overflow: hidden; }
        .topbar { height: 10px; background: linear-gradient(90deg, #c00000 0%, #ffd700 100%); }
        .content { padding: 28px; }
        .kicker { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .12em; color: #8f0000; }
        h1 { margin: 12px 0 8px; font-size: 28px; }
        p { line-height: 1.6; color: #433b36; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin: 20px 0; }
        .card { border: 1px solid #efe5cc; border-radius: 18px; padding: 14px; background: #fffaf0; }
        .label { font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: #7a6d64; font-weight: 700; }
        .value { margin-top: 6px; font-weight: 700; color: #1b1714; }
        .actions { margin-top: 24px; }
        .btn { display: inline-block; margin-right: 10px; margin-bottom: 10px; padding: 12px 18px; border-radius: 999px; text-decoration: none; font-weight: 700; }
        .btn-primary { background: #c00000; color: #ffffff; }
        .btn-secondary { background: #fff2bf; color: #1b1714; border: 1px solid #e8d48a; }
        .footer { margin-top: 18px; font-size: 12px; color: #6d625b; }
    </style>
</head>
<body>
<div class="shell">
    <div class="topbar"></div>
    <div class="content">
        <div class="kicker">{{ __('Branch payment alert') }}</div>
        <h1>{{ __('A new branch payment has been completed.') }}</h1>
        <p>{{ __('A donor payment has just been confirmed and posted into the branch collections flow. Review the payment status page or open your dashboard for the latest branch activity.') }}</p>

        <div class="grid">
            <div class="card">
                <div class="label">{{ __('Reference') }}</div>
                <div class="value">{{ $payment->public_reference }}</div>
            </div>
            <div class="card">
                <div class="label">{{ __('Amount') }}</div>
                <div class="value">TZS {{ number_format((float) $payment->amount, 2) }}</div>
            </div>
            <div class="card">
                <div class="label">{{ __('Giving type') }}</div>
                <div class="value">{{ $payment->paymentTypeLabel() }}</div>
            </div>
            <div class="card">
                <div class="label">{{ __('Branch') }}</div>
                <div class="value">{{ $payment->branch?->name ?? __('Unknown branch') }}</div>
            </div>
        </div>

        <div class="actions">
            <a class="btn btn-primary" href="{{ $dashboardUrl }}">{{ __('Open dashboard') }}</a>
            <a class="btn btn-secondary" href="{{ $statusUrl }}">{{ __('Open status page') }}</a>
        </div>

        <p class="footer">{{ __('This alert is generated automatically by the RGC payment workflow after successful checkout confirmation.') }}</p>
    </div>
</div>
</body>
</html>