@extends('layouts.app')

@section('title', __('Users') . ' - RGC')

@section('content')
<div class="card-rgc admin-console-shell admin-console-shell--users">
    <div class="user-admin-header">
        <div>
            <span class="section-kicker section-kicker--icon">@include('partials.ui.icon', ['name' => 'users', 'class' => 'section-kicker-icon'])<span>{{ __('Church Accounts') }}</span></span>
            <h1 class="mt-4 text-2xl font-semibold">{{ __('All user accounts') }}</h1>
            <p class="mt-2 text-sm text-black/65">{{ __('Super Admin can create church accounts, place people into regional, district, or branch leadership, return them to normal member access, reset passwords, and deactivate accounts when needed.') }}</p>
        </div>
        <a class="btn-rgc w-full sm:w-auto" href="{{ route('admin.users.create') }}">@include('partials.ui.icon', ['name' => 'plus', 'class' => 'button-icon'])<span>{{ __('Add account') }}</span></a>
    </div>

    <div class="user-admin-summary mt-5">
        <article class="user-admin-summary-card">
            <span>{{ __('Accounts in view') }}</span>
            <strong>{{ number_format($users->total()) }}</strong>
            <p>{{ __('All accounts matching the current search and page view.') }}</p>
        </article>
        <article class="user-admin-summary-card">
            <span>{{ __('Current search') }}</span>
            <strong>{{ filled($search) ? __('Filtered') : __('All users') }}</strong>
            <p>{{ filled($search) ? __('Showing the accounts that match your current search.') : __('No search filter is active right now.') }}</p>
        </article>
    </div>

    <form class="user-admin-search mt-5" method="GET" action="{{ route('admin.users.index') }}">
        <div class="user-admin-search-field">
            <input class="input-rgc" type="search" name="q" value="{{ $search }}" placeholder="{{ __('Search by name, email, role, or status') }}">
        </div>
        <button class="btn-rgc-alt w-full sm:w-auto" type="submit">@include('partials.ui.icon', ['name' => 'search', 'class' => 'button-icon'])<span>{{ __('Search') }}</span></button>
    </form>

    <div class="table-wrap mt-5 user-admin-table-wrap">
        <table class="responsive-table w-full text-sm user-admin-table">
            <thead>
                <tr>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Role') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Branch') }}</th>
                    <th>{{ __('District') }}</th>
                    <th>{{ __('Region') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $managedUser)
                    <tr class="border-t user-admin-row">
                        <td>
                            <div class="user-admin-name">{{ $managedUser->name }}</div>
                            <div class="mt-1 text-xs text-black/55">
                                {{ $managedUser->branch?->name ?? __('No branch assigned') }}
                                @if($managedUser->district?->name)
                                    · {{ $managedUser->district->name }}
                                @endif
                                @if($managedUser->region?->name)
                                    · {{ $managedUser->region->name }}
                                @endif
                            </div>
                            @if($managedUser->id === auth()->id())
                                <div class="mt-1 text-xs text-black/50">{{ __('Current account') }}</div>
                            @endif
                        </td>
                        <td class="break-all">
                            <div class="user-admin-email">{{ $managedUser->email }}</div>
                        </td>
                        <td>
                            <div class="user-admin-role">{{ __(Illuminate\Support\Str::headline($managedUser->normalizedRoleName() ?? $managedUser->role)) }}</div>
                            @if($managedUser->phone)
                                <div class="mt-1 text-xs text-black/55">{{ $managedUser->phone }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $managedUser->isActive() ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $managedUser->isActive() ? __('Active') : __('Inactive') }}
                            </span>
                        </td>
                        <td>{{ $managedUser->branch?->name ?? '—' }}</td>
                        <td>{{ $managedUser->district?->name ?? '—' }}</td>
                        <td>{{ $managedUser->region?->name ?? '—' }}</td>
                        <td>
                            <div class="user-admin-actions">
                                <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('admin.users.edit', $managedUser) }}">@include('partials.ui.icon', ['name' => 'edit', 'class' => 'button-icon'])<span>{{ __('Edit') }}</span></a>
                                @if($managedUser->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $managedUser) }}" onsubmit="return confirm('{{ __('Delete this user account?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-rgc w-full sm:w-auto" type="submit">@include('partials.ui.icon', ['name' => 'trash', 'class' => 'button-icon'])<span>{{ __('Delete') }}</span></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-6 text-center text-sm text-black/65">{{ __('No accounts were found for the current search.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $users->links() }}</div>
</div>
@endsection
