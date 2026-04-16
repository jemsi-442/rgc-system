@extends('layouts.app')

@section('content')
<div class="branch-show-layout branch-show-layout--executive">
    <div class="card-rgc branch-show-hero branch-show-hero--executive">
        <div class="branch-show-header">
            <div>
                <span class="section-kicker section-kicker--icon">@include('partials.ui.icon', ['name' => 'church', 'class' => 'section-kicker-icon'])<span>{{ __('Branch Details') }}</span></span>
                <h1 class="mt-4 text-2xl font-semibold">{{ $branch->name }}</h1>
                <p class="mt-2 text-sm text-black/65">{{ __('Review the branch location, identity, contact details, and key signs of church life for this branch.') }}</p>
            </div>
            <div class="branch-show-actions">
                <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('branches.index') }}">@include('partials.ui.icon', ['name' => 'church', 'class' => 'button-icon'])<span>{{ __('Back to branches') }}</span></a>
                <a class="btn-rgc-alt w-full sm:w-auto" target="_blank" href="{{ route('branches.print', $branch) }}">@include('partials.ui.icon', ['name' => 'archive', 'class' => 'button-icon'])<span>{{ __('Print branch details') }}</span></a>
                <a class="btn-rgc w-full sm:w-auto" href="{{ route('branches.pdf', $branch) }}">@include('partials.ui.icon', ['name' => 'archive', 'class' => 'button-icon'])<span>{{ __('Download PDF') }}</span></a>
                <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('branches.records.export', ['branch' => $branch, 'format' => 'xlsx']) }}">@include('partials.ui.icon', ['name' => 'archive', 'class' => 'button-icon'])<span>{{ __('Export branch details XLSX') }}</span></a>
                <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('branches.records.export', ['branch' => $branch, 'format' => 'csv']) }}">@include('partials.ui.icon', ['name' => 'archive', 'class' => 'button-icon'])<span>{{ __('Export branch details CSV') }}</span></a>
                @can('update', $branch)
                    <a class="btn-rgc w-full sm:w-auto" href="{{ route('branches.edit', $branch) }}">@include('partials.ui.icon', ['name' => 'edit', 'class' => 'button-icon'])<span>{{ __('Edit branch') }}</span></a>
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

    <div class="branch-show-stats branch-show-stats--executive mt-5">
        <article class="branch-preview-stat">
            <span>{{ __('Users') }}</span>
            <strong>{{ $branch->users_count }}</strong>
            <p>{{ __('People currently attached to this branch.') }}</p>
        </article>
        <article class="branch-preview-stat">
            <span>{{ __('Messages') }}</span>
            <strong>{{ $branch->messages_count }}</strong>
            <p>{{ __('Branch chat messages already shared in this branch.') }}</p>
        </article>
        <article class="branch-preview-stat">
            <span>{{ __('Events') }}</span>
            <strong>{{ $branch->events_count }}</strong>
            <p>{{ __('Upcoming and past events currently tied to this branch.') }}</p>
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
            <p>{{ __('Offerings minus expenses based on what is recorded here.') }}</p>
        </article>
        <article class="branch-preview-stat">
            <span>{{ __('Payment Requests') }}</span>
            <strong>{{ $branch->offering_payments_count }}</strong>
            <p>{{ __('Payment requests created for this branch.') }}</p>
        </article>
        <article class="branch-preview-stat">
            <span>{{ __('Pending Payments') }}</span>
            <strong>{{ $branch->pending_payments_count }}</strong>
            <p>{{ __('Phone prompts or payment requests still waiting for confirmation.') }}</p>
        </article>
        <article class="branch-preview-stat">
            <span>{{ __('Completed Payments') }}</span>
            <strong>{{ $branch->completed_payments_count }}</strong>
            <p>{{ __('Giving requests already confirmed and recorded.') }}</p>
        </article>
        <article class="branch-preview-stat">
            <span>{{ __('Snippe collected total') }}</span>
            <strong>TZS {{ number_format((float) ($branch->completed_payments_total_amount ?? 0), 2) }}</strong>
            <p>{{ __('Total value of completed giving requests for this branch.') }}</p>
        </article>
    </div>

    <div class="branch-show-grid branch-show-grid--hero mt-5">
        <section class="card-rgc branch-show-card branch-show-card--filter">
            <div class="branch-show-header">
                <div>
                    <span class="section-kicker section-kicker--icon">@include('partials.ui.icon', ['name' => 'filter', 'class' => 'section-kicker-icon'])<span>{{ __('Filtered Export') }}</span></span>
                    <p class="mt-2 text-sm text-black/65">{{ __('Filter the branch export by date range. Leave either field blank when you want the export to include older or newer activity automatically.') }}</p>
                </div>
            </div>
            <form class="mt-4 grid gap-4 md:grid-cols-2" method="GET">
                <div class="md:col-span-2 flex flex-wrap gap-3">
                    <button class="btn-rgc-alt w-full sm:w-auto" formaction="{{ route('branches.records.export', ['branch' => $branch, 'format' => 'xlsx']) }}" name="preset" value="this_month" type="submit">@include('partials.ui.icon', ['name' => 'filter', 'class' => 'button-icon'])<span>{{ __('This month') }}</span></button>
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
                    <button class="btn-rgc-alt w-full sm:w-auto" formaction="{{ route('branches.records.export', ['branch' => $branch, 'format' => 'xlsx']) }}" type="submit">@include('partials.ui.icon', ['name' => 'archive', 'class' => 'button-icon'])<span>{{ __('Export filtered branch details XLSX') }}</span></button>
                    <button class="btn-rgc-alt w-full sm:w-auto" formaction="{{ route('branches.records.export', ['branch' => $branch, 'format' => 'csv']) }}" type="submit">@include('partials.ui.icon', ['name' => 'archive', 'class' => 'button-icon'])<span>{{ __('Export filtered branch details CSV') }}</span></button>
                </div>
            </form>
        </section>
    </div>


    <div class="branch-show-grid mt-5">
        <section class="card-rgc branch-show-card">
            <div class="branch-show-header">
                <div>
                    <span class="section-kicker section-kicker--icon">@include('partials.ui.icon', ['name' => 'giving', 'class' => 'section-kicker-icon'])<span>{{ __('Recent Giving Requests') }}</span></span>
                    <p class="mt-2 text-sm text-black/65">{{ __('Giving prompts created for this branch, including pending and completed collections.') }}</p>
                </div>
            </div>
            @if($recentPayments->isEmpty())
                <p class="mt-4 text-sm text-black/65">{{ __('No giving requests have been created for this branch yet.') }}</p>
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
                                <a class="btn-rgc-outline w-full sm:w-auto" href="{{ route('offerings.payments.public.show', $payment->public_reference) }}">@include('partials.ui.icon', ['name' => 'eye', 'class' => 'button-icon'])<span>{{ __('Status page') }}</span></a>
                                @if($payment->isCompleted())
                                    <a class="btn-rgc w-full sm:w-auto" href="{{ $payment->temporaryPublicReceiptUrl() }}">@include('partials.ui.icon', ['name' => 'archive', 'class' => 'button-icon'])<span>{{ __('Download receipt PDF') }}</span></a>
                                @elseif($payment->checkout_url)
                                    <a class="btn-rgc w-full sm:w-auto" href="{{ $payment->checkout_url }}" target="_blank" rel="noopener">@include('partials.ui.icon', ['name' => 'giving', 'class' => 'button-icon'])<span>{{ __('Open checkout') }}</span></a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    <div class="branch-show-grid mt-5">
        <section class="card-rgc branch-show-card">
            <span class="section-kicker section-kicker--icon">@include('partials.ui.icon', ['name' => 'church', 'class' => 'section-kicker-icon'])<span>{{ __('Church Location') }}</span></span>
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

        <section class="card-rgc branch-show-card">
            <span class="section-kicker section-kicker--icon">@include('partials.ui.icon', ['name' => 'user', 'class' => 'section-kicker-icon'])<span>{{ __('Contact Details') }}</span></span>
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
        <section class="card-rgc branch-show-card">
            <div class="branch-show-header">
                <div>
                    <span class="section-kicker section-kicker--icon">@include('partials.ui.icon', ['name' => 'giving', 'class' => 'section-kicker-icon'])<span>{{ __('Recent Offerings') }}</span></span>
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

        <section class="card-rgc branch-show-card">
            <div class="branch-show-header">
                <div>
                    <span class="section-kicker section-kicker--icon">@include('partials.ui.icon', ['name' => 'archive', 'class' => 'section-kicker-icon'])<span>{{ __('Recent Expenses') }}</span></span>
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
        <section class="card-rgc branch-show-card">
            <div class="branch-show-header">
                <div>
                    <span class="section-kicker section-kicker--icon">@include('partials.ui.icon', ['name' => 'sparkles', 'class' => 'section-kicker-icon'])<span>{{ __('Recent Events') }}</span></span>
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

        <section class="card-rgc branch-show-card">
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
