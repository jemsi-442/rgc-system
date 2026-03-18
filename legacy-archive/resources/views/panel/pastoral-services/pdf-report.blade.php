<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ripoti ya Huduma za Kichungaji - {{ $periodLabel }}</title>
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
            font-size: 12px;
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
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto 20px;
            font-size: 10px;
        }

        table.data-table th {
            background: #000;
            color: #fff;
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #000;
        }

        table.data-table td {
            padding: 8px;
            border: 1px solid #000;
            vertical-align: middle;
        }

        .member-name {
            font-weight: bold;
        }

        .member-number {
            font-size: 9px;
            color: #555;
        }

        .service-number {
            font-family: "Courier New", monospace;
            font-size: 9px;
        }

        /* Status Text */
        .status-text {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }

        /* Statistics Table */
        table.stats-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto 20px;
            font-size: 11px;
        }

        table.stats-table th {
            background: #000;
            color: #fff;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #000;
        }

        table.stats-table td {
            padding: 12px 10px;
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            display: block;
            margin-bottom: 3px;
        }

        .stat-label {
            font-size: 9px;
            text-transform: uppercase;
        }

        /* Service Type Summary Table */
        table.type-table {
            width: 60%;
            border-collapse: collapse;
            margin: 0 auto 20px;
            font-size: 11px;
        }

        table.type-table th {
            background: #000;
            color: #fff;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #000;
        }

        table.type-table td {
            padding: 8px 10px;
            border: 1px solid #000;
        }

        table.type-table .total-row {
            font-weight: bold;
        }

        /* Yearly Stats Box */
        .yearly-stats-box {
            border: 2px solid #000;
            padding: 15px;
            margin-top: 25px;
        }

        .yearly-stats-title {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #000;
        }

        /* Yearly Type Grid */
        table.yearly-type-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 10px;
        }

        table.yearly-type-table th {
            background: #000;
            color: #fff;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #000;
        }

        table.yearly-type-table td {
            padding: 10px;
            border: 1px solid #000;
            text-align: center;
        }

        .yearly-type-value {
            font-size: 18px;
            font-weight: bold;
        }

        .yearly-type-label {
            font-size: 9px;
            margin-top: 3px;
        }

        /* No Data Message */
        .no-data {
            text-align: center;
            padding: 30px;
            font-style: italic;
            border: 1px solid #000;
            margin: 20px auto;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10px 5rem;
            border-top: 2px solid #000;
            font-size: 9px;
        }

        .footer-content {
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            width: 50%;
            vertical-align: middle;
        }

        .footer-right {
            display: table-cell;
            width: 50%;
            vertical-align: middle;
            text-align: right;
        }

        .page-number:before {
            content: "Ukurasa " counter(page) " / " counter(pages);
        }
    </style>
