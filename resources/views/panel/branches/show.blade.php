@extends('layouts.app')

@section('content')
<div class="branch-show-layout">
    <div class="card-rgc branch-show-hero">
        <div class="branch-show-header">
            <div>
                <span class="section-kicker">{{ __('Branch Details') }}</span>
                <h1 class="mt-4 text-2xl font-semibold">{{ $branch->name }}</h1>
                <p class="mt-2 text-sm text-black/65">{{ __('Review governance location, branch identity, contact channels, and quick operational signals for this branch.') }}</p>
            </div>
            <div class="branch-show-actions">
                <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('branches.index') }}">{{ __('Back to branches') }}</a>
                <a class="btn-rgc-alt w-full sm:w-auto" target="_blank" href="{{ route('branches.print', $branch) }}">{{ __('Print Profile') }}</a>
                <a class="btn-rgc w-full sm:w-auto" href="{{ route('branches.pdf', $branch) }}">{{ __('Download PDF') }}</a>
                <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('branches.records.export', ['branch' => $branch, 'format' => 'xlsx']) }}">{{ __('Export Records XLSX') }}</a>
                <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('branches.records.export', ['branch' => $branch, 'format' => 'csv']) }}">{{ __('Export Records CSV') }}</a>
                @can('update', $branch)
                    <a class="btn-rgc w-full sm:w-auto" href="{{ route('branches.edit', $branch) }}">{{ __('Edit Branch') }}</a>
                @endcan
            </div>
        </div>

        <div class="branch-preview-breakdown mt-5">
            <span>{{ __('Type: :type', ['type' => __(Illuminate\Support\Str::headline($branch->branch_type))]) }}</span>
            <span>{{ __('Status: :status', ['status' => __(Illuminate\Support\Str::headline($branch->status))]) }}</span>
            @if($branch->is_headquarters)
                <span>{{ __('Headquarters branch') }}</span>
            @endif
        </div>
    </div>

    <div class="branch-show-stats mt-5">
        <article class="branch-preview-stat">
            <span>{{ __('Users') }}</span>
            <strong>{{ $branch->users_count }}</strong>
            <p>{{ __('Registered users currently attached to this branch.') }}</p>
        </article>
        <article class="branch-preview-stat">
            <span>{{ __('Messages') }}</span>
            <strong>{{ $branch->messages_count }}</strong>
            <p>{{ __('Branch chat messages already linked to this branch.') }}</p>
        </article>
        <article class="branch-preview-stat">
            <span>{{ __('Events') }}</span>
            <strong>{{ $branch->events_count }}</strong>
            <p>{{ __('Upcoming and historical events currently tied to this branch.') }}</p>
        </article>
        <article class="branch-preview-stat">
            <span>{{ __('Offerings total') }}</span>
            <strong>TZS {{ number_format((float) ($branch->offerings_total_amount ?? 0), 2) }}</strong>
            <p>{{ __('Total recorded offerings for this branch.') }}</p>
        </article>
        <article class="branch-preview-stat">
            <span>{{ __('Expenses total') }}</span>
            <strong>TZS {{ number_format((float) ($branch->expenses_total_amount ?? 0), 2) }}</strong>
            <p>{{ __('Total recorded expenses for this branch.') }}</p>
        </article>
        <article class="branch-preview-stat">
            <span>{{ __('Net balance') }}</span>
            <strong>TZS {{ number_format($netBalance, 2) }}</strong>
            <p>{{ __('Offerings minus expenses based on current records.') }}</p>
        </article>
        <article class="branch-preview-stat">
            <span>{{ __('Payment Requests') }}</span>
            <strong>{{ $branch->offering_payments_count }}</strong>
            <p>{{ __('Payment requests created for this branch.') }}</p>
        </article>
        <article class="branch-preview-stat">
            <span>{{ __('Pending Payments') }}</span>
            <strong>{{ $branch->pending_payments_count }}</strong>
            <p>{{ __('Phone prompts or checkout requests still waiting for secure confirmation.') }}</p>
        </article>
        <article class="branch-preview-stat">
            <span>{{ __('Completed Payments') }}</span>
            <strong>{{ $branch->completed_payments_count }}</strong>
            <p>{{ __('Snippe payments already confirmed and posted to the ledger.') }}</p>
        </article>
        <article class="branch-preview-stat">
            <span>{{ __('Snippe collected total') }}</span>
            <strong>TZS {{ number_format((float) ($branch->completed_payments_total_amount ?? 0), 2) }}</strong>
            <p>{{ __('Total value of completed Snippe payment requests for this branch.') }}</p>
        </article>
    </div>

    <div class="branch-show-grid mt-5">
        <section class="card-rgc">
            <div class="branch-show-header">
                <div>
                    <span class="section-kicker">{{ __('Filtered Records Export') }}</span>
                    <p class="mt-2 text-sm text-black/65">{{ __('Filter the branch records export by date range. Leave either field blank when you want the export to include older or newer activity automatically.') }}</p>
                </div>
            </div>
            <form class="mt-4 grid gap-4 md:grid-cols-2" method="GET">
                <div class="md:col-span-2 flex flex-wrap gap-3">
                    <button class="btn-rgc-alt w-full sm:w-auto" formaction="{{ route('branches.records.export', ['branch' => $branch, 'format' => 'xlsx']) }}" name="preset" value="this_month" type="submit">{{ __('This month') }}</button>
                    <button class="btn-rgc-alt w-full sm:w-auto" formaction="{{ route('branches.records.export', ['branch' => $branch, 'format' => 'xlsx']) }}" name="preset" value="last_30_days" type="submit">{{ __('Last 30 days') }}</button>
                    <button class="btn-rgc-alt w-full sm:w-auto" formaction="{{ route('branches.records.export', ['branch' => $branch, 'format' => 'xlsx']) }}" name="preset" value="this_year" type="submit">{{ __('This year') }}</button>
                    <button class="btn-rgc-alt w-full sm:w-auto" formaction="{{ route('branches.records.export', ['branch' => $branch, 'format' => 'xlsx']) }}" name="preset" value="all_time" type="submit">{{ __('All time') }}</button>
                </div>
                <label class="flex flex-col gap-2 text-sm font-medium text-black/75">
                    <span>{{ __('From date') }}</span>
                    <input class="rounded-2xl border border-black/10 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-rgc-red focus:ring-2 focus:ring-rgc-red/15" type="date" name="date_from" value="{{ request('date_from') }}">
                </label>
                <label class="flex flex-col gap-2 text-sm font-medium text-black/75">
                    <span>{{ __('To date') }}</span>
                    <input class="rounded-2xl border border-black/10 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-rgc-red focus:ring-2 focus:ring-rgc-red/15" type="date" name="date_to" value="{{ request('date_to') }}">
                </label>
                <div class="flex flex-wrap gap-3 md:col-span-2">
                    <button class="btn-rgc-alt w-full sm:w-auto" formaction="{{ route('branches.records.export', ['branch' => $branch, 'format' => 'xlsx']) }}" type="submit">{{ __('Export filtered records XLSX') }}</button>
                    <button class="btn-rgc-alt w-full sm:w-auto" formaction="{{ route('branches.records.export', ['branch' => $branch, 'format' => 'csv']) }}" type="submit">{{ __('Export filtered records CSV') }}</button>
                </div>
            </form>
        </section>
    </div>


    <div class="branch-show-grid mt-5">
        <section class="card-rgc">
            <div class="branch-show-header">
                <div>
                    <span class="section-kicker">{{ __('Recent Payment Requests') }}</span>
                    <p class="mt-2 text-sm text-black/65">{{ __('Payment prompts and fallback checkout requests created for this branch, including pending and completed collections.') }}</p>
                </div>
            </div>
            @if($recentPayments->isEmpty())
                <p class="mt-4 text-sm text-black/65">{{ __('No Snippe payment requests created for this branch yet.') }}</p>
            @else
                <div class="payment-request-grid mt-4">
                    @foreach($recentPayments as $payment)
                        <article class="payment-request-card">
                            <div class="payment-request-topline">
                                <span class="payment-status-badge is-{{ $payment->status }}">{{ $payment->statusLabel() }}</span>
                                <span class="text-xs text-black/50">{{ $payment->public_reference }}</span>
                            </div>
                            <h3>TZS {{ number_format((float) $payment->amount, 2) }}</h3>
                            <p>{{ $payment->description ?: __('Offering payment') }}</p>
                            <div class="payment-request-meta">
                                <span>{{ $payment->payer_name ?: __('Walk-in giver') }}</span>
                                <span>{{ optional($payment->paid_at ?: $payment->created_at)->diffForHumans() }}</span>
                            </div>
                            <div class="payment-request-actions">
                                <a class="btn-rgc-outline w-full sm:w-auto" href="{{ route('offerings.payments.public.show', $payment->public_reference) }}">{{ __('Status page') }}</a>
                                @if($payment->isCompleted())
                                    <a class="btn-rgc w-full sm:w-auto" href="{{ $payment->temporaryPublicReceiptUrl() }}">{{ __('Download receipt PDF') }}</a>
                                @elseif($payment->checkout_url)
                                    <a class="btn-rgc w-full sm:w-auto" href="{{ $payment->checkout_url }}" target="_blank" rel="noopener">{{ __('Open checkout') }}</a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    <div class="branch-show-grid mt-5">
        <section class="card-rgc">
            <span class="section-kicker">{{ __('Governance Scope') }}</span>
            <dl class="branch-detail-list mt-4">
                <div>
                    <dt>{{ __('Region') }}</dt>
                    <dd>{{ $branch->region->name }}</dd>
                </div>
                <div>
                    <dt>{{ __('District') }}</dt>
                    <dd>{{ $branch->district->name }}</dd>
                </div>
                <div>
                    <dt>{{ __('Branch type') }}</dt>
                    <dd>{{ __(Illuminate\Support\Str::headline($branch->branch_type)) }}</dd>
                </div>
                <div>
                    <dt>{{ __('Status') }}</dt>
                    <dd>{{ __(Illuminate\Support\Str::headline($branch->status)) }}</dd>
                </div>
            </dl>
        </section>

        <section class="card-rgc">
            <span class="section-kicker">{{ __('Contact Details') }}</span>
            <dl class="branch-detail-list mt-4">
                <div>
                    <dt>{{ __('Address') }}</dt>
                    <dd>{{ $branch->address ?: __('No address recorded') }}</dd>
                </div>
                <div>
                    <dt>{{ __('Phone') }}</dt>
                    <dd>{{ $branch->phone ?: __('No phone recorded') }}</dd>
                </div>
                <div>
                    <dt>{{ __('Email') }}</dt>
                    <dd>{{ $branch->email ?: __('No email recorded') }}</dd>
                </div>
                <div>
                    <dt>{{ __('Slug') }}</dt>
                    <dd>{{ $branch->slug }}</dd>
                </div>
            </dl>
        </section>
    </div>

    <div class="branch-show-grid mt-5">
        <section class="card-rgc">
            <div class="branch-show-header">
                <div>
                    <span class="section-kicker">{{ __('Recent Offerings') }}</span>
                </div>
            </div>
            @if($recentOfferings->isEmpty())
                <p class="mt-4 text-sm text-black/65">{{ __('No offerings recorded for this branch yet.') }}</p>
            @else
                <div class="branch-activity-list mt-4">
                    @foreach($recentOfferings as $offering)
                        <div class="branch-activity-item">
                            <strong>TZS {{ number_format((float) $offering->amount, 2) }}</strong>
                            <span>{{ optional($offering->date)->format('d M Y') }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="card-rgc">
            <div class="branch-show-header">
                <div>
                    <span class="section-kicker">{{ __('Recent Expenses') }}</span>
                </div>
            </div>
            @if($recentExpenses->isEmpty())
                <p class="mt-4 text-sm text-black/65">{{ __('No expenses recorded for this branch yet.') }}</p>
            @else
                <div class="branch-activity-list mt-4">
                    @foreach($recentExpenses as $expense)
                        <div class="branch-activity-item">
                            <strong>{{ $expense->description ?: __('General expense') }}</strong>
                            <span>TZS {{ number_format((float) $expense->amount, 2) }} • {{ optional($expense->date)->format('d M Y') }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    <div class="branch-show-grid mt-5">
        <section class="card-rgc">
            <div class="branch-show-header">
                <div>
                    <span class="section-kicker">{{ __('Recent Events') }}</span>
                </div>
            </div>
            @if($recentEvents->isEmpty())
                <p class="mt-4 text-sm text-black/65">{{ __('No events recorded for this branch yet.') }}</p>
            @else
                <div class="branch-activity-list mt-4">
                    @foreach($recentEvents as $event)
                        <div class="branch-activity-item">
                            <strong>{{ $event->title }}</strong>
                            <span>{{ optional($event->event_date)->format('d M Y H:i') }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="card-rgc">
            <div class="branch-show-header">
                <div>
                    <span class="section-kicker">{{ __('Recent Users') }}</span>
                </div>
            </div>
            @if($recentUsers->isEmpty())
                <p class="mt-4 text-sm text-black/65">{{ __('No users are linked to this branch yet.') }}</p>
            @else
                <div class="branch-user-grid mt-4">
                    @foreach($recentUsers as $user)
                        <article class="branch-user-card">
                            <strong>{{ $user->name }}</strong>
                            <p>{{ $user->email }}</p>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
