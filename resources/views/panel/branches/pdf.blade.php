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
        .stats { width: 100%; border-collapse: separate; border-spacing: 10px; margin: 14px -10px 0; }
        .stat { border: 1px solid #eadcae; border-radius: 16px; padding: 12px; background: #fff8db; }
        .stat span { display: block; font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: #71665f; }
        .stat strong { display: block; margin-top: 7px; font-size: 18px; color: #8f0000; }
        .grid { width: 100%; border-collapse: separate; border-spacing: 12px; margin: 14px -12px 0; }
        .panel { border: 1px solid #eee2c3; border-radius: 16px; padding: 14px; }
        .panel h2 { margin: 0 0 10px; font-size: 15px; }
        .dl-row { margin-bottom: 10px; }
        .dt { font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: #70655e; font-weight: bold; }
        .dd { margin-top: 4px; font-weight: bold; }
        .list-row { padding: 8px 0; border-bottom: 1px solid #f1ebdf; }
        .list-row:last-child { border-bottom: 0; }
        .list-title { font-weight: bold; }
        .list-sub { margin-top: 3px; font-size: 10px; color: #655b55; }
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
                    <h1>{{ $branch->name }}</h1>
                    <div class="muted">{{ __('Redeemed Gospel Church Inc. Tanzania') }}</div>
                    <div class="muted">{{ __('Branch Profile Report') }}</div>
                    <div class="muted">{{ __('Issued through the national church management platform.') }}</div>
                </td>
                <td style="text-align:right;" class="muted">
                    <div>{{ __('Generated on :date', ['date' => now()->translatedFormat('d M Y H:i')]) }}</div>
                    <div>{{ __('Region: :region', ['region' => $branch->region->name]) }}</div>
                    <div>{{ __('District: :district', ['district' => $branch->district->name]) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="chips">
        <span class="chip">{{ __('Type: :type', ['type' => __(Illuminate\Support\Str::headline($branch->branch_type))]) }}</span>
        <span class="chip">{{ __('Status: :status', ['status' => __(Illuminate\Support\Str::headline($branch->status))]) }}</span>
        @if($branch->is_headquarters)
            <span class="chip">{{ __('Headquarters branch') }}</span>
        @endif
    </div>

    <table class="stats">
        <tr>
            <td class="stat"><span>{{ __('Users') }}</span><strong>{{ $branch->users_count }}</strong></td>
            <td class="stat"><span>{{ __('Messages') }}</span><strong>{{ $branch->messages_count }}</strong></td>
            <td class="stat"><span>{{ __('Events') }}</span><strong>{{ $branch->events_count }}</strong></td>
        </tr>
        <tr>
            <td class="stat"><span>{{ __('Offerings total') }}</span><strong>TZS {{ number_format((float) ($branch->offerings_total_amount ?? 0), 2) }}</strong></td>
            <td class="stat"><span>{{ __('Expenses total') }}</span><strong>TZS {{ number_format((float) ($branch->expenses_total_amount ?? 0), 2) }}</strong></td>
            <td class="stat"><span>{{ __('Net balance') }}</span><strong>TZS {{ number_format($netBalance, 2) }}</strong></td>
        </tr>
        <tr>
            <td class="stat"><span>{{ __('Payment Requests') }}</span><strong>{{ $branch->offering_payments_count }}</strong></td>
            <td class="stat"><span>{{ __('Pending Payments') }}</span><strong>{{ $branch->pending_payments_count }}</strong></td>
            <td class="stat"><span>{{ __('Completed Payments') }}</span><strong>{{ $branch->completed_payments_count }}</strong></td>
        </tr>
    </table>

    <table class="grid">
        <tr>
            <td class="panel" width="50%">
                <h2>{{ __('Governance Scope') }}</h2>
                <div class="dl-row"><div class="dt">{{ __('Region') }}</div><div class="dd">{{ $branch->region->name }}</div></div>
                <div class="dl-row"><div class="dt">{{ __('District') }}</div><div class="dd">{{ $branch->district->name }}</div></div>
                <div class="dl-row"><div class="dt">{{ __('Branch type') }}</div><div class="dd">{{ __(Illuminate\Support\Str::headline($branch->branch_type)) }}</div></div>
                <div class="dl-row"><div class="dt">{{ __('Status') }}</div><div class="dd">{{ __(Illuminate\Support\Str::headline($branch->status)) }}</div></div>
            </td>
            <td class="panel" width="50%">
                <h2>{{ __('Contact Details') }}</h2>
                <div class="dl-row"><div class="dt">{{ __('Address') }}</div><div class="dd">{{ $branch->address ?: __('No address recorded') }}</div></div>
                <div class="dl-row"><div class="dt">{{ __('Phone') }}</div><div class="dd">{{ $branch->phone ?: __('No phone recorded') }}</div></div>
                <div class="dl-row"><div class="dt">{{ __('Email') }}</div><div class="dd">{{ $branch->email ?: __('No email recorded') }}</div></div>
                <div class="dl-row"><div class="dt">{{ __('Slug') }}</div><div class="dd">{{ $branch->slug }}</div></div>
            </td>
        </tr>
        <tr>
            <td class="panel" width="50%">
                <h2>{{ __('Recent Offerings') }}</h2>
                @forelse($recentOfferings as $offering)
                    <div class="list-row">
                        <div class="list-title">TZS {{ number_format((float) $offering->amount, 2) }}</div>
                        <div class="list-sub">{{ optional($offering->date)->translatedFormat('d M Y') }}</div>
                    </div>
                @empty
                    <div class="list-sub">{{ __('No offerings recorded for this branch yet.') }}</div>
                @endforelse
            </td>
            <td class="panel" width="50%">
                <h2>{{ __('Recent Expenses') }}</h2>
                @forelse($recentExpenses as $expense)
                    <div class="list-row">
                        <div class="list-title">{{ $expense->description ?: __('General expense') }}</div>
                        <div class="list-sub">TZS {{ number_format((float) $expense->amount, 2) }} • {{ optional($expense->date)->translatedFormat('d M Y') }}</div>
                    </div>
                @empty
                    <div class="list-sub">{{ __('No expenses recorded for this branch yet.') }}</div>
                @endforelse
            </td>
        </tr>
        <tr>
            <td class="panel" width="50%">
                <h2>{{ __('Recent Payment Requests') }}</h2>
                @forelse($recentPayments as $payment)
                    <div class="list-row">
                        <div class="list-title">{{ $payment->statusLabel() }} • TZS {{ number_format((float) $payment->amount, 2) }}</div>
                        <div class="list-sub">{{ $payment->public_reference }} • {{ $payment->payer_name ?: __('Walk-in giver') }}</div>
                    </div>
                @empty
                    <div class="list-sub">{{ __('No Snippe payment requests created for this branch yet.') }}</div>
                @endforelse
            </td>
            <td class="panel" width="50%">
                <h2>{{ __('Snippe collected total') }}</h2>
                <div class="dl-row"><div class="dt">{{ __('Completed payments total') }}</div><div class="dd">TZS {{ number_format((float) ($branch->completed_payments_total_amount ?? 0), 2) }}</div></div>
                <div class="dl-row"><div class="dt">{{ __('Payment requests total') }}</div><div class="dd">TZS {{ number_format((float) ($branch->payment_requests_total_amount ?? 0), 2) }}</div></div>
            </td>
        </tr>
        <tr>
            <td class="panel" width="50%">
                <h2>{{ __('Recent Events') }}</h2>
                @forelse($recentEvents as $event)
                    <div class="list-row">
                        <div class="list-title">{{ $event->title }}</div>
                        <div class="list-sub">{{ optional($event->event_date)->translatedFormat('d M Y H:i') }}</div>
                    </div>
                @empty
                    <div class="list-sub">{{ __('No events recorded for this branch yet.') }}</div>
                @endforelse
            </td>
            <td class="panel" width="50%">
                <h2>{{ __('Recent Users') }}</h2>
                @forelse($recentUsers as $user)
                    <div class="list-row">
                        <div class="list-title">{{ $user->name }}</div>
                        <div class="list-sub">{{ $user->email }}</div>
                    </div>
                @empty
                    <div class="list-sub">{{ __('No users are linked to this branch yet.') }}</div>
                @endforelse
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
        {{ __('Generated through the RGC Management Platform.') }}<br>
        {{ __('Generated on :date', ['date' => now()->translatedFormat('d M Y H:i')]) }}
    </div>
</div>
</body>
</html>
