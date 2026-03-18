<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Risiti - {{ $payment->receipt_number }}</title>
    <style>
        @page {
            margin: 10mm;
            size: A4 landscape;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #000;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100%;
        }

        /* Cheque Container - Fixed Size */
        .cheque-container {
            width: 720px;
            height: 320px;
            background: #fff;
            border: 2px solid #000;
            position: relative;
            overflow: hidden;
        }

        /* Inner Border */
        .cheque-container::before {
            content: '';
            position: absolute;
            top: 4px;
            left: 4px;
            right: 4px;
            bottom: 4px;
            border: 1px solid #999;
            pointer-events: none;
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 50px;
            font-weight: bold;
            color: rgba(0, 0, 0, 0.04);
            white-space: nowrap;
            pointer-events: none;
            z-index: 0;
        }

        /* Header */
        .cheque-header {
            background: #000;
            color: #fff;
            padding: 10px 15px;
            display: table;
            width: 100%;
        }

        .header-logo {
            display: table-cell;
            vertical-align: middle;
            width: 50px;
        }

        .header-logo img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            background: #fff;
        }

        .header-center {
            display: table-cell;
            vertical-align: middle;
            padding-left: 10px;
        }

        .header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 100px;
        }

        .church-name {
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .church-subtitle {
            font-size: 9px;
            opacity: 0.9;
        }

        .receipt-badge {
            background: #fff;
            color: #000;
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 9px;
            display: inline-block;
        }

        /* Receipt Strip */
        .receipt-strip {
            background: #e5e5e5;
            padding: 6px 15px;
            display: table;
            width: 100%;
            border-bottom: 1px dashed #999;
        }

        .strip-left {
            display: table-cell;
            vertical-align: middle;
        }

        .strip-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
        }

        .receipt-number {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            font-weight: bold;
            color: #000;
            letter-spacing: 1px;
        }

        .receipt-date {
            font-size: 10px;
            color: #000;
            font-weight: 600;
        }

        /* Main Body */
        .cheque-body {
            padding: 12px 15px;
            position: relative;
            z-index: 1;
            display: table;
            width: 100%;
        }

        .body-left {
            display: table-cell;
            width: 65%;
            vertical-align: top;
            padding-right: 15px;
        }

        .body-right {
            display: table-cell;
            width: 35%;
            vertical-align: top;
            border-left: 1px solid #ccc;
            padding-left: 15px;
        }

        /* Pay To Section */
        .pay-to-label {
            font-size: 8px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .pay-to-name {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
            margin-bottom: 6px;
        }

        .member-info {
            font-size: 9px;
            color: #333;
            margin-bottom: 8px;
        }

        .member-info span {
            margin-right: 15px;
        }

        /* Amount Box */
        .amount-box {
            background: #f5f5f5;
            border: 2px solid #000;
            padding: 8px 12px;
            margin-bottom: 8px;
            display: table;
            width: 100%;
        }

        .amount-left {
            display: table-cell;
            vertical-align: middle;
            width: 55%;
        }

        .amount-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 45%;
        }

        .amount-words {
            font-size: 8px;
            color: #666;
        }

        .amount-words-value {
            font-size: 10px;
            font-weight: 600;
            font-style: italic;
        }

        .amount-figure {
            background: #fff;
            border: 1px solid #000;
            padding: 5px 10px;
            display: inline-block;
        }

        .amount-currency {
            font-size: 8px;
            color: #666;
        }

        .amount-value {
            font-size: 16px;
            font-weight: bold;
            font-family: 'Courier New', monospace;
        }

        /* Details */
        .details-row {
            font-size: 9px;
            margin-bottom: 3px;
        }

        .details-label {
            color: #666;
            display: inline-block;
            width: 80px;
        }

        .details-value {
            font-weight: 600;
            color: #000;
        }

        /* Pledge Summary */
        .pledge-box {
            background: #f5f5f5;
            border: 1px solid #999;
            padding: 8px;
            margin-bottom: 8px;
        }

        .pledge-title {
            font-size: 8px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .pledge-row {
            display: table;
            width: 100%;
            font-size: 9px;
            margin-bottom: 2px;
        }

        .pledge-label {
            display: table-cell;
            color: #666;
            width: 50%;
        }

        .pledge-value {
            display: table-cell;
            font-weight: bold;
            text-align: right;
        }

        /* QR Code Section */
        .qr-section {
            text-align: center;
            margin-top: 5px;
        }

        .qr-code {
            width: 65px;
            height: 65px;
            display: inline-block;
            background: #fff;
            padding: 3px;
            border: 1px solid #999;
        }

        .qr-code img,
        .qr-code svg {
            width: 59px !important;
            height: 59px !important;
        }

        .qr-label {
            font-size: 7px;
            color: #666;
            margin-top: 3px;
        }

        /* Footer */
        .cheque-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: #f5f5f5;
            padding: 8px 15px;
            border-top: 1px solid #ccc;
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            vertical-align: middle;
            width: 50%;
        }

        .footer-center {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            width: 25%;
        }

        .footer-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 25%;
        }

        .footer-blessing {
            font-size: 9px;
            font-weight: 600;
            font-style: italic;
        }

        .footer-text {
            font-size: 7px;
            color: #666;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 100px;
            margin: 0 auto;
            padding-top: 3px;
            font-size: 7px;
            color: #666;
            text-align: center;
        }

        /* Security Strip */
        .security-strip {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: repeating-linear-gradient(
                90deg,
                #000 0px,
                #000 2px,
                transparent 2px,
                transparent 4px
            );
            height: 5px;
        }
    </style>
