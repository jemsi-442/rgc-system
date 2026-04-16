@php
    $name = $name ?? 'sparkles';
    $size = $size ?? '1em';
    $class = $class ?? '';
    $strokeWidth = $strokeWidth ?? 1.9;
@endphp
<svg viewBox="0 0 24 24" aria-hidden="true" class="{{ $class }}" width="{{ $size }}" height="{{ $size }}" fill="none" stroke="currentColor" stroke-width="{{ $strokeWidth }}" stroke-linecap="round" stroke-linejoin="round">
    @switch($name)
        @case('home')
            <path d="M3 10.5 12 3l9 7.5" />
            <path d="M5.5 9.5V20h13V9.5" />
            <path d="M9.5 20v-6h5v6" />
            @break

        @case('megaphone')
            <path d="M4 12v-2.5c0-.8.5-1.5 1.2-1.7l9.8-2.8v12l-9.8-2.8A1.8 1.8 0 0 1 4 12Z" />
            <path d="M15 7.5a5 5 0 0 1 0 9" />
            <path d="M7 15.5 8.5 20" />
            @break

        @case('chat')
            <path d="M5 6.5h14A1.5 1.5 0 0 1 20.5 8v8A1.5 1.5 0 0 1 19 17.5H11l-4.5 3v-3H5A1.5 1.5 0 0 1 3.5 16V8A1.5 1.5 0 0 1 5 6.5Z" />
            @break

        @case('giving')
            <path d="M12 21s-6.5-3.7-8.5-7.9c-1.3-2.7.1-6.1 3-7.1 2-.7 4.1.1 5.5 2 1.4-1.9 3.5-2.7 5.5-2 2.9 1 4.3 4.4 3 7.1C18.5 17.3 12 21 12 21Z" />
            <path d="M10 10h4" />
            <path d="M12 8v4" />
            @break

        @case('user')
            <circle cx="12" cy="8" r="3.25" />
            <path d="M5.5 19a6.5 6.5 0 0 1 13 0" />
            @break

        @case('lock')
            <rect x="5.5" y="10.5" width="13" height="9" rx="2" />
            <path d="M8.5 10.5V8a3.5 3.5 0 0 1 7 0v2.5" />
            @break

        @case('users')
            <circle cx="9" cy="9" r="2.75" />
            <path d="M4.5 18a4.5 4.5 0 0 1 9 0" />
            <circle cx="16.5" cy="10" r="2.25" />
            <path d="M14 18a4 4 0 0 1 6.5-2.8" />
            @break

        @case('church')
            <path d="M12 3v18" />
            <path d="M8 6h8" />
            <path d="M10 3h4" />
            <path d="M6.5 21V10.5L12 8l5.5 2.5V21" />
            <path d="M10 21v-4h4v4" />
            @break

        @case('image')
            <rect x="3.5" y="5" width="17" height="14" rx="2" />
            <circle cx="9" cy="10" r="1.4" />
            <path d="m6.5 17 4.2-4.2 2.6 2.6 2.2-2.2 2 1.8" />
            @break

        @case('assistant')
            <path d="M9 18h6" />
            <path d="M10 21h4" />
            <path d="M8.5 14.5c-1.2-.9-2-2.4-2-4.1a5.5 5.5 0 1 1 11 0c0 1.7-.8 3.2-2 4.1-.8.6-1.3 1.6-1.5 2.6h-4c-.2-1-.7-2-1.5-2.6Z" />
            @break

        @case('plus')
            <path d="M12 5v14" />
            <path d="M5 12h14" />
            @break

        @case('search')
            <circle cx="11" cy="11" r="5.5" />
            <path d="m16 16 4 4" />
            @break

        @case('filter')
            <path d="M4 6h16" />
            <path d="M7 12h10" />
            <path d="M10 18h4" />
            @break

        @case('archive')
            <path d="M4 7.5h16" />
            <path d="M6 7.5V18a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7.5" />
            <path d="M9.5 11.5h5" />
            <path d="M5 4h14v3.5H5z" />
            @break

        @case('eye')
            <path d="M2.5 12s3.3-5 9.5-5 9.5 5 9.5 5-3.3 5-9.5 5-9.5-5-9.5-5Z" />
            <circle cx="12" cy="12" r="2.5" />
            @break

        @case('edit')
            <path d="M4 20h4l10-10-4-4L4 16v4Z" />
            <path d="m12.5 7.5 4 4" />
            <path d="M14 5.5 16.5 3a1.8 1.8 0 0 1 2.5 2.5L16.5 8" />
            @break

        @case('trash')
            <path d="M4.5 7.5h15" />
            <path d="M9.5 3.5h5" />
            <path d="M7.5 7.5 8 19a1.5 1.5 0 0 0 1.5 1.5h5A1.5 1.5 0 0 0 16 19l.5-11.5" />
            <path d="M10 11v5" />
            <path d="M14 11v5" />
            @break

        @case('sparkles')
        @default
            <path d="m12 3 1.2 3.3L16.5 7.5l-3.3 1.2L12 12l-1.2-3.3L7.5 7.5l3.3-1.2L12 3Z" />
            <path d="m18 13 .8 2.2L21 16l-2.2.8L18 19l-.8-2.2L15 16l2.2-.8.8-2.2Z" />
            <path d="m6 14 .9 2.4L9.3 17l-2.4.9L6 20.3l-.9-2.4L2.7 17l2.4-.6L6 14Z" />
    @endswitch
</svg>
