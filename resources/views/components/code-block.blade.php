{{--
    Code Block — kode, output terminal, log, dan diff.
    <x-shadcn::code-block title="app/Models/User.php" language="php" :code="$code" line-numbers :highlight="[3, 4]" />
    Mode diff: baris berawalan "+" / "-" otomatis jadi baris tambah/hapus.
--}}
@props([
    'code' => null,
    'title' => null,
    'language' => null,
    'lineNumbers' => false,
    'startLine' => 1,
    'highlight' => [],
    'diff' => false,
    'wrap' => false,
    'size' => 'default', // default | sm
    'variant' => 'default', // default | destructive
    'copyable' => true,
    'maxHeight' => null, // mis. '18rem'
])

@php
    $text = $code ?? html_entity_decode(trim((string) $slot), ENT_QUOTES | ENT_HTML5);
    $lines = preg_split('/\r\n|\n|\r/', rtrim($text, "\r\n"));
    $highlight = array_map('intval', (array) $highlight);
@endphp

<div
    {{ $attributes->class([
        'sc-code-block',
        'sc-code-block-size-' . $size,
        'sc-code-block-' . $variant,
        'sc-code-block-wrap' => $wrap,
        'sc-code-block-has-header' => filled($title) || filled($language) || isset($actions),
    ]) }}
    x-data="{
        copied: false,
        copy() {
            navigator.clipboard.writeText(@js($text)).then(() => {
                this.copied = true
                setTimeout(() => (this.copied = false), 1500)
            })
        },
    }"
>
    @if (filled($title) || filled($language) || isset($actions))
        <div class="sc-code-block-header">
            <span class="sc-code-block-title">{{ $title }}</span>

            <div class="sc-code-block-actions">
                @if (filled($language))
                    <span class="sc-code-block-language">{{ $language }}</span>
                @endif

                {{ $actions ?? '' }}

                @if ($copyable)
                    <button type="button" class="sc-code-block-copy" x-on:click="copy()" x-bind:aria-label="copied ? 'Tersalin' : 'Salin kode'">
                        <x-shadcn::icon name="copy" x-show="! copied" />
                        <x-shadcn::icon name="check" x-show="copied" x-cloak />
                    </button>
                @endif
            </div>
        </div>
    @elseif ($copyable)
        <button type="button" class="sc-code-block-copy sc-code-block-copy-floating" x-on:click="copy()" x-bind:aria-label="copied ? 'Tersalin' : 'Salin kode'">
            <x-shadcn::icon name="copy" x-show="! copied" />
            <x-shadcn::icon name="check" x-show="copied" x-cloak />
        </button>
    @endif

    <pre
        class="sc-code-block-content"
        tabindex="0"
        @if ($maxHeight) style="max-height: {{ $maxHeight }}" @endif
        @if (filled($language)) data-language="{{ $language }}" @endif
    ><code>@foreach ($lines as $index => $line)@php
        $number = $startLine + $index;
        $lineVariant = in_array($number, $highlight, true) ? 'highlighted' : null;

        if ($diff && str_starts_with($line, '+')) {
            $lineVariant = 'added';
            $line = substr($line, 1);
        } elseif ($diff && str_starts_with($line, '-')) {
            $lineVariant = 'removed';
            $line = substr($line, 1);
        }
    @endphp<span class="sc-code-line" @if ($lineVariant) data-variant="{{ $lineVariant }}" @endif>@if ($lineNumbers)<span class="sc-code-line-number" aria-hidden="true">{{ $number }}</span>@endif<span class="sc-code-line-text">{{ $line === '' ? ' ' : $line }}</span></span>@endforeach</code></pre>

    @isset($footer)
        <div class="sc-code-block-footer">{{ $footer }}</div>
    @endisset
</div>