</head>
<body>
    <div class="cheque-container">
        <!-- Watermark -->
        <div class="watermark">RGC</div>

        <!-- Header -->
        <div class="cheque-header">
            <div class="header-logo">
                <img src="{{ public_path('images/RGC_logo.png') }}" alt="RGC Logo">
            </div>
            <div class="header-center">
                <div class="church-name">KANISA LA KIINJILI LA KILUTHERI TANZANIA</div>
                <div class="church-subtitle">USHARIKA WA RGC - DAR ES SALAAM</div>
            </div>
            <div class="header-right">
                <div class="receipt-badge">RISITI RASMI</div>
            </div>
        </div>

        <!-- Receipt Strip -->
        <div class="receipt-strip">
            <div class="strip-left">
                <span style="font-size: 8px; color: #666;">NAMBA:</span>
                <span class="receipt-number">{{ $payment->receipt_number }}</span>
            </div>
            <div class="strip-right">
                <span class="receipt-date">{{ $payment->payment_date->format('d/m/Y') }}</span>
            </div>
        </div>

        <!-- Main Body -->
        <div class="cheque-body">
            <div class="body-left">
                <!-- Pay To -->
                <div class="pay-to-label">Imelipwa na</div>
                <div class="pay-to-name">{{ strtoupper($member->full_name) }}</div>
                <div class="member-info">
                    <span><strong>Namba:</strong> {{ $member->member_number }}</span>
                    <span><strong>Bahasha:</strong> {{ $member->envelope_number ?? 'N/A' }}</span>
                    @if($member->phone)<span><strong>Simu:</strong> {{ $member->phone }}</span>@endif
                </div>

                <!-- Amount Box -->
                <div class="amount-box">
                    <div class="amount-left">
                        <div class="amount-words">Kiasi kwa Maneno:</div>
                        <div class="amount-words-value">
                            @php
                                $amount = $payment->amount;
                                $formatter = new NumberFormatter('sw_TZ', NumberFormatter::SPELLOUT);
                                $amountWords = ucfirst($formatter->format(floor($amount))) . ' Shilingi';
                            @endphp
                            {{ $amountWords }} pekee
                        </div>
                    </div>
                    <div class="amount-right">
                        <div class="amount-figure">
                            <span class="amount-currency">TZS</span>
                            <span class="amount-value">{{ number_format($payment->amount, 0) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="details-row">
                    <span class="details-label">Aina ya Malipo:</span>
                    <span class="details-value">{{ $payment->pledge->pledge_type }}</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Njia ya Malipo:</span>
                    <span class="details-value">{{ $payment->payment_method ?? 'Taslimu' }}</span>
                </div>
                @if($payment->reference_number)
                <div class="details-row">
                    <span class="details-label">Kumbukumbu:</span>
                    <span class="details-value">{{ $payment->reference_number }}</span>
                </div>
                @endif
            </div>

            <div class="body-right">
                <!-- Pledge Summary -->
                <div class="pledge-box">
                    <div class="pledge-title">Muhtasari wa Ahadi</div>
                    <div class="pledge-row">
                        <span class="pledge-label">Jumla ya Ahadi:</span>
                        <span class="pledge-value">TZS {{ number_format($payment->pledge->amount, 0) }}</span>
                    </div>
                    <div class="pledge-row">
                        <span class="pledge-label">Kilicholipwa:</span>
                        <span class="pledge-value">TZS {{ number_format($payment->pledge->amount_paid, 0) }}</span>
                    </div>
                    <div class="pledge-row">
                        <span class="pledge-label">Salio:</span>
                        <span class="pledge-value">TZS {{ number_format($payment->pledge->remaining_amount, 0) }}</span>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="qr-section">
                    <div class="qr-code">
                        @php
                            $qrData = "{$payment->receipt_number}|{$member->member_number}|{$payment->amount}";
                        @endphp
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(59)->style('square')->generate($qrData) !!}
                    </div>
                    <div class="qr-label">Scan kuthibitisha</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="cheque-footer">
            <div class="footer-left">
                <div class="footer-blessing">Asante kwa mchango wako. Mungu akubariki!</div>
                <div class="footer-text">Imeundwa: {{ now()->format('d/m/Y H:i') }}</div>
            </div>
            <div class="footer-center">
                <div class="signature-line">Saini/Muhuri</div>
            </div>
            <div class="footer-right">
                <div class="footer-text">RGC Usharika wa RGC</div>
                <div class="footer-text">Dar es Salaam, Tanzania</div>
            </div>
        </div>

        <!-- Security Strip -->
        <div class="security-strip"></div>
    </div>
</body>
</html>
