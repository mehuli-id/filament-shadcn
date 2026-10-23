{{--
    Attachment — berkas/gambar dengan pratinjau, nama, info, status unggah, dan aksi.
    <x-shadcn::attachment name="laporan.pdf" description="PDF · 1,2 MB" state="uploading" :progress="64">
        <x-slot:actions>
            <x-shadcn::attachment.action aria-label="Batalkan unggah laporan.pdf"><x-shadcn::icon name="x" /></x-shadcn::attachment.action>
        </x-slot:actions>
    </x-shadcn::attachment>
--}}
@props([
    'name' => null,
    'description' => null,
    'state' => 'done', // idle | uploading | processing | error | done
    'size' => 'default', // default | sm | xs
    'orientation' => 'horizontal', // horizontal | vertical
    'image' => null, // URL gambar pratinjau
    'href' => null, // seluruh kartu jadi tautan
    'progress' => null, // 0–100, tampil saat uploading/processing
])

<div
    {{ $attributes->class([
        'sc-attachment',
        'sc-attachment-size-' . $size,
        'sc-attachment-' . $orientation,
    ]) }}
    data-state="{{ $state }}"
    @if ($state === 'error') role="alert" @endif
>
    <div @class(['sc-attachment-media', 'sc-attachment-media-image' => filled($image)])>
        @if (filled($image))
            <img src="{{ $image }}" alt="" />
        @elseif (isset($media))
            {{ $media }}
        @else
            <x-shadcn::icon name="file" />
        @endif
    </div>

    <div class="sc-attachment-content">
        <span class="sc-attachment-title">{{ $name ?? $slot }}</span>

        @if (filled($description))
            <span class="sc-attachment-description">{{ $description }}</span>
        @endif

        @if ($progress !== null && in_array($state, ['uploading', 'processing'], true))
            <span class="sc-attachment-progress" aria-hidden="true">
                <span style="width: {{ max(0, min(100, (int) $progress)) }}%"></span>
            </span>
        @endif
    </div>

    @isset($actions)
        <div class="sc-attachment-actions">{{ $actions }}</div>
    @endisset

    @if (filled($href))
        <a href="{{ \Mehuli\FilamentShadcn\Support\SafeUrl::filter($href) }}" class="sc-attachment-trigger">
            <span class="sc-sr-only">{{ $name }}</span>
        </a>
    @endif
</div>
