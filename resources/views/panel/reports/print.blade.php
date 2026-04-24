<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? __('Ripoti ya Fedha') }} - {{ $settings->company_name ?? 'Redeemed Gospel Church Inc. Tanzania' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/rgc_logo.png') }}">
    <style>
        @page {
            margin: 10mm;
            size: A4;
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
        }

        .church-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }

        .church-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .church-address {
            font-size: 10px;
            margin-bottom: 3px;
        }

        .church-contact {
            font-size: 10px;
        }

        /* Report Title */
        .report-title {
            text-align: center;
            margin: 25px 0;
            padding: 15px;
            background: #f5f5f5;
            border: 1px solid #ddd;
        }

        .report-title h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .report-period {
            font-size: 12px;
            margin-bottom: 5px;
        }

        .report-date {
            font-size: 10px;
            color: #666;
        }

        /* Summary Section */
        .summary-section {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #ddd;
        }

        .summary-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
            background: #c00000;
            color: white;
            padding: 8px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }

        .summary-row:last-child {
            border-bottom: none;
            font-weight: bold;
            background: #e0e0e0;
            margin-top: 5px;
            padding: 8px 5px;
        }

        /* Table Styles */
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            text-align: center;
            background: #c00000;
            color: white;
            padding: 8px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table th {
            background: #c00000;
            color: white;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #000;
            font-size: 10px;
        }

        .data-table td {
            padding: 6px 8px;
            border: 1px solid #000;
            vertical-align: top;
        }

        .data-table tr:nth-child(even) {
            background: #f9f9f9;
        }

        .total-row {
            background: #e0e0e0 !important;
            font-weight: bold;
        }

        .total-row td {
            border-top: 2px solid #000;
        }

        /* Print Styles */
        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .page-break {
                page-break-before: always;
            }
        }

        /* Footer */
        .report-footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="report-header">
        <div class="header-left">
            @if(file_exists(public_path('images/rgc_logo.png')))
                <img src="{{ asset('images/rgc_logo.png') }}" alt="{{ __('RGC Logo') }}" class="church-logo">
            @endif
        </div>
        <div class="header-center">
            <div class="church-name">{{ $settings->company_name ?? 'REDEEMED GOSPEL CHURCH INC. TANZANIA' }}</div>
            <div class="church-address">{{ $settings->address ?? 'Toangoma, Temeke, Dar es Salaam' }}</div>
            <div class="church-contact">{{ $settings->phone ?? '+255 123 456 789' }} | {{ $settings->email ?? 'noreply@rgc.or.tz' }}</div>
        </div>
        <div class="header-right"></div>
    </div>

    <!-- Report Title -->
    <div class="report-title">
        <h1>{{ $title ?? __('RIPOTI YA FEDHA') }}</h1>
        <div class="report-period">{{ __('Kipindi') }}: {{ $period_text ?? __('Kila Mwezi') }}</div>
        <div class="report-date">{{ __('Imetengenezwa') }}: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-title">{{ __('MUHTASARI WA RIPOTI') }}</div>
        <div class="summary-row">
            <span>{{ __('Jumla ya Mapato:') }}</span>
            <span>{{ number_format($total_income ?? 0, 2) }} TZS</span>
        </div>
        <div class="summary-row">
            <span>{{ __('Jumla ya Matumizi:') }}</span>
            <span>{{ number_format($total_expense ?? 0, 2) }} TZS</span>
        </div>
        <div class="summary-row">
            <span>{{ __('Salio:') }}</span>
            <span>{{ number_format(($total_income ?? 0) - ($total_expense ?? 0), 2) }} TZS</span>
        </div>
    </div>

    <!-- Income Section -->
    @if(!empty($income_data))
    <div class="section-title">{{ __('MAPATO') }}</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="15%">{{ __('Tarehe') }}</th>
                <th width="20%">{{ __('Kategoria') }}</th>
                <th width="30%">{{ __('Maelezo') }}</th>
                <th width="20%">{{ __('Mchangiaji') }}</th>
                <th width="15%">{{ __('Kiasi (TZS)') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($income_data as $item)
            <tr>
                <td>{{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y') }}</td>
                <td>{{ $item['category'] ?? '-' }}</td>
                <td>{{ $item['description'] ?? '-' }}</td>
                <td>{{ $item['contributor'] ?? '-' }}</td>
                <td align="right">{{ number_format($item['amount'] ?? 0, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" align="right"><strong>{{ __('JUMLA YA MAPATO:') }}</strong></td>
                <td align="right">{{ number_format($total_income ?? 0, 2) }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    <!-- Expense Section -->
    @if(!empty($expense_data))
    <div class="section-title">{{ __('MATUMIZI') }}</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="15%">{{ __('Tarehe') }}</th>
                <th width="25%">{{ __('Kategoria') }}</th>
                <th width="45%">{{ __('Maelezo') }}</th>
                <th width="15%">{{ __('Kiasi (TZS)') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expense_data as $item)
            <tr>
                <td>{{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y') }}</td>
                <td>{{ $item['category'] ?? '-' }}</td>
                <td>{{ $item['description'] ?? '-' }}</td>
                <td align="right">{{ number_format($item['amount'] ?? 0, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" align="right"><strong>{{ __('JUMLA YA MATUMIZI:') }}</strong></td>
                <td align="right">{{ number_format($total_expense ?? 0, 2) }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    <!-- Footer -->
    <div class="report-footer">
        <p>{{ __('Ripoti hii imetengenezwa kiotomatiki kwa ajili ya matumizi ya ndani') }} | {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
