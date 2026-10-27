<?php

namespace Mehuli\FilamentShadcn;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Enums\DatabaseNotificationsPosition;
use Filament\Enums\UserMenuPosition;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Mehuli\FilamentShadcn\Pages\Appearance;
use Mehuli\FilamentShadcn\Pages\ComponentShowcase;
use Mehuli\FilamentShadcn\Pages\ShadcnComponents;
use Mehuli\FilamentShadcn\Pages\ShowcaseDashboard;
use Mehuli\FilamentShadcn\Pages\ShowcaseForm;
use Mehuli\FilamentShadcn\Support\AppearanceRepository;
use Mehuli\FilamentShadcn\Support\SafeUrl;
use Mehuli\FilamentShadcn\Support\ThemeSettings;
use Mehuli\FilamentShadcn\Support\TokenBuilder;

class ShadcnPlugin implements Plugin
{
    /**
     * Pengaturan lewat kode (lebih kuat dari config, kalah dari halaman Tampilan).
     *
     * @var array<string, mixed>
     */
    protected array $overrides = [];

    protected bool | Closure | null $hasAppearancePage = null;

    protected bool | Closure | null $hasShowcase = null;

    protected bool | Closure | null $canCustomizeAppearance = null;

    protected bool | Closure | null $hasShell = null;

    protected ?string $sidebarWidth = null;

    protected ?string $brandIcon = null;

    protected string | Closure | null $brandSubtitle = null;

    /**
     * @var list<array{label: string, url: string}> | null
     */
    protected ?array $headerLinks = null;

    /**
     * Lebar sidebar yang diterima (dicetak Filament tanpa escape di <style>).
     */
    public const SIDEBAR_WIDTH_PATTERN = '/^\d{1,3}(\.\d{1,3})?(rem|px|em)$/';

    protected ?ThemeSettings $resolved = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function getId(): string
    {
        return 'filament-shadcn';
    }

    /*
    |--------------------------------------------------------------------------
    | Pengaturan fluent
    |--------------------------------------------------------------------------
    */

    /**
     * vega | nova | maia | lyra | mira
     */
    public function style(string $style): static
    {
        return $this->set('style', $style);
    }

    /**
     * neutral | zinc | slate | stone | gray | mauve | olive | mist | taupe
     */
    public function baseColor(string $color): static
    {
        return $this->set('base_color', $color);
    }

    /**
     * 'default', nama warna Tailwind, atau kode warna (#hex, rgb(), oklch()).
     */
    public function primary(string $color): static
    {
        return $this->set('primary', $color);
    }

    /**
     * @param  array{danger?: string, success?: string, warning?: string, info?: string}  $colors
     */
    public function colors(array $colors): static
    {
        return $this->set('colors', $colors);
    }

    public function radius(float $rem): static
    {
        return $this->set('radius', $rem);
    }

    public function density(float $scale): static
    {
        return $this->set('density', $scale);
    }

    /**
     * @param  class-string | null  $provider
     */
    public function font(?string $sans = null, ?string $mono = null, ?string $provider = null, ?string $heading = null): static
    {
        $this->set('font_sans', $sans);
        $this->set('font_mono', $mono);
        $this->set('font_heading', $heading);

        return $this->set('font_provider', $provider);
    }

    /**
     * Radius kartu & kontrol tersendiri (rem). null = turunan radius dasar.
     */
    public function radii(?float $card = null, ?float $control = null): static
    {
        return $this->set('radii', array_filter(['card' => $card, 'control' => $control], static fn (?float $value): bool => $value !== null));
    }

    /**
     * Shade palet utama yang ditentukan sendiri, mis. [500 => '#f59e0b', 600 => '#b45309'].
     *
     * @param  array<int, string>  $palette
     */
    public function primaryPalette(array $palette): static
    {
        return $this->set('primary_palette', $palette);
    }

    /**
     * Warna Filament tambahan, mis. ['teal' => 'teal', 'merek' => '#7c3aed'].
     *
     * @param  array<string, string>  $colors
     */
    public function extraColors(array $colors): static
    {
        return $this->set('extra_colors', $colors);
    }

    /**
     * Tebal judul (100–900), label grup sidebar & kepala tabel kapital, angka tabular.
     */
    public function typography(?int $headingWeight = null, ?bool $uppercaseLabels = null, ?bool $tabularNums = null): static
    {
        return $this->set('typography', array_filter([
            'heading_weight' => $headingWeight,
            'uppercase_labels' => $uppercaseLabels,
            'tabular_nums' => $tabularNums,
        ], static fn (mixed $value): bool => $value !== null));
    }

    /**
     * inset | sidebar | floating
     */
    public function sidebarLayout(string $layout): static
    {
        return $this->set('sidebar', $layout);
    }

