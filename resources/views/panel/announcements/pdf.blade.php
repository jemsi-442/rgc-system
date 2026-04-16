<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $announcement->title }} - RGC</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #171717;
            font-size: 12px;
            line-height: 1.65;
            margin: 0;
            background: #ffffff;
        }

        .page {
            padding: 0 34px 30px;
        }

        .top-stripe {
            height: 12px;
            margin: 0 -34px 18px;
            background: #000000;
            border-bottom: 5px solid #c00000;
            box-shadow: inset 0 -2px 0 #ffd700;
        }

        .brand-shell {
            border: 1px solid #e6ded0;
            border-radius: 18px;
            background: #fffdf7;
            padding: 18px 22px;
            margin-bottom: 18px;
        }

        .brand-table {
            width: 100%;
            border-collapse: collapse;
        }

        .brand-table td {
            vertical-align: top;
        }

        .brand-logo-cell {
            width: 88px;
            padding-right: 16px;
        }

        .brand-logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
        }

        .brand-copy h1 {
            margin: 0;
            font-size: 24px;
            line-height: 1.15;
            color: #8f1111;
        }

        .brand-copy p {
            margin: 6px 0 0;
            color: #444;
        }

        .brand-copy .subline {
            margin-top: 10px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #666;
        }

        .brand-badge-cell {
            width: 168px;
            padding-left: 16px;
        }

        .brand-badge {
            border: 1px solid #e7d7a8;
            border-radius: 16px;
            background: #fff6d6;
            padding: 12px 14px;
            text-align: left;
        }

        .brand-badge strong {
            display: block;
            margin-bottom: 5px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #8f1111;
        }

        .brand-badge span {
            display: block;
            color: #444;
            font-size: 11px;
        }

        .summary-strip {
            margin-bottom: 18px;
            padding: 12px 14px;
            border-left: 4px solid #c00000;
            background: #fffaf0;
            color: #4b4b4b;
        }

        .chips {
            margin: 0 0 18px;
        }

        .chip {
            display: inline-block;
            margin: 0 6px 6px 0;
            padding: 5px 10px;
            border-radius: 999px;
            background: #f4f4f4;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .chip.primary {
            background: #fff4bf;
            color: #8f1111;
        }

        .chip.warn {
            background: #ffe5e5;
            color: #8f1111;
        }

        .hero-image {
            margin: 14px 0 22px;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #ddd;
            background: #fafafa;
        }

        .hero-image img {
            width: 100%;
            max-height: 360px;
            object-fit: cover;
        }

        .section-label {
            margin-top: 22px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #666;
        }

        .title {
            margin: 10px 0 14px;
            font-size: 28px;
            line-height: 1.2;
        }

        .body {
            white-space: pre-wrap;
            color: #222;
        }

        .meta-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 22px;
        }

        .meta-grid td {
            width: 50%;
            padding: 10px 0;
            vertical-align: top;
            border-top: 1px solid #ececec;
        }

        .meta-grid strong {
            display: block;
            margin-bottom: 4px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #666;
        }

        .qr-block {
            margin-top: 24px;
            padding: 16px;
            border: 1px solid #ececec;
            border-radius: 14px;
            background: #fcfcfc;
        }

        .qr-table {
            width: 100%;
            border-collapse: collapse;
        }

        .qr-table td {
            vertical-align: middle;
        }

        .qr-svg {
            width: 120px;
        }

        .qr-svg svg {
            width: 120px;
            height: 120px;
        }

        .qr-copy {
            padding-left: 16px;
        }

        .qr-copy strong {
            display: block;
            margin-bottom: 6px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #666;
        }

        .qr-copy p {
            margin: 0 0 8px;
            color: #444;
        }

        .qr-link {
            font-size: 10px;
            color: #444;
            word-break: break-all;
        }

        .signature-block {
            margin-top: 22px;
            padding: 16px 18px;
            border: 1px solid #ece6d6;
            border-radius: 14px;
            background: #fffdfa;
        }

        .signature-label {
            margin: 0 0 18px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #666;
        }

        .signature-line {
            width: 220px;
            border-top: 1px solid #444;
            margin-bottom: 8px;
        }

        .signature-name {
            font-weight: 700;
            color: #8f1111;
        }

        .signature-role {
            color: #444;
        }

        .footer {
            margin-top: 28px;
            padding-top: 14px;
            border-top: 1px solid #ececec;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="top-stripe"></div>

        <div class="brand-shell">
            <table class="brand-table">
                <tr>
                    @if($logoDataUri)
                        <td class="brand-logo-cell">
                            <img class="brand-logo" src="{{ $logoDataUri }}" alt="{{ __('RGC Logo') }}">
                        </td>
                    @endif
                    <td class="brand-copy">
                        <h1>{{ __('Redeemed Gospel Church Inc. Tanzania') }}</h1>
                        <p>{{ __('Official announcement export generated from the Redeemed Gospel Church Tanzania system.') }}</p>
                        <div class="subline">{{ __('Head Office: Toangoma, Temeke, Dar es Salaam') }}</div>
                    </td>
                    <td class="brand-badge-cell">
                        <div class="brand-badge">
                            <strong>{{ __('Official Circular') }}</strong>
                            <span>{{ __('Generated on :date', ['date' => now()->translatedFormat('d M Y H:i')]) }}</span>
                            <span>{{ $announcement->audienceLabel() }}</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="summary-strip">
            {{ __('Issued through the Redeemed Gospel Church Tanzania system.') }}
        </div>

        <div class="chips">
            <span class="chip primary">{{ $announcement->audienceLabel() }}</span>
            @if($announcement->hasPin())
                <span class="chip primary">{{ __('Pinned') }}</span>
            @endif
            @if($announcement->isArchived())
                <span class="chip">{{ __('Archived') }}</span>
            @endif
            @if($announcement->hasExpiry())
                <span class="chip {{ $announcement->isExpired() ? 'warn' : '' }}">{{ $announcement->isExpired() ? __('Expired') : __('Expires :date', ['date' => $announcement->expires_at->translatedFormat('d M Y')]) }}</span>
            @endif
        </div>

        <div class="section-label">{{ __('Announcement Details') }}</div>
        <div class="title">{{ $announcement->title }}</div>

        @if($imageDataUri)
            <div class="hero-image">
                <img src="{{ $imageDataUri }}" alt="{{ $announcement->title }}">
            </div>
        @endif

        <div class="section-label">{{ __('Full announcement') }}</div>
        <div class="body">{{ filled($announcement->body) ? $announcement->body : __('This announcement was published as an image-led update without additional body text.') }}</div>

        <table class="meta-grid">
            <tr>
                <td>
                    <strong>{{ __('Published') }}</strong>
                    {{ optional($announcement->created_at)->translatedFormat('d M Y, H:i') }}
                </td>
                <td>
                    <strong>{{ __('Created by') }}</strong>
                    {{ $announcement->creator?->name ?? __('System') }}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>{{ __('Visibility') }}</strong>
                    {{ $announcement->audienceLabel() }}
                </td>
                <td>
                    <strong>{{ __('Expiry Date') }}</strong>
                    {{ $announcement->hasExpiry() ? $announcement->expires_at->translatedFormat('d M Y') : __('No expiry date') }}
                </td>
            </tr>
            @if($announcement->region || $announcement->district || $announcement->branch)
                <tr>
                    <td>
                        <strong>{{ __('Region') }}</strong>
                        {{ $announcement->region?->name ?? '—' }}
                    </td>
                    <td>
                        <strong>{{ __('District') }}</strong>
                        {{ $announcement->district?->name ?? '—' }}
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>{{ __('Branch') }}</strong>
                        {{ $announcement->branch?->name ?? '—' }}
                    </td>
                    <td>
                        <strong>{{ __('Archive Status') }}</strong>
                        {{ $announcement->isArchived() ? __('Archived automatically after expiry') : __('Active announcement') }}
                    </td>
                </tr>
            @endif
        </table>

        <div class="qr-block">
            <table class="qr-table">
                <tr>
                    @if($qrCodeSvg)
                        <td class="qr-svg">{!! $qrCodeSvg !!}</td>
                    @endif
                    <td class="qr-copy">
                        <strong>{{ __('Online version') }}</strong>
                        @if($qrCodeSvg)
                            <p>{{ __('Scan to open the online announcement') }}</p>
                            <p>{{ __('Use this QR code to open the live version inside the RGC Tanzania system.') }}</p>
                        @else
                            <p>{{ __('Open the online announcement using the link below.') }}</p>
                        @endif
                        <div class="qr-link">{{ $announcementUrl }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="signature-block">
            <div class="signature-label">{{ __('Authorized by') }}</div>
            <div class="signature-line"></div>
            <div class="signature-name">{{ __('Office of the Super Admin') }}</div>
            <div class="signature-role">{{ __('Redeemed Gospel Church Inc. Tanzania') }}</div>
        </div>

        <div class="footer">
            {{ __('Generated on :date', ['date' => now()->translatedFormat('d M Y H:i')]) }}
        </div>
    </div>
</body>
</html>
