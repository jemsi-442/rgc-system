<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title ?? __('Ripoti ya Fedha') }} - {{ $settings->company_name ?? 'Redeemed Gospel Church Inc. Tanzania' }}</title>
    <style>
        @page {
            margin: 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            padding: 5rem;
        }

        /* Header with Logo */
        .report-header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }

        .header-left {
            display: table-cell;
            width: 15%;
            vertical-align: middle;
        }

        .header-center {
            display: table-cell;
            width: 70%;
            vertical-align: middle;
            text-align: center;
        }

        .header-right {
            display: table-cell;
            width: 15%;
            vertical-align: middle;
            text-align: right;
        }

        .logo {
            width: 70px;
            height: auto;
        }

        .church-name {
            font-size: 16px;
            font-weight: bold;
            color: #000;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .diocese {
            font-size: 12px;
            color: #000;
            margin-bottom: 2px;
        }

        .parish {
            font-size: 11px;
            color: #333;
        }

        .report-meta {
            font-size: 10px;
            color: #000;
            line-height: 1.5;
        }

        /* Report Title Box */
        .report-title-box {
            border: 2px solid #000;
            padding: 12px 20px;
            margin-bottom: 20px;
            text-align: center;
        }

        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .report-subtitle {
            font-size: 11px;
            color: #000;
        }

        /* Summary Section */
        .summary-section {
            margin: 20px 0;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto 20px;
            font-size: 11px;
        }

        .summary-table th {
            background: #000;
            color: #fff;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #000;
        }

        .summary-table td {
            padding: 10px 8px;
            border: 1px solid #000;
            vertical-align: middle;
            text-align: right;
            font-weight: bold;
        }

        .summary-table td:first-child {
            text-align: left;
            font-weight: normal;
        }

        /* Section Title */
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #000;
            padding: 8px 0;
            border-bottom: 1px solid #000;
            margin: 20px 0 10px;
            text-transform: uppercase;
        }

        /* Main Data Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto 20px;
            font-size: 10px;
        }

        table th {
            background: #000;
            color: #fff;
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #000;
        }

        table td {
            padding: 8px;
            border: 1px solid #000;
            vertical-align: middle;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .table-total {
            font-weight: bold;
        }

        .table-total td {
            border-top: 2px solid #000;
            padding: 12px 8px;
        }

        /* Category Section */
        .category-header {
            background: #000;
            color: #fff;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 11px;
            margin-top: 15px;
            border: 1px solid #000;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-title {
            font-size: 11px;
            font-weight: bold;
            color: #000;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .signature-box {
            width: 30%;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 8px;
        }

        .signature-name {
            font-weight: bold;
            font-size: 10px;
        }

        .signature-title-text {
            font-size: 9px;
            color: #000;
        }

        .signature-date {
            font-size: 8px;
            color: #000;
            margin-top: 3px;
        }

        /* Footer */
        .report-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10px 1.5cm;
            border-top: 1px solid #000;
            font-size: 8px;
            color: #000;
            display: flex;
            justify-content: space-between;
        }

        .footer-left {
            text-align: left;
        }

        .footer-center {
            text-align: center;
        }

        .footer-right {
            text-align: right;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(0, 0, 0, 0.05);
            font-weight: bold;
            text-transform: uppercase;
            pointer-events: none;
            z-index: -1;
        }

        /* Print specific styles */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none;
            }
        }

        /* Charts placeholder */
        .chart-section {
            margin: 20px 0;
            padding: 20px;
            border: 1px dashed #000;
            text-align: center;
        }

        .chart-placeholder {
            color: #000;
            font-size: 10px;
        }

        /* Notes section */
        .notes-section {
            margin-top: 25px;
            padding: 15px;
            border: 1px solid #000;
        }

        .notes-title {
            font-weight: bold;
            color: #000;
            margin-bottom: 8px;
            font-size: 11px;
        }

        .notes-content {
            font-size: 10px;
            color: #000;
        }
    </style>
