@extends('layouts.app')

@section('title', __('Users') . ' - RGC')

@section('content')
<div class="card-rgc">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <span class="section-kicker">{{ __('User Governance') }}</span>
            <h1 class="mt-4 text-2xl font-semibold">{{ __('All user accounts') }}</h1>
            <p class="mt-2 text-sm text-black/65">{{ __('Super Admin can create users, promote them into regional, district, or branch leadership, return them to normal member access, reset passwords, and deactivate accounts when needed.') }}</p>
        </div>
        <a class="btn-rgc w-full sm:w-auto" href="{{ route('admin.users.create') }}">{{ __('Add User') }}</a>
    </div>

    <form class="mt-5 flex flex-col gap-3 sm:flex-row" method="GET" action="{{ route('admin.users.index') }}">
        <input class="input-rgc" type="search" name="q" value="{{ $search }}" placeholder="{{ __('Search by name, email, role, or status') }}">
        <button class="btn-rgc-alt w-full sm:w-auto" type="submit">{{ __('Search') }}</button>
    </form>

    <div class="table-wrap mt-5">
        <table class="responsive-table w-full text-sm">
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
                    <tr class="border-t">
                        <td>
                            <div class="font-semibold">{{ $managedUser->name }}</div>
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
                        <td class="break-all">{{ $managedUser->email }}</td>
                        <td>
                            <div>{{ __(Illuminate\Support\Str::headline($managedUser->normalizedRoleName() ?? $managedUser->role)) }}</div>
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
                            <div class="flex flex-col gap-2 sm:flex-row">
                                <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('admin.users.edit', $managedUser) }}">{{ __('Edit') }}</a>
                                @if($managedUser->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $managedUser) }}" onsubmit="return confirm('{{ __('Delete this user account?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-rgc w-full sm:w-auto" type="submit">{{ __('Delete') }}</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-6 text-center text-sm text-black/65">{{ __('No users found for the current search.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $users->links() }}</div>
</div>
@endsection
