{{-- Aspect Ratio: <x-shadcn::aspect-ratio ratio="16/9"><img src="…" alt="…"></x-shadcn::aspect-ratio> --}}
@props([
    'ratio' => '16/9',
])

<div {{ $attributes->class(['sc-aspect-ratio'])->style(['aspect-ratio: ' . $ratio]) }}>
    {{ $slot }}
</div>
