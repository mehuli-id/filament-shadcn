{{-- Separator — <x-shadcn::separator />, <x-shadcn::separator orientation="vertical" />, <x-shadcn::separator label="atau" /> --}}
@props([
    'orientation' => 'horizontal',
    'decorative' => true,
    'label' => null,
])

@if (filled($label))
    <div {{ $attributes->class(['sc-separator-labeled']) }} role="separator" aria-label="{{ $label }}">
        <span class="sc-separator-labeled-text">{{ $label }}</span>
    </div>
@else
    <div
        @if ($decorative) role="none" @else role="separator" aria-orientation="{{ $orientation }}" @endif
        {{ $attributes->class(['sc-separator', 'sc-separator-' . $orientation]) }}
    ></div>
@endif
