{{-- Spinner — indikator memuat: <x-shadcn::spinner />, size="sm|default|lg", label="Menyimpan" --}}
@props([
    'size' => 'default',
    'label' => null, // bawaan: terjemahan ui.spinner.label
])

<span role="status" {{ $attributes->class(['sc-spinner', 'sc-spinner-' . $size]) }}>
    <x-shadcn::icon name="loader" class="sc-spinner-icon" />
    <span class="sc-sr-only">{{ $label ?? __('filament-shadcn::filament-shadcn.ui.spinner.label') }}</span>
</span>
