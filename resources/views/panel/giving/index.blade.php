@extends('layouts.app')

@section('title', __('Giving') . ' - RGC')

@section('content')
<div class="grid gap-6 xl:grid-cols-[minmax(0,1.1fr)_minmax(0,0.9fr)]">
    <section class="card-rgc-strong">
        <div class="space-y-2">
            <p class="section-kicker">{{ __('Member giving') }}</p>
            <h1 class="text-2xl font-semibold">{{ __('Give to your branch securely') }}</h1>
            <p class="text-sm text-black/70">{{ __('Choose the type of giving, enter the amount, and the system will generate a secure Snippe checkout link for your branch.') }}</p>
        </div>

        <div class="branch-preview-breakdown mt-5">
            <span>{{ __('Branch: :branch', ['branch' => $branch->name]) }}</span>
            <span>{{ __('District: :district', ['district' => $branch->district->name]) }}</span>
            <span>{{ __('Region: :region', ['region' => $branch->region->name]) }}</span>
        </div>

        @if(session('payment_link'))
            <div class="announcement-callout mt-5 space-y-3">
                <p class="font-semibold text-black">{{ __('Snippe payment link created.') }}</p>
                <p class="text-sm text-black/70">{{ __('Reference') }}: <span class="font-medium text-black">{{ session('payment_reference') }}</span></p>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <a class="btn-rgc w-full sm:w-auto" href="{{ session('payment_link') }}" target="_blank" rel="noopener">{{ __('Open checkout') }}</a>
                    <a class="btn-rgc-outline w-full sm:w-auto" href="{{ route('offerings.payments.public.show', session('payment_reference')) }}">{{ __('View status page') }}</a>
                </div>
            </div>
        @endif

        <form class="mt-6 grid gap-4" method="POST" action="{{ route('giving.store') }}">
            @csrf
            <div>
                <label class="field-label" for="payment_type">{{ __('Giving type') }}</label>
                <select class="input-rgc" id="payment_type" name="payment_type">
                    @foreach($paymentTypes as $value => $label)
                        <option value="{{ $value }}" @selected(old('payment_type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid gap-3 sm:grid-cols-4">
                @foreach([5000, 10000, 25000, 50000] as $quickAmount)
                    <button class="btn-rgc-outline w-full" type="button" data-quick-amount="{{ $quickAmount }}">TZS {{ number_format($quickAmount) }}</button>
                @endforeach
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="giving_amount">{{ __('Amount') }}</label>
                    <input class="input-rgc" id="giving_amount" type="number" step="0.01" min="100" name="amount" value="{{ old('amount') }}" required>
                </div>
                <div>
                    <label class="field-label" for="giving_date">{{ __('Offering date') }}</label>
                    <input class="input-rgc" id="giving_date" type="date" name="offering_date" value="{{ old('offering_date', now()->toDateString()) }}">
                </div>
            </div>
            <div>
                <label class="field-label" for="giving_payer_name">{{ __('Payer name') }}</label>
                <input class="input-rgc" id="giving_payer_name" type="text" name="payer_name" value="{{ old('payer_name', auth()->user()->name) }}" required>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="giving_payer_phone">{{ __('Phone number') }}</label>
                    <input class="input-rgc" id="giving_payer_phone" type="text" name="payer_phone" value="{{ old('payer_phone', auth()->user()->phone) }}" placeholder="2557XXXXXXXX">
                </div>
                <div>
                    <label class="field-label" for="giving_payer_email">{{ __('Email address') }}</label>
                    <input class="input-rgc" id="giving_payer_email" type="email" name="payer_email" value="{{ old('payer_email', auth()->user()->email) }}">
                </div>
            </div>
            <div>
                <label class="field-label" for="giving_description">{{ __('Description') }}</label>
                <textarea class="textarea-rgc min-h-28" id="giving_description" name="description" placeholder="{{ __('Sunday giving, thanksgiving, special contribution, or any branch-specific note.') }}">{{ old('description') }}</textarea>
            </div>
            <div class="announcement-callout">
                <p class="font-semibold text-black">{{ __('What happens next?') }}</p>
                <p class="mt-2 text-sm text-black/70">{{ __('A secure checkout link will open for you. Once Snippe confirms the payment, the giving will be posted automatically to your branch ledger.') }}</p>
            </div>
            <div class="form-actions">
                <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Create payment link') }}</button>
            </div>
        </form>
    </section>

    <section class="space-y-6">
        <div class="panel-grid cols-2">
            <article class="stat-card">
                <span class="stat-label">{{ __('My payment requests') }}</span>
                <strong>{{ $stats['payments'] }}</strong>
            </article>
            <article class="stat-card">
                <span class="stat-label">{{ __('Pending Payments') }}</span>
                <strong>{{ $stats['pending'] }}</strong>
            </article>
            <article class="stat-card">
                <span class="stat-label">{{ __('Completed Payments') }}</span>
                <strong>{{ $stats['completed'] }}</strong>
            </article>
            <article class="stat-card">
                <span class="stat-label">{{ __('My completed giving total') }}</span>
                <strong>TZS {{ number_format($stats['completed_total'], 2) }}</strong>
            </article>
        </div>

        <div class="card-rgc">
            <div class="space-y-2">
                <p class="section-kicker">{{ __('My recent giving') }}</p>
                <h2 class="text-xl font-semibold">{{ __('Payment history') }}</h2>
                <p class="text-sm text-black/65">{{ __('Track the status of the checkout links you have already created.') }}</p>
            </div>

            <div class="mt-5 grid gap-4">
                @forelse($payments as $payment)
                    <article class="payment-request-card">
                        <div class="payment-request-topline">
                            <span class="payment-status-badge is-{{ $payment->status }}">{{ $payment->statusLabel() }}</span>
                            <span class="text-xs text-black/50">{{ $payment->public_reference }}</span>
                        </div>
                        <h3>TZS {{ number_format((float) $payment->amount, 2) }}</h3>
                        <p>{{ $payment->paymentTypeLabel() }}{{ $payment->description ? ' • '.$payment->description : '' }}</p>
                        <div class="payment-request-meta">
                            <span>{{ optional($payment->created_at)->translatedFormat('d M Y H:i') }}</span>
                            <span>{{ optional($payment->paid_at ?: $payment->created_at)->diffForHumans() }}</span>
                        </div>
                        <div class="payment-request-actions">
                            <a class="btn-rgc-outline w-full sm:w-auto" href="{{ route('offerings.payments.public.show', $payment->public_reference) }}">{{ __('Status page') }}</a>
                            <button class="btn-rgc-outline w-full sm:w-auto" type="button" data-copy-text="{{ $payment->public_reference }}">{{ __('Copy reference') }}</button>
                            <button class="btn-rgc-outline w-full sm:w-auto" type="button" data-share-link="{{ route('offerings.payments.public.show', $payment->public_reference) }}" data-share-title="{{ __('Offering Payment Status') }}">{{ __('Share status page') }}</button>
                            @if($payment->isCompleted())
                                <a class="btn-rgc w-full sm:w-auto" href="{{ route('offerings.payments.public.receipt', $payment->public_reference) }}">{{ __('Download receipt PDF') }}</a>
                            @elseif($payment->checkout_url)
                                <a class="btn-rgc w-full sm:w-auto" href="{{ $payment->checkout_url }}" target="_blank" rel="noopener">{{ __('Open checkout') }}</a>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-black/15 bg-white p-6 text-sm text-black/60">
                        {{ __('You have not created any payment requests yet.') }}
                    </div>
                @endforelse
            </div>

            <div class="mt-4">{{ $payments->links() }}</div>
        </div>
    </section>
</div>
@endsection
