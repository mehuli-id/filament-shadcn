{{--
    Bubble — bingkai isi percakapan.
    <x-shadcn::bubble variant="tinted" align="end">Sudah live, siap direview.
        <x-slot:reactions>🎉</x-slot:reactions>
    </x-shadcn::bubble>
--}}
@props([
    'variant' => 'default', // default | secondary | muted | tinted | outline | ghost | destructive
    'align' => 'start', // start | end
    'reactionsSide' => 'bottom', // bottom | top
    'reactionsAlign' => 'end', // end | start
    'href' => null,
])

<div
    {{ $attributes->class(['sc-bubble', 'sc-bubble-' . $variant]) }}
    data-align="{{ $align }}"
>
    @if (filled($href))
        <a href="{{ \Mehuli\FilamentShadcn\Support\SafeUrl::filter($href) }}" class="sc-bubble-content">{{ $slot }}</a>
    @else
        <div class="sc-bubble-content">{{ $slot }}</div>
    @endif

    @isset($reactions)
        <div class="sc-bubble-reactions" data-side="{{ $reactionsSide }}" data-align="{{ $reactionsAlign }}">
            {{ $reactions }}
        </div>
    @endisset
</div>
