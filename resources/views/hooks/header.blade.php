{{-- Header shadcn di dalam kartu konten (ganti topbar Filament). Lihat docs/mockup/gambar/Shadcn.png. --}}
@php
    use Filament\Enums\DatabaseNotificationsPosition;
    use Filament\Support\Icons\Heroicon;

    $page = request()->attributes->get('filament-shadcn.page');
    $breadcrumbs = ($page && method_exists($page, 'getBreadcrumbs')) ? $page->getBreadcrumbs() : [];
    $title = ($page && method_exists($page, 'getTitle')) ? $page->getTitle() : null;

    $isCollapsibleOnDesktop = filament()->isSidebarCollapsibleOnDesktop() || filament()->isSidebarFullyCollapsibleOnDesktop();
    $isAuthenticated = filament()->auth()->check();
    $hasThemeSwitcher = filament()->hasDarkMode() && (! filament()->hasDarkModeForced());
    $hasNotifications = $isAuthenticated
        && filament()->hasDatabaseNotifications()
        && filament()->getDatabaseNotificationsPosition() === DatabaseNotificationsPosition::Topbar;
@endphp

<header @class(['sc-header', 'sc-header-collapsible' => $isCollapsibleOnDesktop])>
    @if (filament()->hasNavigation())
        <button
            type="button"
            x-data="{}"
            x-on:click="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
            aria-controls="fi-main-sidebar"
            x-bind:aria-expanded="$store.sidebar.isOpen"
            aria-label="{{ __('filament-shadcn::filament-shadcn.shell.toggle_sidebar') }}"
            class="sc-header-sidebar-btn"
        >
            {{ \Filament\Support\generate_icon_html(Heroicon::OutlinedBars3, attributes: new \Illuminate\View\ComponentAttributeBag(['class' => 'sc-header-icon-mobile'])) }}
            <svg class="sc-header-icon-desktop" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18"/></svg>
        </button>

        <span class="sc-header-separator" aria-hidden="true"></span>
    @endif

    <a href="{{ filament()->getHomeUrl() ?? '#' }}" class="sc-header-brand">{{ filament()->getBrandName() }}</a>

    <div class="sc-header-title">
        @if (count($breadcrumbs) > 1)
            <nav aria-label="{{ __('filament-shadcn::filament-shadcn.shell.breadcrumbs') }}">
                <ol class="sc-header-breadcrumbs">
                    @foreach ($breadcrumbs as $url => $label)
                        <li>
                            @if (is_string($url) && ! $loop->last)
                                <a href="{{ $url }}">{{ $label }}</a>
                            @else
                                <span @if ($loop->last) aria-current="page" @endif>{{ $label }}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        @elseif (filled($title))
            <h1 class="sc-header-heading">{{ $title }}</h1>
        @endif
    </div>

    <div class="sc-header-end">
        @if (filament()->isGlobalSearchEnabled())
            @livewire(Filament\Livewire\GlobalSearch::class)
        @endif

        @if ($hasThemeSwitcher)
            @include('filament-shadcn::partials.theme-switcher')
        @endif

        @if ($hasNotifications)
            @livewire(filament()->getDatabaseNotificationsLivewireComponent(), [
                'lazy' => filament()->hasLazyLoadedDatabaseNotifications(),
            ])
        @endif

        @foreach ($links as $link)
            <a
                href="{{ $link['url'] }}"
                @if ($link['external']) target="_blank" rel="noopener noreferrer" @endif
                class="sc-header-link"
            >{{ $link['label'] }}</a>
        @endforeach

        @if ($isAuthenticated)
            {{-- Ponsel: avatar di header. Menu pengguna lengkap ada di kaki sidebar (butuh konteks Livewire). --}}
            @if (filament()->hasProfile())
                <a href="{{ filament()->getProfileUrl() }}" class="sc-header-user" aria-label="{{ filament()->getUserName(filament()->auth()->user()) }}">
                    <x-filament-panels::avatar.user :user="filament()->auth()->user()" />
                </a>
            @else
                <button type="button" x-data="{}" x-on:click="$store.sidebar.open()" class="sc-header-user" aria-label="{{ filament()->getUserName(filament()->auth()->user()) }}">
                    <x-filament-panels::avatar.user :user="filament()->auth()->user()" />
                </button>
            @endif
        @endif
    </div>
</header>
