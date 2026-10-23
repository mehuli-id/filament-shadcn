{{-- Panel gambar untuk layout login "split" (form + gambar). --}}
<aside class="sc-auth-aside" aria-hidden="{{ $tagline ? 'false' : 'true' }}">
    @if (filled($image))
        <img
            class="sc-auth-aside-image"
            src="{{ str($image)->startsWith(['http://', 'https://', '/', 'data:']) ? $image : asset($image) }}"
            alt=""
            loading="eager"
        />
    @else
        <div class="sc-auth-aside-placeholder">
            <svg viewBox="0 0 400 560" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                @foreach ([40, 80, 120, 170, 230, 300] as $i => $r)
                    <circle cx="300" cy="140" r="{{ $r }}" fill="none" stroke="currentColor" stroke-opacity="{{ 0.5 - $i * 0.07 }}" />
                @endforeach
                @for ($y = 0; $y < 5; $y++)
                    @for ($x = 0; $x < 8; $x++)
                        <circle cx="{{ 30 + $x * 24 }}" cy="{{ 300 + $y * 24 }}" r="1.5" fill="currentColor" fill-opacity="0.35" />
                    @endfor
                @endfor
            </svg>
        </div>
    @endif

    @if (filled($tagline))
        <figure class="sc-auth-aside-quote">
            <blockquote>{{ $tagline }}</blockquote>
            @if (filled($taglineAuthor))
                <figcaption>{{ $taglineAuthor }}</figcaption>
            @endif
        </figure>
    @endif
</aside>
