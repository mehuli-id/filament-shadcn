@props([
    'src' => null,
    'alt' => '',
    'initials' => null,
])

<div {{ $attributes->class(['sc-message-avatar']) }}>
    @if (filled($src))
        <img src="{{ $src }}" alt="{{ $alt }}" class="sc-avatar" />
    @elseif (filled($initials))
        <span class="sc-avatar sc-avatar-fallback" @if (filled($alt)) aria-label="{{ $alt }}" @else aria-hidden="true" @endif>{{ $initials }}</span>
    @else
        {{ $slot }}
    @endif
</div>
