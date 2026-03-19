<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Ripoti ya Fedha' }} - {{ $settings->company_name ?? 'Redeemed Gospel Church Inc. Tanzania' }}</title>
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
                <img src="{{ asset('images/rgc_logo.png') }}" alt="RGC Logo" class="church-logo">
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
        <h1>{{ $title ?? 'RIPOTI YA FEDHA' }}</h1>
        <div class="report-period">Kipindi: {{ $period_text ?? 'Kila Mwezi' }}</div>
        <div class="report-date">Imetengenezwa: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-title">MUHTASARI WA RIPOTI</div>
        <div class="summary-row">
            <span>Jumla ya Mapato:</span>
            <span>{{ number_format($total_income ?? 0, 2) }} TZS</span>
        </div>
        <div class="summary-row">
            <span>Jumla ya Matumizi:</span>
            <span>{{ number_format($total_expense ?? 0, 2) }} TZS</span>
        </div>
        <div class="summary-row">
            <span>Salio:</span>
            <span>{{ number_format(($total_income ?? 0) - ($total_expense ?? 0), 2) }} TZS</span>
        </div>
    </div>

    <!-- Income Section -->
    @if(!empty($income_data))
    <div class="section-title">MAPATO</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="15%">Tarehe</th>
                <th width="20%">Kategoria</th>
                <th width="30%">Maelezo</th>
                <th width="20%">Mchangiaji</th>
                <th width="15%">Kiasi (TZS)</th>
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
                <td colspan="4" align="right"><strong>JUMLA YA MAPATO:</strong></td>
                <td align="right">{{ number_format($total_income ?? 0, 2) }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    <!-- Expense Section -->
    @if(!empty($expense_data))
    <div class="section-title">MATUMIZI</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="15%">Tarehe</th>
                <th width="25%">Kategoria</th>
                <th width="45%">Maelezo</th>
                <th width="15%">Kiasi (TZS)</th>
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
                <td colspan="3" align="right"><strong>JUMLA YA MATUMIZI:</strong></td>
                <td align="right">{{ number_format($total_expense ?? 0, 2) }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    <!-- Footer -->
    <div class="report-footer">
        <p>Ripoti hii imetengenezwa kiotomatiki kwaajili ya matumizi ya ndani | {{ now()->format('d/m/Y H:i') }}</p>
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

        .church-info {
            text-align: center;
        }

        .church-name {
            font-size: 22px;
            font-weight: bold;
            color: #c00000;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .church-address {
            font-size: 11px;
            color: #666;
            margin-top: 3px;
        }

        .church-contact {
            font-size: 10px;
            color: #888;
            margin-top: 2px;
        }

        /* Report Title */
        .report-title {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background: linear-gradient(135deg, #c00000 0%, #8f1111 100%);
            color: #fff;
            border-radius: 8px;
        }

        .report-title h1 {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .report-title .period {
            font-size: 12px;
            opacity: 0.9;
        }

        .report-title .generated {
            font-size: 10px;
            opacity: 0.7;
            margin-top: 5px;
        }

        /* Summary Cards */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 20px 0;
        }

        .summary-card {
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }

        .summary-card.income {
            background: #e8f5e9;
            border: 2px solid #4caf50;
        }

        .summary-card.expense {
            background: #ffebee;
            border: 2px solid #f44336;
        }

        .summary-card.balance {
            background: #e3f2fd;
            border: 2px solid #2196f3;
        }

        .summary-card .label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .summary-card .amount {
            font-size: 18px;
            font-weight: bold;
        }

        .summary-card.income .amount { color: #2e7d32; }
        .summary-card.expense .amount { color: #c62828; }
        .summary-card.balance .amount { color: #1565c0; }

        /* Section Title */
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #c00000;
            margin: 25px 0 12px;
            padding: 10px 15px;
            background: #f5f5f5;
            border-left: 4px solid #c00000;
            border-radius: 0 5px 5px 0;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background: #c00000;
            color: #fff;
        }

        table th {
            padding: 12px 10px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
        }

        table td {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .text-right {
            text-align: right;
        }

        .amount-positive {
            color: #2e7d32;
            font-weight: 600;
        }

        .amount-negative {
            color: #c62828;
            font-weight: 600;
        }

        .table-total {
            background: #f0f0f0 !important;
            font-weight: bold;
        }

        .table-total td {
            border-top: 2px solid #c00000;
            padding: 12px 10px;
        }

        /* Final Summary */
        .final-summary {
            width: 50%;
            margin: 30px auto;
            border: 2px solid #c00000;
            border-radius: 8px;
            overflow: hidden;
        }

        .final-summary-header {
            background: #c00000;
            color: #fff;
            text-align: center;
            padding: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .final-summary-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .final-summary-row:last-child {
            border-bottom: none;
            background: #f5f5f5;
        }

        .final-summary-row.balance {
            font-size: 14px;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
        }

        .signature-title {
            font-size: 12px;
            font-weight: bold;
            color: #c00000;
            margin-bottom: 30px;
            text-transform: uppercase;
        }

        .signatures-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .signature-box {
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 10px;
        }

        .signature-name {
            font-weight: bold;
            font-size: 11px;
        }

        .signature-title-text {
            font-size: 10px;
            color: #666;
        }

        .signature-date {
            font-size: 9px;
            color: #888;
            margin-top: 5px;
        }

        /* Print Button */
        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }

        .print-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .print-btn.primary {
            background: #c00000;
            color: #fff;
        }

        .print-btn.primary:hover {
            background: #8f1111;
        }

        .print-btn.secondary {
            background: #f0f0f0;
            color: #333;
        }

        .print-btn.secondary:hover {
            background: #e0e0e0;
        }

        /* Footer */
        .report-footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #888;
            display: flex;
            justify-content: space-between;
        }

        @media print {
            .print-controls {
                display: none !important;
            }

            body {
                padding: 0;
            }

            .report-title {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            table thead {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .summary-card {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Print Controls -->
    <div class="print-controls">
        <button class="print-btn primary" onclick="window.print()">
            <i class="fas fa-print"></i> Chapisha
        </button>
        <button class="print-btn secondary" onclick="window.close()">
            <i class="fas fa-times"></i> Funga
        </button>
    </div>

    <!-- Report Header with Logo -->
    <div class="report-header">
        <div class="logo-container">
            <img src="{{ asset('images/rgc_logo.png') }}" alt="RGC Logo" class="logo">
            <div class="church-info">
                <div class="church-name">{{ $settings->company_name ?? 'Redeemed Gospel Church Inc. Tanzania' }}</div>
                <div class="church-address">{{ $settings->address ?? 'Dar es Salaam, Tanzania' }}</div>
                <div class="church-contact">
                    Simu: {{ $settings->phone ?? '+255 XXX XXX XXX' }} |
                    Email: {{ $settings->email ?? 'noreply@rgc.or.tz' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Report Title -->
    <div class="report-title">
        <h1>{{ $title ?? 'Ripoti ya Mapato na Matumizi' }}</h1>
        <div class="period">{{ $period_text ?? '' }}</div>
        <div class="generated">Imetengenezwa: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <!-- Summary Section -->
    <div class="summary-grid">
        <div class="summary-card income">
            <div class="label">Jumla ya Mapato</div>
            <div class="amount">TZS {{ number_format($total_income ?? 0, 2) }}</div>
        </div>
        <div class="summary-card expense">
            <div class="label">Jumla ya Matumizi</div>
            <div class="amount">TZS {{ number_format($total_expense ?? 0, 2) }}</div>
        </div>
        <div class="summary-card balance">
            <div class="label">Salio</div>
            <div class="amount">TZS {{ number_format(($total_income ?? 0) - ($total_expense ?? 0), 2) }}</div>
        </div>
    </div>

    <!-- Income Section -->
    @if(isset($income_data) && count($income_data) > 0)
    <div class="section-title">
        <i class="fas fa-arrow-down"></i> Mapato (Michango)
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 12%;">Tarehe</th>
                <th style="width: 18%;">Kategoria</th>
                <th style="width: 25%;">Maelezo</th>
                <th style="width: 20%;">Mchangiaji</th>
                <th style="width: 20%;" class="text-right">Kiasi (TZS)</th>
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
                <td class="text-right amount-positive">{{ number_format($item['amount'], 2) }}</td>
            </tr>
            @endforeach
            <tr class="table-total">
                <td colspan="5"><strong>Jumla ya Mapato</strong></td>
                <td class="text-right amount-positive"><strong>{{ number_format($total_income ?? 0, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>
    @endif

    <!-- Expense Section -->
    @if(isset($expense_data) && count($expense_data) > 0)
    <div class="section-title">
        <i class="fas fa-arrow-up"></i> Matumizi
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 15%;">Tarehe</th>
                <th style="width: 20%;">Kategoria</th>
                <th style="width: 40%;">Maelezo</th>
                <th style="width: 20%;" class="text-right">Kiasi (TZS)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expense_data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y') }}</td>
                <td>{{ $item['category'] ?? '-' }}</td>
                <td>{{ $item['description'] ?? '-' }}</td>
                <td class="text-right amount-negative">{{ number_format($item['amount'], 2) }}</td>
            </tr>
            @endforeach
            <tr class="table-total">
                <td colspan="4"><strong>Jumla ya Matumizi</strong></td>
                <td class="text-right amount-negative"><strong>{{ number_format($total_expense ?? 0, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>
    @endif

    <!-- Final Summary -->
    <div class="final-summary">
        <div class="final-summary-header">Muhtasari wa Mwisho</div>
        <div class="final-summary-row">
            <span><strong>Jumla ya Mapato</strong></span>
            <span class="amount-positive"><strong>TZS {{ number_format($total_income ?? 0, 2) }}</strong></span>
        </div>
        <div class="final-summary-row">
            <span><strong>Jumla ya Matumizi</strong></span>
            <span class="amount-negative"><strong>TZS {{ number_format($total_expense ?? 0, 2) }}</strong></span>
        </div>
        <div class="final-summary-row balance">
            <span><strong>SALIO</strong></span>
            <span style="font-weight: bold; font-size: 16px; color: {{ ($total_income ?? 0) - ($total_expense ?? 0) >= 0 ? '#2e7d32' : '#c62828' }};">
                <strong>TZS {{ number_format(($total_income ?? 0) - ($total_expense ?? 0), 2) }}</strong>
            </span>
        </div>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-title">Thibitisho la Ripoti</div>
        <div class="signatures-grid">
            <div class="signature-box">
                <div class="signature-line">
                    <div class="signature-name">________________________</div>
                    <div class="signature-title-text">Mhasibu</div>
                    <div class="signature-date">Tarehe: _______________</div>
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    <div class="signature-name">________________________</div>
                    <div class="signature-title-text">Mwenyekiti wa Fedha</div>
                    <div class="signature-date">Tarehe: _______________</div>
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    <div class="signature-name">________________________</div>
                    <div class="signature-title-text">Mchungaji Mkuu</div>
                    <div class="signature-date">Tarehe: _______________</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="report-footer">
        <div>{{ $settings->company_name ?? 'Redeemed Gospel Church Inc. Tanzania' }}</div>
        <div>Ripoti imetengenezwa na Mfumo wa Kanisa</div>
        <div>{{ now()->format('d/m/Y H:i') }}</div>
    </div>
</body>
</html>
