<?php

namespace Mehuli\FilamentShadcn;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Mehuli\FilamentShadcn\Pages\Appearance;
use Mehuli\FilamentShadcn\Pages\ComponentShowcase;
use Mehuli\FilamentShadcn\Pages\ShadcnComponents;
use Mehuli\FilamentShadcn\Support\AppearanceRepository;
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
    public function font(?string $sans = null, ?string $mono = null, ?string $provider = null): static
    {
        $this->set('font_sans', $sans);
        $this->set('font_mono', $mono);

        return $this->set('font_provider', $provider);
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
        }

        if ($pages !== []) {
            $panel->pages($pages);
        }

        $panel->renderHook(
            PanelsRenderHook::HEAD_END,
            fn (): View => view('filament-shadcn::hooks.head', [
                'css' => TokenBuilder::make($this->settings())->css(),
                'dataAttributes' => TokenBuilder::make($this->settings())->dataAttributes(),
            ]),
        );

        $panel->renderHook(
            PanelsRenderHook::SIMPLE_LAYOUT_START,
            fn (): ?View => $this->renderAuthAside(),
        );
    }

    public function boot(Panel $panel): void {}

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

    public function hasShowcase(): bool
    {
        $condition = $this->hasShowcase ?? config('filament-shadcn.showcase.enabled', false);

        return (bool) ($condition instanceof Closure ? app()->call($condition) : $condition);
    }

    protected function renderAuthAside(): ?View
    {
        $settings = $this->settings();

        if ($settings->get('auth_layout') !== 'split' || filament()->auth()->check()) {
            return null;
        }

        return view('filament-shadcn::hooks.auth-aside', [
            'image' => $this->authImageUrl($settings),
            'tagline' => $settings->get('auth_tagline'),
            'taglineAuthor' => $settings->get('auth_tagline_author'),
            'brand' => filament()->getBrandName(),
        ]);
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
