@extends('layouts.app')

@section('title', __('Offering Payment Status') . ' - RGC')

@section('content')
@php
    $returnUrl = auth()->check() ? route('giving.index') : route('home');
    $returnLabel = auth()->check() ? __('Back to my giving') : __('Return to homepage');
    $failedRecoveryCopy = auth()->check()
        ? __('If this payment expired or failed, return to your giving page and create a fresh request when you are ready.')
        : __('If this payment expired or failed, return to the church site and start a new giving request when you are ready.');
@endphp
<div class="mx-auto max-w-5xl space-y-6">
    <section class="card-rgc payment-status-shell payment-status-shell--hero {{ $payment->isCompleted() ? 'is-success' : ($payment->isFailed() ? 'is-failed' : 'is-pending') }}">
        <div class="payment-status-header">
            <div>
                <p class="section-kicker">{{ __('Offering Payment Status') }}</p>
                <h1 class="mt-3 text-3xl font-semibold">{{ $payment->statusLabel() }}</h1>
                <p class="mt-3 text-sm text-black/65">{{ __('Reference') }}: <span class="font-semibold text-black">{{ $payment->public_reference }}</span></p>
            </div>
            <span class="payment-status-badge is-{{ $payment->status }}">{{ $payment->statusLabel() }}</span>
        </div>

        <div class="stats-grid mt-6">
            <div class="stat-card"><span>{{ __('Branch') }}</span><strong>{{ $payment->branch?->name }}</strong></div>
            <div class="stat-card"><span>{{ __('Amount') }}</span><strong>TZS {{ number_format((float) $payment->amount, 2) }}</strong></div>
            <div class="stat-card"><span>{{ __('Payer') }}</span><strong>{{ $payment->maskedPayerName() }}</strong></div>
            <div class="stat-card"><span>{{ __('Giving type') }}</span><strong>{{ $payment->paymentTypeLabel() }}</strong></div>
        </div>

        @if($payment->isCompleted())
            <div class="announcement-callout mt-6 payment-success-callout">
                <p class="font-semibold text-black">{{ __('Payment confirmed successfully.') }}</p>
                <p class="mt-2 text-sm text-black/70">{{ __('The offering has already been posted to the branch ledger and is ready for reporting.') }}</p>
                <p class="mt-2 text-sm text-black/70">{{ __('Thank God with joy for this completed giving and for the work it will support in the church.') }}</p>
                <div class="action-tile-grid mt-4">
                    <a class="action-tile is-primary" href="{{ $payment->temporaryPublicReceiptUrl() }}">
                        <span class="action-tile-icon">@include('partials.ui.icon', ['name' => 'archive'])</span>
                        <strong>{{ __('Download receipt PDF') }}</strong>
                        <p>{{ __('Keep a clean record of this confirmed giving.') }}</p>
                    </a>
                    <button class="action-tile" type="button" data-copy-text="{{ $payment->public_reference }}">
                        <span class="action-tile-icon">@include('partials.ui.icon', ['name' => 'archive'])</span>
                        <strong>{{ __('Copy reference') }}</strong>
                        <p>{{ __('Keep the payment reference ready for support or follow-up.') }}</p>
                    </button>
                    <button class="action-tile" type="button" data-share-link="{{ $payment->publicStatusUrl() }}" data-share-title="{{ __('Offering Payment Status') }}">
                        <span class="action-tile-icon">@include('partials.ui.icon', ['name' => 'megaphone'])</span>
                        <strong>{{ __('Share status page') }}</strong>
                        <p>{{ __('Send this status page to the giver or branch team.') }}</p>
                    </button>
                    @auth
                        <a class="action-tile" href="{{ route('giving.index') }}">
                            <span class="action-tile-icon">@include('partials.ui.icon', ['name' => 'giving'])</span>
                            <strong>{{ __('Give again') }}</strong>
                            <p>{{ __('Start another branch contribution when you are ready.') }}</p>
                        </a>
                    @else
                        <a class="action-tile" href="{{ route('home') }}">
                            <span class="action-tile-icon">@include('partials.ui.icon', ['name' => 'home'])</span>
                            <strong>{{ __('Return to homepage') }}</strong>
                            <p>{{ __('Go back to the church home page from here.') }}</p>
                        </a>
                    @endauth
                </div>
            </div>
        @elseif($payment->isPending() && ! $payment->checkout_url)
            <div class="announcement-callout mt-6">
                <p class="font-semibold text-black">{{ __('Payment prompt sent successfully.') }}</p>
                <p class="mt-2 text-sm text-black/70">{{ __('A payment prompt was sent to :phone. Ask the payer to approve it on the phone, then this page will update after confirmation arrives.', ['phone' => $payment->maskedPayerPhone()]) }}</p>
                <p class="mt-2 text-sm text-black/70">{{ __('Requested network') }}: {{ $payment->requestedNetworkLabel() }} · {{ __('Last update') }}: {{ optional($payment->updated_at)->translatedFormat('d M Y H:i') }}</p>
                <p class="mt-2 text-sm text-black/70">{{ __('Wait with peace. Once confirmation arrives, the giving will be recorded automatically.') }}</p>
                <div class="action-tile-grid mt-4">
                    <button class="action-tile" type="button" data-copy-text="{{ $payment->public_reference }}">
                        <span class="action-tile-icon">@include('partials.ui.icon', ['name' => 'archive'])</span>
                        <strong>{{ __('Copy reference') }}</strong>
                        <p>{{ __('Keep the payment reference close while confirmation is pending.') }}</p>
                    </button>
                    <button class="action-tile" type="button" data-share-link="{{ $payment->publicStatusUrl() }}" data-share-title="{{ __('Offering Payment Status') }}">
                        <span class="action-tile-icon">@include('partials.ui.icon', ['name' => 'megaphone'])</span>
                        <strong>{{ __('Share status page') }}</strong>
                        <p>{{ __('Send the live status page to the payer if needed.') }}</p>
                    </button>
                    <a class="action-tile" href="{{ $returnUrl }}">
                        <span class="action-tile-icon">@include('partials.ui.icon', ['name' => auth()->check() ? 'giving' : 'home'])</span>
                        <strong>{{ $returnLabel }}</strong>
                        <p>{{ __('Move back without losing this payment reference.') }}</p>
                    </a>
                </div>
            </div>
        @elseif($payment->isPending() && $payment->checkout_url)
            <div class="announcement-callout mt-6">
                <p class="font-semibold text-black">{{ __('Complete payment using the checkout link below.') }}</p>
                <p class="mt-2 text-sm text-black/70">{{ __('After payment, the system will receive confirmation and this page will update to completed status.') }}</p>
                <p class="mt-2 text-sm text-black/70">{{ __('Move forward with confidence and finish this giving in peace when you are ready.') }}</p>
                <div class="action-tile-grid mt-4">
                    <a class="action-tile is-primary" href="{{ $payment->checkout_url }}" target="_blank" rel="noopener">
                        <span class="action-tile-icon">@include('partials.ui.icon', ['name' => 'giving'])</span>
                        <strong>{{ __('Open checkout') }}</strong>
                        <p>{{ __('Continue payment from the secure checkout page.') }}</p>
                    </a>
                    <button class="action-tile" type="button" data-copy-text="{{ $payment->public_reference }}">
                        <span class="action-tile-icon">@include('partials.ui.icon', ['name' => 'archive'])</span>
                        <strong>{{ __('Copy reference') }}</strong>
                        <p>{{ __('Keep the payment reference available during checkout.') }}</p>
                    </button>
                    <button class="action-tile" type="button" data-share-link="{{ $payment->publicStatusUrl() }}" data-share-title="{{ __('Offering Payment Status') }}">
                        <span class="action-tile-icon">@include('partials.ui.icon', ['name' => 'megaphone'])</span>
                        <strong>{{ __('Share status page') }}</strong>
                        <p>{{ __('Send the payment status page to the giver if needed.') }}</p>
                    </button>
                    <a class="action-tile" href="{{ $returnUrl }}">
                        <span class="action-tile-icon">@include('partials.ui.icon', ['name' => auth()->check() ? 'giving' : 'home'])</span>
                        <strong>{{ $returnLabel }}</strong>
                        <p>{{ __('Leave checkout for now and return later if needed.') }}</p>
                    </a>
                </div>
            </div>
        @else
            <div class="announcement-callout mt-6">
                <p class="font-semibold text-black">{{ __('This payment is not currently active.') }}</p>
                <p class="mt-2 text-sm text-black/70">{{ $failedRecoveryCopy }}</p>
                <p class="mt-2 text-sm text-black/70">{{ __('Do not be discouraged. You can begin again when the time is right.') }}</p>
                <div class="action-tile-grid mt-4">
                    <button class="action-tile" type="button" data-copy-text="{{ $payment->public_reference }}">
                        <span class="action-tile-icon">@include('partials.ui.icon', ['name' => 'archive'])</span>
                        <strong>{{ __('Copy reference') }}</strong>
                        <p>{{ __('Keep the reference ready if you need support.') }}</p>
                    </button>
                    <a class="action-tile" href="{{ $returnUrl }}">
                        <span class="action-tile-icon">@include('partials.ui.icon', ['name' => auth()->check() ? 'giving' : 'home'])</span>
                        <strong>{{ $returnLabel }}</strong>
                        <p>{{ __('Go back and create a fresh request when you are ready.') }}</p>
                    </a>
                </div>
            </div>
        @endif
    </section>

    <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,0.9fr)]">
        <article class="card-rgc">
            <div class="space-y-2">
                <p class="section-kicker">{{ __('Payment summary') }}</p>
                <h2 class="text-2xl font-semibold">{{ __('Receipt Details') }}</h2>
            </div>

            <dl class="payment-receipt-grid mt-6">
                <div>
                    <dt>{{ __('Giving type') }}</dt>
                    <dd>{{ $payment->paymentTypeLabel() }}</dd>
                </div>
                <div>
                    <dt>{{ __('Purpose') }}</dt>
                    <dd>{{ $payment->description ?: __('Offering payment') }}</dd>
                </div>
                <div>
                    <dt>{{ __('Created') }}</dt>
                    <dd>{{ optional($payment->created_at)->translatedFormat('d M Y H:i') }}</dd>
                </div>
                <div>
                    <dt>{{ __('Payment status') }}</dt>
                    <dd>{{ $payment->statusLabel() }}</dd>
                </div>
                <div>
                    <dt>{{ __('Requested network') }}</dt>
                    <dd>{{ $payment->requestedNetworkLabel() }}</dd>
                </div>
                <div>
                    <dt>{{ __('Offering date') }}</dt>
                    <dd>{{ optional($payment->offering_date)->translatedFormat('d M Y') ?? __('Not set') }}</dd>
                </div>
                <div>
                    <dt>{{ __('Paid at') }}</dt>
                    <dd>{{ optional($payment->paid_at)->translatedFormat('d M Y H:i') ?? __('Waiting for confirmation') }}</dd>
                </div>
                <div>
                    <dt>{{ __('Provider reference') }}</dt>
                    <dd>{{ $payment->provider_reference }}</dd>
                </div>
                <div>
                    <dt>{{ __('Payer email') }}</dt>
                    <dd>{{ $payment->maskedPayerEmail() }}</dd>
                </div>
                <div>
                    <dt>{{ __('Payer phone') }}</dt>
                    <dd>{{ $payment->maskedPayerPhone() }}</dd>
                </div>
                @if($payment->externalReference())
                    <div>
                        <dt>{{ __('Provider external reference') }}</dt>
                        <dd>{{ $payment->externalReference() }}</dd>
                    </div>
                @endif
                <div>
                    <dt>{{ __('Provider channel') }}</dt>
                    <dd>{{ $payment->providerChannelLabel() }}</dd>
                </div>
            </dl>
        </article>

        <article class="space-y-6">
            <div class="card-rgc">
                <div class="space-y-2">
                    <p class="section-kicker">{{ __('Timeline') }}</p>
                    <h2 class="text-2xl font-semibold">{{ __('Payment Journey') }}</h2>
                </div>

                <div class="payment-timeline mt-6">
                    <div class="payment-step is-complete">
                        <strong>{{ __('Request created') }}</strong>
                        <span>{{ optional($payment->created_at)->diffForHumans() }}</span>
                    </div>
                    <div class="payment-step {{ $payment->isPending() ? 'is-current' : ($payment->isCompleted() ? 'is-complete' : '') }}">
                        <strong>{{ __('Waiting for customer payment') }}</strong>
                        <span>{{ $payment->checkout_url ? __('Checkout link ready') : __('Phone prompt sent to payer') }}</span>
                    </div>
                    <div class="payment-step {{ $payment->isCompleted() ? 'is-complete' : ($payment->isFailed() ? 'is-failed' : '') }}">
                        <strong>{{ $payment->isCompleted() ? __('Payment confirmed successfully.') : __('Webhook confirmation') }}</strong>
                        <span>
                            @if($payment->isCompleted())
                                {{ __('Confirmed by Snippe and posted into offerings.') }}
                            @elseif($payment->isFailed())
                                {{ __('The payment did not complete successfully.') }}
                            @else
                                {{ __('Still waiting for payment confirmation from Snippe.') }}
                            @endif
                        </span>
                    </div>
                </div>

                @if($payment->offering)
                    <div class="mt-6 rounded-3xl border border-black/10 bg-white px-5 py-4 text-sm text-black/70">
                        <p class="font-semibold text-black">{{ __('Ledger record created') }}</p>
                        <p class="mt-2">{{ __('This payment is already linked to offering record #:id.', ['id' => $payment->offering->id]) }}</p>
                    </div>
                @endif
            </div>

            <div class="card-rgc payment-contact-card">
                <div class="space-y-2">
                    <p class="section-kicker">{{ __('Need support?') }}</p>
                    <h2 class="text-2xl font-semibold">{{ __('Branch Contact') }}</h2>
                    <p class="text-sm text-black/65">{{ __('If you need help with this payment, use the branch contact details below.') }}</p>
                    <p class="text-sm text-black/65">{{ __('Your church family can help you follow this giving through to completion.') }}</p>
                </div>

                <dl class="payment-receipt-grid mt-6">
                    <div>
                        <dt>{{ __('Branch') }}</dt>
                        <dd>{{ $payment->branch?->name ?? __('Unknown branch') }}</dd>
                    </div>
                    <div>
                        <dt>{{ __('District') }}</dt>
                        <dd>{{ $payment->branch?->district?->name ?? __('Not set') }}</dd>
                    </div>
                    <div>
                        <dt>{{ __('Phone') }}</dt>
                        <dd>{{ $payment->branch?->phone ?: __('Not provided') }}</dd>
                    </div>
                    <div>
                        <dt>{{ __('Email') }}</dt>
                        <dd>{{ $payment->branch?->email ?: __('Not provided') }}</dd>
                    </div>
                </dl>
            </div>
        </article>
    </section>
</div>
@endsection
