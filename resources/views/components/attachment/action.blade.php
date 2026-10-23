{{-- Tombol ikon di dalam Attachment. Wajib beri aria-label. --}}
<button type="button" {{ $attributes->class(['sc-attachment-action']) }}>
    {{ $slot }}
</button>
