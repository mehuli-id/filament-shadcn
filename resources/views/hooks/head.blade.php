{{-- Token warna & ukuran shadcn/ui, dibuat dari pengaturan tema. --}}
<style id="filament-shadcn-tokens">{!! $css !!}</style>
<script>
    (function (root) {
        @foreach ($dataAttributes as $key => $value)
            root.dataset[@js($key)] = @js($value);
        @endforeach
    })(document.documentElement);
</script>
