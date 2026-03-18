<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ripoti ya Ahadi - {{ $periodLabel }}</title>
    <style>
        @page {
            margin: 10mm;
            size: A4;
            orientation: landscape;
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
            padding: 2rem;
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
            font-size: 16px;
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
            background: #360958;
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
            background: #360958;
            color: white;
            padding: 8px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table th {
            background: #360958;
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

        .status-paid {
            color: #16A34A;
            font-weight: bold;
        }

        .status-pending {
            color: #DC2626;
            font-weight: bold;
        }

        .amount {
            text-align: right;
            font-family: 'Courier New', monospace;
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
            @if(file_exists(public_path('images/RGC_logo.png')))
                <img src="{{ asset('images/RGC_logo.png') }}" alt="Church Logo" class="church-logo">
            @endif
        </div>
        <div class="header-center">
            <div class="church-name">{{ $churchName ?? 'RGC MAKABE RGC' }}</div>
            <div class="church-address">{{ $address ?? 'P.O. Box 123, Makabe' }}</div>
            <div class="church-contact">{{ $phone ?? '+255 123 456 789' }} | {{ $email ?? 'makabe@RGC.go.tz' }}</div>
        </div>
        <div class="header-right"></div>
    </div>

    <!-- Report Title -->
    <div class="report-title">
        <h1>RIPOTI YA AHADI</h1>
        <div class="report-period">Kipindi: {{ $periodLabel }}</div>
        <div class="report-date">Imetengenezwa: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-title">MUHTASARI WA AHADI</div>
        <div class="summary-row">
            <span>Jumla ya Ahadi:</span>
            <span>{{ number_format($totalPledged, 2) }} TZS</span>
        </div>
        <div class="summary-row">
            <span>Jumla Iliyolipwa:</span>
            <span>{{ number_format($totalPaid, 2) }} TZS</span>
        </div>
        <div class="summary-row">
            <span>Jumla ya Baki:</span>
            <span>{{ number_format($totalBalance, 2) }} TZS</span>
        </div>
        <div class="summary-row">
            <span>Jumla ya Ahadi:</span>
            <span>{{ $ahadiData->count() }} ahadi</span>
        </div>
    </div>

    <!-- Ahadi Table -->
    <div class="section-title">ORODHA YA AHADI</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">Na.</th>
                <th width="12%">Tarehe ya Ahadi</th>
                <th width="25%">Mwanachama</th>
                <th width="12%">Simu</th>
                <th width="15%">Kiasi Ahadi (TZS)</th>
                <th width="15%">Kilipwa (TZS)</th>
                <th width="13%">Baki (TZS)</th>
                <th width="8%">Hali</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ahadiData as $index => $ahadi)
                @php
                    $paidAmount = $ahadi->payments ? $ahadi->payments->sum('amount') : 0;
                    $balance = floatval($ahadi->amount) - $paidAmount;
                    $status = $balance <= 0 ? 'Imelipwa' : 'Bado';
                @endphp
                <tr>
                    <td align="center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($ahadi->pledge_date)->format('d/m/Y') }}</td>
                    <td>{{ $ahadi->member ? $ahadi->member->first_name . ' ' . $ahadi->member->last_name : '-' }}</td>
                    <td>{{ $ahadi->member ? $ahadi->member->phone : '-' }}</td>
                    <td class="amount">{{ number_format(floatval($ahadi->amount), 2) }}</td>
                    <td class="amount">{{ number_format($paidAmount, 2) }}</td>
                    <td class="amount">{{ number_format(max(0, $balance), 2) }}</td>
                    <td align="center">
                        <span class="{{ $status === 'Imelipwa' ? 'status-paid' : 'status-pending' }}">
                            {{ $status }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" align="center">
                        <p>Hakuna ahadi zilizopatikana kwa kipindi hiki</p>
                    </td>
                </tr>
            @endforelse
            
            @if(!$ahadiData->isEmpty())
            <tr class="total-row">
                <td colspan="4" align="right"><strong>JUMLA KUU:</strong></td>
                <td class="amount">{{ number_format($totalPledged, 2) }}</td>
                <td class="amount">{{ number_format($totalPaid, 2) }}</td>
                <td class="amount">{{ number_format($totalBalance, 2) }}</td>
                <td></td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- Footer -->
    <div class="report-footer">
        <p>Ripoti hii imetengenezwa kiotomatiki kwaajili ya matumizi ya ndani | {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
