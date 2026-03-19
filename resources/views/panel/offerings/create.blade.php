@extends('layouts.app')
@section('content')
<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
    <div class="card-rgc">
        <div class="space-y-2">
            <p class="section-kicker">{{ __('Manual entry') }}</p>
            <h1 class="text-xl font-semibold">{{ __('Record Offering') }}</h1>
            <p class="text-sm text-black/65">{{ __('Use this when the branch has already received the money offline and you only need to record it in the ledger.') }}</p>
        </div>
        <form class="mt-4 grid gap-4" method="POST" action="{{ route('offerings.store') }}">
            @csrf
            <div>
                <label class="field-label" for="offering_date">{{ __('Offering date') }}</label>
                <input class="input-rgc" id="offering_date" type="date" name="offering_date" value="{{ old('offering_date') }}" required>
            </div>
            <div>
                <label class="field-label" for="amount">{{ __('Amount') }}</label>
                <input class="input-rgc" id="amount" type="number" step="0.01" name="amount" value="{{ old('amount') }}" placeholder="{{ __('Amount') }}" required>
            </div>
            <div>
                <label class="field-label" for="description">{{ __('Description') }}</label>
                <textarea class="textarea-rgc min-h-32" id="description" name="description" placeholder="{{ __('Description') }}">{{ old('description') }}</textarea>
            </div>
            <div class="form-actions">
                <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Save') }}</button>
            </div>
        </form>
    </div>

    <div class="card-rgc">
        <div class="space-y-2">
            <p class="section-kicker">{{ __('Snippe checkout') }}</p>
            <h2 class="text-xl font-semibold">{{ __('Create Payment Link') }}</h2>
            <p class="text-sm text-black/65">{{ __('Generate a secure Snippe checkout link for members or donors, then let the webhook confirm the payment and record the offering automatically.') }}</p>
        </div>

        @error('snippe')
            <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $message }}</div>
        @enderror

        <form class="mt-4 grid gap-4" method="POST" action="{{ route('offerings.payments.store') }}">
            @csrf
            <div>
                <label class="field-label" for="payment_offering_date">{{ __('Offering date') }}</label>
                <input class="input-rgc" id="payment_offering_date" type="date" name="offering_date" value="{{ old('offering_date', now()->toDateString()) }}">
            </div>
            <div>
                <label class="field-label" for="payment_type">{{ __('Giving type') }}</label>
                <select class="input-rgc" id="payment_type" name="payment_type">
                    <option value="offering">{{ __('Offering') }}</option>
                    <option value="sadaka" @selected(old('payment_type') === 'sadaka')>{{ __('Sadaka') }}</option>
                    <option value="thanksgiving" @selected(old('payment_type') === 'thanksgiving')>{{ __('Thanksgiving') }}</option>
                    <option value="special_contribution" @selected(old('payment_type') === 'special_contribution')>{{ __('Special Contribution') }}</option>
                    <option value="project_support" @selected(old('payment_type') === 'project_support')>{{ __('Project Support') }}</option>
                </select>
            </div>
            <div>
                <label class="field-label" for="payment_amount">{{ __('Amount') }}</label>
                <input class="input-rgc" id="payment_amount" type="number" step="0.01" name="amount" value="{{ old('amount') }}" required>
            </div>
            <div>
                <label class="field-label" for="payer_name">{{ __('Payer name') }}</label>
                <input class="input-rgc" id="payer_name" type="text" name="payer_name" value="{{ old('payer_name') }}" placeholder="{{ __('Full name') }}" required>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="payer_phone">{{ __('Phone number') }}</label>
                    <input class="input-rgc" id="payer_phone" type="text" name="payer_phone" value="{{ old('payer_phone') }}" placeholder="2557XXXXXXXX">
                </div>
                <div>
                    <label class="field-label" for="payer_email">{{ __('Email address') }}</label>
                    <input class="input-rgc" id="payer_email" type="email" name="payer_email" value="{{ old('payer_email') }}" placeholder="name@example.com">
                </div>
            </div>
            <div>
                <label class="field-label" for="payment_description">{{ __('Description') }}</label>
                <textarea class="textarea-rgc min-h-32" id="payment_description" name="description" placeholder="{{ __('Sunday giving, thanksgiving, special contribution, or any branch-specific note.') }}">{{ old('description') }}</textarea>
            </div>
            <div class="announcement-callout">
                <p class="font-semibold text-black">{{ __('What happens next?') }}</p>
                <p class="mt-2 text-sm text-black/70">{{ __('The system will create a Snippe hosted checkout link, then wait for the secure webhook before posting the final offering to your branch ledger.') }}</p>
            </div>
            <div class="form-actions">
                <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Create payment link') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
