@extends('layouts.app')
@section('content')
<div class="grid gap-6 xl:grid-cols-[minmax(0,1.25fr)_minmax(0,1fr)]">
    <div class="card-rgc">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="section-kicker section-kicker--icon">@include('partials.ui.icon', ['name' => 'giving', 'class' => 'section-kicker-icon'])<span>{{ __('Ledger') }}</span></p>
                <h1 class="text-xl font-semibold">{{ __('Offerings') }}</h1>
                <p class="mt-2 text-sm text-black/65">{{ __('Manual branch records and payment-prompt collections meet here so finance staff can review the full giving picture in one place.') }}</p>
            </div>
            <a class="btn-rgc w-full sm:w-auto" href="{{ route('offerings.create') }}">@include('partials.ui.icon', ['name' => 'plus', 'class' => 'button-icon'])<span>{{ __('Add or collect') }}</span></a>
        </div>

        @if(session('payment_reference'))
            <div class="announcement-callout mt-5 space-y-3">
                <p class="font-semibold text-black">
                    {{ session('payment_prompt_phone') ? __('Payment prompt sent.') : __('Payment checkout link created.') }}
                </p>
                <p class="text-sm text-black/70">{{ __('Reference') }}: <span class="font-medium text-black">{{ session('payment_reference') }}</span></p>
                @if(session('payment_prompt_phone'))
                    <p class="text-sm text-black/70">{{ __('Prompt sent to') }}: <span class="font-medium text-black">{{ session('payment_prompt_phone') }}</span></p>
                @endif
                <div class="action-tile-grid mt-4">
                    @if(session('payment_link'))
                        <a class="action-tile is-primary" href="{{ session('payment_link') }}" target="_blank" rel="noopener">
                            <span class="action-tile-icon">@include('partials.ui.icon', ['name' => 'giving'])</span>
                            <strong>{{ __('Open checkout') }}</strong>
                            <p>{{ __('Continue the latest payment request from checkout.') }}</p>
                        </a>
                    @endif
                    <a class="action-tile" href="{{ route('offerings.payments.public.show', session('payment_reference')) }}">
                        <span class="action-tile-icon">@include('partials.ui.icon', ['name' => 'eye'])</span>
                        <strong>{{ __('View status page') }}</strong>
                        <p>{{ __('Follow the latest payment request from its live status page.') }}</p>
                    </a>
                </div>
            </div>
        @endif

        <div class="table-wrap mt-5">
            <table class="responsive-table w-full text-sm">
                <thead>
                    <tr>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Description') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($offerings as $offering)
                        <tr class="border-t">
                            <td>{{ $offering->offering_date?->format('Y-m-d') ?? $offering->offering_date }}</td>
                            <td>TZS {{ number_format((float) $offering->amount, 2) }}</td>
                            <td>{{ $offering->description ?: __('No description') }}</td>
                        </tr>
                    @empty
                        <tr class="border-t">
                            <td colspan="3" class="py-6 text-center text-black/60">{{ __('No offerings recorded yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $offerings->links() }}</div>
    </div>

    <div class="card-rgc">
            <div class="space-y-2">
                <p class="section-kicker section-kicker--icon">@include('partials.ui.icon', ['name' => 'giving', 'class' => 'section-kicker-icon'])<span>{{ __('Snippe payments') }}</span></p>
                <h2 class="text-xl font-semibold">{{ __('Payment Requests') }}</h2>
                <p class="text-sm text-black/65">{{ __('Track pending phone prompts, refresh status manually, and confirm whether the collection has already reached the branch ledger.') }}</p>
            </div>

        <div class="mt-5 grid gap-4">
            @forelse($payments as $payment)
                <article class="rounded-3xl border border-black/10 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex rounded-full bg-black px-3 py-1 text-xs font-semibold text-white">{{ $payment->statusLabel() }}</span>
                                <span class="text-xs font-medium text-black/55">{{ $payment->public_reference }}</span>
                            </div>
                            <h3 class="mt-2 text-lg font-semibold text-black">TZS {{ number_format((float) $payment->amount, 2) }}</h3>
                            <p class="mt-1 text-sm text-black/65">{{ $payment->description ?: __('Offering payment') }}</p>
                            <p class="mt-2 text-xs text-black/50">{{ __('Payer') }}: {{ $payment->payer_name ?: __('Walk-in giver') }}</p>
                            @if($payment->isPending() && ! $payment->usesHostedCheckout())
                                <p class="mt-2 text-xs text-black/55">{{ __('Prompt sent to :phone • Requested network: :network', ['phone' => $payment->maskedPayerPhone(), 'network' => $payment->requestedNetworkLabel()]) }}</p>
                            @endif
                        </div>
                        <div class="text-sm text-black/55">
                            {{ $payment->created_at?->diffForHumans() }}
                        </div>
                    </div>

                    @if($payment->reviewed_at)
                        <div class="mt-4 rounded-2xl border border-black/10 bg-black/[0.03] px-4 py-3 text-sm text-black/70">
                            <p class="font-semibold text-black">{{ __('Reviewed') }}</p>
                            <p class="mt-1">{{ __('Reviewed by') }}: {{ $payment->reviewedBy?->name ?? __('System') }}</p>
                            <p class="mt-1">{{ __('Reviewed at') }}: {{ $payment->reviewed_at->format('Y-m-d H:i') }}</p>
                        </div>
                    @endif

                    <div class="action-tile-grid mt-4">
                        @if($payment->checkout_url)
                            <a class="action-tile is-primary" href="{{ $payment->checkout_url }}" target="_blank" rel="noopener">
                                <span class="action-tile-icon">@include('partials.ui.icon', ['name' => 'giving'])</span>
                                <strong>{{ __('Open checkout') }}</strong>
                                <p>{{ __('Resume the hosted checkout for this payment request.') }}</p>
                            </a>
                        @endif
                        <a class="action-tile" href="{{ route('offerings.payments.public.show', $payment->public_reference) }}">
                            <span class="action-tile-icon">@include('partials.ui.icon', ['name' => 'eye'])</span>
                            <strong>{{ __('Status page') }}</strong>
                            <p>{{ __('Open the live status page for this payment request.') }}</p>
                        </a>
                        <form method="POST" action="{{ route('offerings.payments.sync', $payment) }}" class="action-tile-form">
                            @csrf
                            <button class="action-tile" type="submit">
                                <span class="action-tile-icon">@include('partials.ui.icon', ['name' => 'sparkles'])</span>
                                <strong>{{ __('Refresh status') }}</strong>
                                <p>{{ __('Ask the system to check the latest payment status now.') }}</p>
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-3xl border border-dashed border-black/15 bg-white p-6 text-sm text-black/60">
                    {{ __('No Snippe payment requests created yet.') }}
                </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $payments->links() }}</div>
    </div>
</div>
@endsection
