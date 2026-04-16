<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $branch->name }} - {{ __('Branch Details') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 0; background: #f7f3e8; color: #1f1a17; }
        .page { max-width: 960px; margin: 0 auto; padding: 28px; }
        .actions { margin-bottom: 18px; display: flex; gap: 10px; flex-wrap: wrap; }
        .actions a, .actions button { text-decoration: none; border: 0; padding: 10px 14px; border-radius: 999px; font-weight: 700; cursor: pointer; }
        .actions a { background: #ffffff; color: #8f0000; border: 1px solid rgba(143,0,0,.15); }
        .actions button { background: #c00000; color: #fff; }
        .sheet { background: #fff; border-radius: 22px; padding: 28px; box-shadow: 0 18px 40px rgba(0,0,0,.08); }
        .brand { display: flex; justify-content: space-between; gap: 16px; align-items: flex-start; border-bottom: 2px solid #f1e2a3; padding-bottom: 16px; }
        .brand h1 { margin: 10px 0 6px; font-size: 28px; }
        .brand p { margin: 0; color: #60554e; }
        .chips { margin-top: 16px; display: flex; flex-wrap: wrap; gap: 8px; }
        .chip { background: #f4efe0; border-radius: 999px; padding: 8px 12px; font-size: 13px; font-weight: 700; }
        .stats { margin-top: 24px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
        .stat { border: 1px solid #f0e1a8; border-radius: 18px; padding: 16px; background: linear-gradient(180deg, #fff, #fff8da); }
        .stat span { display: block; font-size: 12px; text-transform: uppercase; letter-spacing: .08em; color: #726861; }
        .stat strong { display: block; margin-top: 8px; font-size: 22px; color: #8f0000; }
        .grid { margin-top: 24px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; }
        .panel { border: 1px solid #eee2c3; border-radius: 18px; padding: 18px; }
        .panel h2 { margin: 0 0 14px; font-size: 18px; }
        dl { margin: 0; display: grid; gap: 12px; }
        dt { font-size: 12px; text-transform: uppercase; color: #7a6f66; font-weight: 700; letter-spacing: .08em; }
        dd { margin: 4px 0 0; font-weight: 700; }
        .list { margin: 0; padding: 0; list-style: none; display: grid; gap: 10px; }
        .list li { border-bottom: 1px solid #f2ebe0; padding-bottom: 10px; }
        .list li:last-child { border-bottom: 0; padding-bottom: 0; }
        .list strong { display: block; }
        .list span { color: #635851; font-size: 13px; }
        @media print {
            body { background: #fff; }
            .actions { display: none; }
            .page { padding: 0; }
            .sheet { box-shadow: none; border-radius: 0; }
        }
    </style>
</head>
<body>
<div class="page">
    <div class="actions">
        <a href="{{ route('branches.show', $branch) }}">{{ __('Back to branch') }}</a>
        <a href="{{ route('branches.pdf', $branch) }}">{{ __('Download PDF') }}</a>
        <button onclick="window.print()">{{ __('Print now') }}</button>
    </div>

    <div class="sheet">
        <div class="brand">
            <div>
                <div style="font-size:12px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#8f0000;">RGC</div>
                <h1>{{ $branch->name }}</h1>
                <p>{{ __('Redeemed Gospel Church Inc. Tanzania') }}</p>
                <p>{{ __('Branch details report') }}</p>
            </div>
            <div style="text-align:right;font-size:13px;color:#60554e;">
                <div>{{ __('Generated on :date', ['date' => now()->format('d M Y H:i')]) }}</div>
                <div>{{ __('Region: :region', ['region' => $branch->region->name]) }}</div>
                <div>{{ __('District: :district', ['district' => $branch->district->name]) }}</div>
            </div>
        </div>

        <div class="chips">
            <span class="chip">{{ __('Type: :type', ['type' => __(Illuminate\Support\Str::headline($branch->branch_type))]) }}</span>
            <span class="chip">{{ __('Status: :status', ['status' => __(Illuminate\Support\Str::headline($branch->status))]) }}</span>
            @if($branch->is_headquarters)
                <span class="chip">{{ __('Headquarters branch') }}</span>
            @endif
        </div>

        <div class="stats">
            <div class="stat"><span>{{ __('Users') }}</span><strong>{{ $branch->users_count }}</strong></div>
            <div class="stat"><span>{{ __('Messages') }}</span><strong>{{ $branch->messages_count }}</strong></div>
            <div class="stat"><span>{{ __('Events') }}</span><strong>{{ $branch->events_count }}</strong></div>
            <div class="stat"><span>{{ __('Offerings total') }}</span><strong>TZS {{ number_format((float) ($branch->offerings_total_amount ?? 0), 2) }}</strong></div>
            <div class="stat"><span>{{ __('Expenses total') }}</span><strong>TZS {{ number_format((float) ($branch->expenses_total_amount ?? 0), 2) }}</strong></div>
            <div class="stat"><span>{{ __('Net balance') }}</span><strong>TZS {{ number_format($netBalance, 2) }}</strong></div>
        </div>

        <div class="grid">
            <section class="panel">
                <h2>{{ __('Church Location') }}</h2>
                <dl>
                    <div><dt>{{ __('Region') }}</dt><dd>{{ $branch->region->name }}</dd></div>
                    <div><dt>{{ __('District') }}</dt><dd>{{ $branch->district->name }}</dd></div>
                    <div><dt>{{ __('Branch type') }}</dt><dd>{{ __(Illuminate\Support\Str::headline($branch->branch_type)) }}</dd></div>
                    <div><dt>{{ __('Status') }}</dt><dd>{{ __(Illuminate\Support\Str::headline($branch->status)) }}</dd></div>
                </dl>
            </section>
            <section class="panel">
                <h2>{{ __('Contact Details') }}</h2>
                <dl>
                    <div><dt>{{ __('Address') }}</dt><dd>{{ $branch->address ?: __('No address recorded') }}</dd></div>
                    <div><dt>{{ __('Phone') }}</dt><dd>{{ $branch->phone ?: __('No phone recorded') }}</dd></div>
                    <div><dt>{{ __('Email') }}</dt><dd>{{ $branch->email ?: __('No email recorded') }}</dd></div>
                    <div><dt>{{ __('Slug') }}</dt><dd>{{ $branch->slug }}</dd></div>
                </dl>
            </section>
        </div>

        <div class="grid">
            <section class="panel">
                <h2>{{ __('Recent Offerings') }}</h2>
                <ul class="list">
                    @forelse($recentOfferings as $offering)
                        <li>
                            <strong>TZS {{ number_format((float) $offering->amount, 2) }}</strong>
                            <span>{{ optional($offering->date)->format('d M Y') }}</span>
                        </li>
                    @empty
                        <li><span>{{ __('No offerings recorded for this branch yet.') }}</span></li>
                    @endforelse
                </ul>
            </section>
            <section class="panel">
                <h2>{{ __('Recent Expenses') }}</h2>
                <ul class="list">
                    @forelse($recentExpenses as $expense)
                        <li>
                            <strong>{{ $expense->description ?: __('General expense') }}</strong>
                            <span>TZS {{ number_format((float) $expense->amount, 2) }} • {{ optional($expense->date)->format('d M Y') }}</span>
                        </li>
                    @empty
                        <li><span>{{ __('No expenses recorded for this branch yet.') }}</span></li>
                    @endforelse
                </ul>
            </section>
        </div>
    </div>
</div>
</body>
</html>