</head>
<body>
    <!-- Header with Logo -->
    <div class="report-header">
        <div class="header-left">
            @if(file_exists(public_path('images/RGC_logo.png')))
                <img src="{{ public_path('images/RGC_logo.png') }}" alt="Logo" class="logo">
            @endif
        </div>
        <div class="header-center">
            <div class="church-name">{{ $churchName }}</div>
            <div class="diocese">{{ $diocese }}</div>
            <div class="parish">{{ $parish }}</div>
        </div>
        <div class="header-right">
            <div class="report-meta">
                <strong>Tarehe:</strong> {{ $generatedAt }}<br>
                <strong>Imetayarishwa na:</strong><br>{{ $generatedBy }}
            </div>
        </div>
    </div>

    <!-- Report Title Box -->
    <div class="report-title-box">
        <div class="report-title">Ripoti ya Huduma za Kichungaji</div>
        <div class="report-subtitle">Kipindi: {{ $periodLabel }} | Jumla ya Huduma: {{ $stats['total'] }}</div>
    </div>

    <!-- Current Period Statistics -->
    <div class="section-title">Muhtasari wa Kipindi: {{ $periodLabel }}</div>
    <table class="stats-table">
        <tr>
            <td>
                <span class="stat-value">{{ $stats['total'] }}</span>
                <span class="stat-label">Jumla</span>
            </td>
            <td>
                <span class="stat-value">{{ $stats['completed'] }}</span>
                <span class="stat-label">Zimekamilika</span>
            </td>
            <td>
                <span class="stat-value">{{ $stats['approved'] }}</span>
                <span class="stat-label">Zimeidhinishwa</span>
            </td>
            <td>
                <span class="stat-value">{{ $stats['pending'] }}</span>
                <span class="stat-label">Zinasubiri</span>
            </td>
            <td>
                <span class="stat-value">{{ $stats['rejected'] }}</span>
                <span class="stat-label">Zimekataliwa</span>
            </td>
            <td>
                <span class="stat-value">{{ $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100) : 0 }}%</span>
                <span class="stat-label">Kiwango Kukamilika</span>
            </td>
        </tr>
    </table>

    <!-- Main Services Table -->
    <div class="section-title">Orodha ya Waumini Waliohudumiwa</div>

    @if($services->count() > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th style="width: 12%;">Namba ya Huduma</th>
                <th style="width: 22%;">Jina la Muumini</th>
                <th style="width: 13%;">Simu</th>
                <th style="width: 18%;">Aina ya Huduma</th>
                <th style="width: 10%;">Tarehe Ombi</th>
                <th style="width: 10%;">Tarehe Huduma</th>
                <th style="width: 10%;">Hali</th>
            </tr>
        </thead>
        <tbody>
            @foreach($services as $index => $service)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td><span class="service-number">{{ $service->service_number }}</span></td>
                <td>
                    <span class="member-name">{{ $service->member->first_name }} {{ $service->member->last_name }}</span><br>
                    <span class="member-number">{{ $service->member->member_number }}</span>
                </td>
                <td>{{ $service->member->phone ?? '-' }}</td>
                <td>{{ $service->service_type }}</td>
                <td>{{ $service->created_at->format('d/m/Y') }}</td>
                <td>{{ $service->preferred_date ? \Carbon\Carbon::parse($service->preferred_date)->format('d/m/Y') : '-' }}</td>
                <td>
                    <span class="status-text">{{ $service->status }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        Hakuna huduma za kichungaji zilizopatikana kwa kipindi hiki
    </div>
    @endif

    <!-- Services by Type Summary -->
    @if($servicesByType->count() > 0)
    <div class="section-title">Muhtasari kwa Aina ya Huduma ({{ $periodLabel }})</div>
    <table class="type-table">
        <thead>
            <tr>
                <th style="width: 70%;">Aina ya Huduma</th>
                <th style="width: 30%; text-align: center;">Idadi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($servicesByType as $type => $count)
            <tr>
                <td>{{ $type }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $count }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td>JUMLA</td>
                <td style="text-align: center;">{{ $servicesByType->sum() }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    <!-- Yearly Statistics (Always Show) -->
    <div class="yearly-stats-box">
        <div class="yearly-stats-title">Takwimu za Mwaka {{ $currentYear }} (Mwaka Mzima)</div>

        <table class="stats-table">
            <tr>
                <td>
                    <span class="stat-value">{{ $yearlyStats['total'] }}</span>
                    <span class="stat-label">Jumla Mwaka</span>
                </td>
                <td>
                    <span class="stat-value">{{ $yearlyStats['completed'] }}</span>
                    <span class="stat-label">Zimekamilika</span>
                </td>
                <td>
                    <span class="stat-value">{{ $yearlyStats['approved'] }}</span>
                    <span class="stat-label">Zimeidhinishwa</span>
                </td>
                <td>
                    <span class="stat-value">{{ $yearlyStats['pending'] }}</span>
                    <span class="stat-label">Zinasubiri</span>
                </td>
                <td>
                    <span class="stat-value">{{ $yearlyStats['rejected'] }}</span>
                    <span class="stat-label">Zimekataliwa</span>
                </td>
                <td>
                    <span class="stat-value">{{ $yearlyStats['total'] > 0 ? round(($yearlyStats['completed'] / $yearlyStats['total']) * 100) : 0 }}%</span>
                    <span class="stat-label">Asilimia Kukamilika</span>
                </td>
            </tr>
        </table>

        @if($yearlyServicesByType->count() > 0)
        <div style="margin-top: 15px; text-align: center; font-weight: bold; font-size: 11px; border-bottom: 1px solid #000; padding-bottom: 8px; margin-bottom: 10px;">
            Huduma kwa Aina (Mwaka {{ $currentYear }})
        </div>
        <table class="yearly-type-table">
            <tr>
                @foreach($yearlyServicesByType as $service)
                <th>{{ $service->service_type }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach($yearlyServicesByType as $service)
                <td>
                    <div class="yearly-type-value">{{ $service->total }}</div>
                </td>
                @endforeach
            </tr>
        </table>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-content">
            <div class="footer-left">
                <strong>{{ $churchName }}</strong> | Mfumo wa Usimamizi wa Kanisa<br>
                Ripoti imetayarishwa tarehe {{ $generatedAt }} na {{ $generatedBy }}
            </div>
            <div class="footer-right">
                <span class="page-number"></span>
            </div>
        </div>
    </div>
</body>
</html>
