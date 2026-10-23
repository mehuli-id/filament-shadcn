{{-- Skeleton — placeholder saat memuat: <x-shadcn::skeleton width="12rem" height="1rem" />, shape="circle" --}}
@props([
    'width' => null,
    'height' => null,
    'shape' => 'rect', // rect | circle | pill
])

<div
    aria-hidden="true"
    {{ $attributes->class(['sc-skeleton', 'sc-skeleton-' . $shape])->style([
        $width ? 'width: ' . $width : '',
        $height ? 'height: ' . $height : '',
    ]) }}
></div>