    /**
     * muted | split | plain
     */
    public function authLayout(string $layout, ?string $image = null, string $imagePosition = 'end', ?string $tagline = null, ?string $taglineAuthor = null): static
    {
        $this->set('auth_layout', $layout);
        $this->set('auth_image', $image);
        $this->set('auth_image_position', $imagePosition);
        $this->set('auth_tagline', $tagline);

        return $this->set('auth_tagline_author', $taglineAuthor);
    }

    /**
     * @param  array{light?: list<string>, dark?: list<string>}  $charts
     */
    public function charts(array $charts): static
    {
        return $this->set('charts', $charts);
    }

    /**
     * Timpa token shadcn apa pun, mis. hasil salin dari tweakcn.
     *
     * @param  array<string, string>  $light
     * @param  array<string, string>  $dark
     */
    public function tokens(array $light = [], array $dark = []): static
    {
        return $this->set('tokens', ['light' => $light, 'dark' => $dark]);
    }

    public function appearancePage(bool | Closure $condition = true): static
    {
        $this->hasAppearancePage = $condition;

        return $this;
    }

    /**
     * Siapa yang boleh membuka halaman Tampilan, mis.
     * ->canCustomizeAppearance(fn () => auth()->user()?->is_admin)
     */
    public function canCustomizeAppearance(bool | Closure $condition): static
    {
        $this->canCustomizeAppearance = $condition;

        return $this;
    }

    /**
     * Header shadcn di dalam kartu konten (seperti docs/mockup) sebagai ganti topbar Filament.
     */
    public function shell(bool | Closure $condition = true): static
    {
        $this->hasShell = $condition;

        return $this;
    }

    /**
     * Lebar sidebar desktop, mis. '16rem'.
     */
    public function sidebarWidth(string $width): static
    {
        $this->sidebarWidth = $width;

        return $this;
    }

    /**
     * Ikon kotak merek dan subjudul di bawah nama merek (sidebar & login).
     */
    public function brand(?string $icon = null, string | Closure | null $subtitle = null): static
    {
        $this->brandIcon = $icon;
        $this->brandSubtitle = $subtitle;

        return $this;
    }

    /**
     * @param  list<array{label: string, url: string}>  $links
     */
    public function headerLinks(array $links): static
    {
        $this->headerLinks = $links;

        return $this;
    }