</head>
<body>
    @if($include_watermark ?? false)
    <div class="watermark">{{ $settings->company_name ?? 'RGC' }}</div>
    @endif

    <!-- Report Header with Logo -->
    @if($include_header ?? true)
    <div class="report-header">
        <div class="header-left">
            @if($include_logo ?? true)
            <img src="{{ public_path('images/rgc_logo.png') }}" alt="{{ __('RGC Logo') }}" class="logo">
            @endif
        </div>
        <div class="header-center">
            <div class="church-name">{{ $settings->company_name ?? 'REDEEMED GOSPEL CHURCH INC. TANZANIA' }}</div>
            <div class="diocese">{{ __('MAKAO MAKUU YA RGC TANZANIA') }}</div>
            <div class="parish">REDEEMED GOSPEL CHURCH INC. TANZANIA</div>
            <div class="report-meta">
                <div>{{ __('Makao Makuu') }}: Toangoma, Temeke, Dar es Salaam</div>
                <div>{{ __('Simu') }}: {{ $settings->phone ?? '+255 22 266 9035' }}</div>
                <div>{{ __('Barua pepe') }}: {{ $settings->email ?? 'noreply@rgc.or.tz' }}</div>
            </div>
        </div>
        <div class="header-right">
            <!-- Empty for balance -->
        </div>
    </div>
    @endif

    <!-- Report Title Box -->
    <div class="report-title-box">
        <div class="report-title">{{ $title ?? __('Ripoti ya Fedha') }}</div>
        <div class="report-subtitle">{{ $period_text ?? __('Kipindi') . ': ' . ($start_date ?? '') . ' - ' . ($end_date ?? '') }}</div>
        <div class="report-subtitle">{{ __('Imetengenezwa') }}: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <!-- Summary Section -->
    @if($include_summary ?? true)
    <table style="width: 100%; border: none; margin: 20px 0;">
        <tr>
            <td style="width: 33%; border: none; padding: 5px;">
                <div class="summary-card income">
                    <div class="label">{{ __('Jumla ya Mapato') }}</div>
                    <div class="amount">TZS {{ number_format($total_income ?? 0, 2) }}</div>
                </div>
            </td>
            <td style="width: 33%; border: none; padding: 5px;">
                <div class="summary-card expense">
                    <div class="label">{{ __('Jumla ya Matumizi') }}</div>
                    <div class="amount">TZS {{ number_format($total_expense ?? 0, 2) }}</div>
                </div>
            </td>
            <td style="width: 33%; border: none; padding: 5px;">
                <div class="summary-card balance">
                    <div class="label">{{ __('Salio') }}</div>
                    <div class="amount">TZS {{ number_format(($total_income ?? 0) - ($total_expense ?? 0), 2) }}</div>
                </div>
            </td>
        </tr>
    </table>
    @endif

    <!-- Income Section -->
    @if(isset($income_data) && count($income_data) > 0)
    <div class="section-title">
        <i class="fas fa-arrow-down"></i> {{ __('Mapato (Michango)') }}
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 12%;">{{ __('Tarehe') }}</th>
                <th style="width: 18%;">{{ __('Kategoria') }}</th>
                <th style="width: 25%;">{{ __('Maelezo') }}</th>
                <th style="width: 20%;">{{ __('Mchangiaji') }}</th>
                <th style="width: 20%;" class="text-right">{{ __('Kiasi (TZS)') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($income_data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y') }}</td>
                <td>{{ $item['category'] ?? '-' }}</td>
                <td>{{ $item['description'] ?? '-' }}</td>
                <td>{{ $item['contributor'] ?? '-' }}</td>
                <td class="text-right">{{ number_format($item['amount'], 2) }}</td>
            </tr>
            @endforeach
            @if($include_totals ?? true)
            <tr class="table-total">
                <td colspan="5"><strong>{{ __('Jumla ya Mapato') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($total_income ?? 0, 2) }}</strong></td>
            </tr>
            @endif
        </tbody>
    </table>
    @endif

    <!-- Expense Section -->
    @if(isset($expense_data) && count($expense_data) > 0)
    <div class="section-title">
        <i class="fas fa-arrow-up"></i> {{ __('Matumizi') }}
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 15%;">{{ __('Tarehe') }}</th>
                <th style="width: 20%;">{{ __('Kategoria') }}</th>
                <th style="width: 40%;">{{ __('Maelezo') }}</th>
                <th style="width: 20%;" class="text-right">{{ __('Kiasi (TZS)') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expense_data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y') }}</td>
                <td>{{ $item['category'] ?? '-' }}</td>
                <td>{{ $item['description'] ?? '-' }}</td>
                <td class="text-right">{{ number_format($item['amount'], 2) }}</td>
            </tr>
            @endforeach
            @if($include_totals ?? true)
            <tr class="table-total">
                <td colspan="4"><strong>{{ __('Jumla ya Matumizi') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($total_expense ?? 0, 2) }}</strong></td>
            </tr>
            @endif
        </tbody>
    </table>
    @endif

    <!-- Final Summary -->
    @if($include_summary ?? true)
    <table style="width: 50%; margin: 30px auto; border: 2px solid #000;">
        <thead>
            <tr style="background: #000; color: #fff;">
                <th colspan="2" style="text-align: center; padding: 12px;">{{ __('MUHTASARI WA MWISHO') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 10px; font-weight: bold;">{{ __('Jumla ya Mapato') }}</td>
                <td style="padding: 10px; text-align: right;">TZS {{ number_format($total_income ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; font-weight: bold;">{{ __('Jumla ya Matumizi') }}</td>
                <td style="padding: 10px; text-align: right;">TZS {{ number_format($total_expense ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td style="padding: 12px; font-weight: bold; font-size: 12px;">{{ __('SALIO') }}</td>
                <td style="padding: 12px; text-align: right; font-weight: bold; font-size: 14px;">
                    TZS {{ number_format(($total_income ?? 0) - ($total_expense ?? 0), 2) }}
                </td>
            </tr>
        </tbody>
    </table>
    @endif

    <!-- Notes Section -->
    @if(isset($notes) && $notes)
    <div class="notes-section">
        <div class="notes-title">{{ __('Maelezo Mengine:') }}</div>
        <div class="notes-content">{{ $notes }}</div>
    </div>
    @endif

    <!-- Footer -->
    <div class="report-footer">
        <div class="footer-left">{{ $settings->company_name ?? 'Redeemed Gospel Church Inc. Tanzania' }}</div>
        <div class="footer-center">{{ __('Ripoti imetengenezwa na Mfumo wa Kanisa') }}</div>
        <div class="footer-right">{{ __('Ukurasa wa') }} <span class="page-number"></span></div>
    </div>
</body>
</html>
