@props([
    'href',
    'title',
    'description' => null,
])

<a href="{{ \Mehuli\FilamentShadcn\Support\SafeUrl::filter($href) }}" {{ $attributes->class(['sc-navigation-menu-link']) }}>
    <span class="sc-navigation-menu-link-title">{{ $title }}</span>
    @if (filled($description))
        <span class="sc-navigation-menu-link-description">{{ $description }}</span>
    @endif
</a>