    public function showcase(bool | Closure $condition = true): static
    {
        $this->hasShowcase = $condition;

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Registrasi ke panel
    |--------------------------------------------------------------------------
    */

    public function register(Panel $panel): void
    {
        // Pakai closure supaya nilai dari halaman Tampilan dibaca saat request.
        $panel->colors(fn (): array => $this->settings()->filamentColors());

        $panel->font(
            fn (): string => $this->settings()->fontSans(),
            provider: fn (): ?string => $this->settings()->fontProvider(),
        );

        $panel->monoFont(
            fn (): string => $this->settings()->fontMono(),
            provider: fn (): ?string => $this->settings()->fontProvider(),
        );

        $pages = [];

        if ($this->hasAppearancePage()) {
            $pages[] = Appearance::class;
        }

        if ($this->hasShowcase()) {
            $pages[] = ComponentShowcase::class;
            $pages[] = ShadcnComponents::class;
            $pages[] = ShowcaseDashboard::class;
            $pages[] = ShowcaseForm::class;
        }

        if ($pages !== []) {
            $panel->pages($pages);
        }

        $panel->renderHook(
            PanelsRenderHook::HEAD_END,
            fn (): View => view('filament-shadcn::hooks.head', [
                'css' => TokenBuilder::make($this->settings())->css(),
                'dataAttributes' => TokenBuilder::make($this->settings())->dataAttributes() + [
                    'scShell' => $this->hasShell() ? 'on' : 'off',
                ],
            ]),
        );

        // Font judul dimuat lewat penyedia font yang sama dengan font isi (bawaan Filament: Bunny).
        // Dengan LocalFontProvider, @font-face disediakan sendiri oleh aplikasi.
        $panel->renderHook(
            PanelsRenderHook::HEAD_END,
            fn (): ?\Illuminate\Contracts\Support\Htmlable => $this->headingFontHtml(),
        );

        $panel->sidebarWidth(fn (): string => $this->getSidebarWidth());

        // Shell: topbar Filament dimatikan, header shadcn dipasang di dalam kartu konten.
        $panel->topbar(fn (): bool => ! $this->hasShell());
        $panel->userMenu(position: fn (): UserMenuPosition => $this->hasShell() ? UserMenuPosition::Sidebar : UserMenuPosition::Topbar);

        // Halaman aktif dicatat saat view halaman dirender (sebelum layout), untuk judul/breadcrumb header.
        $panel->renderHook(
            PanelsRenderHook::PAGE_START,
            function (): string {
                request()->attributes->set('filament-shadcn.page', \Livewire\Livewire::current());

                return '';
            },
        );

        $panel->renderHook(
            PanelsRenderHook::CONTENT_BEFORE,
            fn (): ?View => $this->hasShell() ? view('filament-shadcn::hooks.header', [
                'links' => $this->getHeaderLinks(),
            ]) : null,
        );

        $panel->renderHook(
            PanelsRenderHook::SIDEBAR_LOGO_AFTER,
            fn (): ?View => $this->hasShell() ? view('filament-shadcn::hooks.brand', [
                'icon' => $this->getBrandIcon(),
                'subtitle' => $this->getBrandSubtitle(),
            ]) : null,
        );

        // Halaman login/daftar/lupa sandi: pilihan tema, merek, panel gambar, teks persetujuan.
        $panel->renderHook(
            PanelsRenderHook::SIMPLE_LAYOUT_START,
            fn (): View => $this->renderAuthStart(),
        );

        $panel->renderHook(
            PanelsRenderHook::SIMPLE_PAGE_START,
            fn (): ?View => $this->isGuestAuthPage() && $this->settings()->get('auth_layout') === 'split' && $this->showsAuthBrand()
                ? view('filament-shadcn::hooks.auth-card-start', ['icon' => $this->getBrandIcon()])
                : null,
        );

        // Tombol "Masuk dengan …": di atas form (muted/plain) atau di bawahnya sebagai ikon (split).
        $panel->renderHook(
            PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
            fn (): ?View => $this->renderSocialLogin(before: true),
        );

        $panel->renderHook(
            PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
            fn (): ?View => $this->renderSocialLogin(before: false),
        );

        $panel->renderHook(
            PanelsRenderHook::SIMPLE_LAYOUT_END,
            fn (): ?View => $this->renderAuthTerms(),
        );
    }

    public function boot(Panel $panel): void
    {
        // Tanpa topbar Filament menaruh lonceng notifikasi di kaki sidebar. Dengan shell, lonceng
        // pindah ke header, kecuali posisinya diatur sendiri lewat ->databaseNotifications(position: …).
        if ($this->hasShell()) {
            (function (): void {
                $this->databaseNotificationsPosition ??= DatabaseNotificationsPosition::Topbar;
            })->call($panel);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    public function settings(): ThemeSettings
    {
        return $this->resolved ??= ThemeSettings::resolve(
            $this->overrides,
            $this->hasAppearancePage() ? app(AppearanceRepository::class)->all() : [],
        );
    }

    /**
     * Pengaturan tanpa nilai dari halaman Tampilan (dipakai sebagai "bawaan").
     */
    public function defaultSettings(): ThemeSettings
    {
        return ThemeSettings::resolve($this->overrides);
    }

    public function flushSettings(): void
    {
        $this->resolved = null;
    }

    public function hasAppearancePage(): bool
    {
        $condition = $this->hasAppearancePage ?? config('filament-shadcn.appearance_page.enabled', true);

        return (bool) ($condition instanceof Closure ? app()->call($condition) : $condition);
    }

    public function canAccessAppearance(): bool
    {
        // Bawaan yang aman: tanpa ->canCustomizeAppearance(...), halaman Tampilan
        // hanya bisa dibuka di APP_ENV=local. Di production wajib ditentukan siapa yang boleh.
        $condition = $this->canCustomizeAppearance ?? fn (): bool => app()->isLocal();

        return (bool) ($condition instanceof Closure ? app()->call($condition) : $condition);
    }

    public function hasShell(): bool
    {
        $condition = $this->hasShell ?? config('filament-shadcn.layout.shell', true);

        return (bool) ($condition instanceof Closure ? app()->call($condition) : $condition);
    }

    public function headingFontHtml(): ?\Illuminate\Contracts\Support\Htmlable
    {
        $settings = $this->settings();
        $heading = $settings->fontHeading();

        if ($heading === null || $heading === $settings->fontSans()) {
            return null;
        }

        $provider = $settings->fontProvider() ?? \Filament\FontProviders\BunnyFontProvider::class;

        if (! is_a($provider, \Filament\FontProviders\Contracts\FontProvider::class, true) || $provider === \Filament\FontProviders\LocalFontProvider::class) {
            return null;
        }

        return app($provider)->getHtml($heading);
    }

    public function getSidebarWidth(): string
    {
        $width = trim((string) ($this->sidebarWidth ?? config('filament-shadcn.layout.sidebar_width', '16rem')));

        return preg_match(static::SIDEBAR_WIDTH_PATTERN, $width) ? $width : '16rem';
    }

    public function getBrandIcon(): ?string
    {
        $icon = $this->brandIcon ?? config('filament-shadcn.layout.brand_icon', 'heroicon-o-home');

        return filled($icon) ? (string) $icon : null;
    }

    public function getBrandSubtitle(): ?string
    {
        $subtitle = $this->brandSubtitle ?? config('filament-shadcn.layout.brand_subtitle');
        $subtitle = $subtitle instanceof Closure ? app()->call($subtitle) : $subtitle;

        return filled($subtitle) ? __((string) $subtitle) : null;
    }

    /**
     * Tautan header yang aman (URL lewat SafeUrl, label lewat terjemahan).
     *
     * @return list<array{label: string, url: string, external: bool}>
     */
    public function getHeaderLinks(): array
    {
        $links = [];

        foreach ((array) ($this->headerLinks ?? config('filament-shadcn.layout.header_links', [])) as $link) {
            $url = SafeUrl::filter($link['url'] ?? null);

            if ($url === null || blank($link['label'] ?? null)) {
                continue;
            }

            $links[] = [
                'label' => __((string) $link['label']),
                'url' => $url,
                'external' => str_starts_with($url, 'http') && ! str_starts_with($url, url('/')),
            ];
        }

        return $links;
    }

    public function hasShowcase(): bool
    {
        $condition = $this->hasShowcase ?? config('filament-shadcn.showcase.enabled', false);

        return (bool) ($condition instanceof Closure ? app()->call($condition) : $condition);
    }

    protected function isGuestAuthPage(): bool
    {
        return ! filament()->auth()->check();
    }

    /**
     * Kotak ikon + nama merek dipakai bila panel tanpa ->brandLogo() (logo Filament tetap dipakai bila ada).
     */
    protected function showsAuthBrand(): bool
    {
        return blank(filament()->getBrandLogo()) && filled($this->getBrandIcon());
    }

    protected function renderAuthStart(): View
    {
        $settings = $this->settings();

        return view('filament-shadcn::hooks.auth-start', [
            'isGuest' => $this->isGuestAuthPage(),
            'layout' => $settings->get('auth_layout'),
            'showBrand' => $this->showsAuthBrand(),
            'icon' => $this->getBrandIcon(),
            'image' => $this->authImageUrl($settings),
            'tagline' => $settings->get('auth_tagline'),
            'taglineAuthor' => $settings->get('auth_tagline_author'),
            'brand' => filament()->getBrandName(),
        ]);
    }

    /**
     * @return list<array{label: string, url: string, icon: ?string}>
     */
    public function getSocialLoginProviders(): array
    {
        $providers = [];

        foreach ((array) config('filament-shadcn.auth.social', []) as $provider) {
            $url = SafeUrl::filter($provider['url'] ?? null);

            if ($url === null || blank($provider['label'] ?? null)) {
                continue;
            }

            $providers[] = [
                'label' => __((string) $provider['label']),
                'url' => $url,
                'icon' => filled($provider['icon'] ?? null) ? (string) $provider['icon'] : null,
            ];
        }

        return $providers;
    }

    protected function renderSocialLogin(bool $before): ?View
    {
        $providers = $this->getSocialLoginProviders();
        $isSplit = $this->settings()->get('auth_layout') === 'split';

        // Muted/plain: di atas form. Split: di bawah form, ikon saja.
        if ($providers === [] || $before === $isSplit) {
            return null;
        }

        // Saat langkah MFA, form login biasa disembunyikan; tombol ikut disembunyikan.
        $page = \Livewire\Livewire::current();

        if ($page !== null && filled(data_get($page, 'userUndertakingMultiFactorAuthentication'))) {
            return null;
        }

        return view('filament-shadcn::auth.social', [
            'providers' => $providers,
            'before' => $before,
            'compact' => $isSplit,
        ]);
    }

    protected function renderAuthTerms(): ?View
    {
        $terms = SafeUrl::filter(config('filament-shadcn.auth.terms_url'));
        $privacy = SafeUrl::filter(config('filament-shadcn.auth.privacy_url'));

        if (! $this->isGuestAuthPage() || ($terms === null && $privacy === null)) {
            return null;
        }

        return view('filament-shadcn::hooks.auth-end', ['termsUrl' => $terms, 'privacyUrl' => $privacy]);
    }

    /**
     * URL gambar login. Unggahan halaman Tampilan dibuat URL-nya saat render
     * (bukan dari URL yang tersimpan), supaya tetap benar bila APP_URL/domain berubah.
     */
    protected function authImageUrl(ThemeSettings $settings): ?string
    {
        $path = $settings->get('auth_image_path');

        if (is_string($path) && filled($path)) {
            return Storage::disk('public')->url($path);
        }

        return $settings->get('auth_image');
    }

    protected function set(string $key, mixed $value): static
    {
        if ($value !== null) {
            $this->overrides[$key] = $value;
        }

        $this->resolved = null;

        return $this;
    }
}
