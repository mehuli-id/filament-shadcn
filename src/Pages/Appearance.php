<?php

namespace Mehuli\FilamentShadcn\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Storage;
use Mehuli\FilamentShadcn\ShadcnPlugin;
use Mehuli\FilamentShadcn\Support\AppearanceRepository;
use Mehuli\FilamentShadcn\Support\Presets;
use Mehuli\FilamentShadcn\Support\TokenBuilder;
use Mehuli\FilamentShadcn\Support\ThemeSettings;
use UnitEnum;

/**
 * Halaman "Tampilan": admin bisa mengubah gaya, warna, dan tata letak panel
 * tanpa deploy. Disimpan ke file JSON lewat AppearanceRepository.
 *
 * @property-read Schema $form
 */
class Appearance extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static ?string $slug = 'appearance';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('filament-shadcn::filament-shadcn.appearance.navigation_label');
    }

    public static function getNavigationGroup(): string | UnitEnum | null
    {
        return config('filament-shadcn.appearance_page.navigation_group');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-shadcn.appearance_page.navigation_sort');
    }

    public static function canAccess(): bool
    {
        return ShadcnPlugin::get()->canAccessAppearance();
    }

    public function getTitle(): string | Htmlable
    {
        return __('filament-shadcn::filament-shadcn.appearance.title');
    }

    public function getSubheading(): string | Htmlable | null
    {
        return __('filament-shadcn::filament-shadcn.appearance.subheading');
    }

    public function mount(): void
    {
        $this->form->fill($this->toFormState(ShadcnPlugin::get()->settings()));
    }

    public function form(Schema $schema): Schema
    {
        $t = fn (string $key): string => __("filament-shadcn::filament-shadcn.appearance.{$key}");

        $accentOptions = collect(Presets::accentColors())
            ->keys()
            ->mapWithKeys(fn (string $name): array => [$name => ucfirst($name)])
            ->all();

        return $schema
            ->statePath('data')
            ->components([
                Section::make($t('sections.style'))
                    ->description($t('sections.style_description'))
                    ->aside()
                    ->schema([
                        ToggleButtons::make('style')
                            ->label($t('fields.style'))
                            ->options(collect(Presets::styles())->keys()->mapWithKeys(fn (string $name): array => [$name => $t("styles.{$name}")])->all())
                            ->inline()
                            ->required(),
                        Grid::make(2)->schema([
                            TextInput::make('radius')
                                ->label($t('fields.radius'))
                                ->helperText($t('fields.radius_helper'))
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(1.5)
                                ->step(0.125)
                                ->suffix('rem'),
                            TextInput::make('density')
                                ->label($t('fields.density'))
                                ->helperText($t('fields.density_helper'))
                                ->numeric()
                                ->minValue(0.75)
                                ->maxValue(1.25)
                                ->step(0.01),
                        ]),
                    ]),

                Section::make($t('sections.colors'))
                    ->description($t('sections.colors_description'))
                    ->aside()
                    ->schema([
                        Select::make('base_color')
                            ->label($t('fields.base_color'))
                            ->options(collect(Presets::baseColors())->keys()->mapWithKeys(fn (string $name): array => [$name => ucfirst($name)])->all())
                            ->required()
                            ->native(false),
                        ToggleButtons::make('primary_mode')
                            ->label($t('fields.primary_mode'))
                            ->options([
                                'default' => $t('fields.primary_mode_default'),
                                'named' => $t('fields.primary_mode_named'),
                                'custom' => $t('fields.primary_mode_custom'),
                            ])
                            ->inline()
                            ->live(),
                        Select::make('primary_named')
                            ->label($t('fields.primary_named'))
                            ->options($accentOptions)
                            ->native(false)
                            ->visible(fn (Get $get): bool => $get('primary_mode') === 'named')
                            ->required(fn (Get $get): bool => $get('primary_mode') === 'named'),
                        ColorPicker::make('primary_custom')
                            ->label($t('fields.primary_custom'))
                            ->regex(ThemeSettings::COLOR_VALUE_PATTERN)
                            ->visible(fn (Get $get): bool => $get('primary_mode') === 'custom')
                            ->required(fn (Get $get): bool => $get('primary_mode') === 'custom'),
                        Grid::make(['default' => 2, 'lg' => 4])->schema([
                            Select::make('colors.danger')->label($t('fields.danger'))->options($accentOptions)->native(false),
                            Select::make('colors.success')->label($t('fields.success'))->options($accentOptions)->native(false),
                            Select::make('colors.warning')->label($t('fields.warning'))->options($accentOptions)->native(false),
                            Select::make('colors.info')->label($t('fields.info'))->options($accentOptions)->native(false),
                        ]),
                    ]),

                Section::make($t('sections.layout'))
                    ->description($t('sections.layout_description'))
                    ->aside()
                    ->schema([
                        Radio::make('sidebar')
                            ->label($t('fields.sidebar'))
                            ->options([
                                'inset' => $t('fields.sidebar_inset'),
                                'sidebar' => $t('fields.sidebar_sidebar'),
                                'floating' => $t('fields.sidebar_floating'),
                            ]),
                        Radio::make('auth_layout')
                            ->label($t('fields.auth_layout'))
                            ->options([
                                'muted' => $t('fields.auth_muted'),
                                'split' => $t('fields.auth_split'),
                                'plain' => $t('fields.auth_plain'),
                            ])
                            ->live(),
                        FileUpload::make('auth_image_path')
                            ->label($t('fields.auth_image'))
                            ->image()
                            // Tanpa SVG: SVG bisa berisi skrip dan disajikan dari domain aplikasi.
                            ->acceptedFileTypes(config('filament-shadcn.appearance_page.image_types', ['image/png', 'image/jpeg', 'image/webp', 'image/avif']))
                            ->maxSize(config('filament-shadcn.appearance_page.image_max_kb', 4096))
                            ->disk('public')
                            ->directory(static::imageDirectory())
                            ->visibility('public')
                            ->imageEditor()
                            ->visible(fn (Get $get): bool => $get('auth_layout') === 'split'),
                    ]),

                Section::make($t('sections.typography'))
                    ->description($t('sections.typography_description'))
                    ->aside()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('font_sans')
                                ->label($t('fields.font_sans'))
                                ->helperText($t('fields.font_helper'))
                                ->regex(ThemeSettings::FONT_NAME_PATTERN)
                                ->maxLength(64)
                                ->datalist(['Geist', 'Inter', 'Plus Jakarta Sans', 'Manrope', 'DM Sans', 'Figtree', 'Outfit', 'Public Sans']),
                            TextInput::make('font_mono')
                                ->label($t('fields.font_mono'))
                                ->regex(ThemeSettings::FONT_NAME_PATTERN)
                                ->maxLength(64)
                                ->datalist(['Geist Mono', 'JetBrains Mono', 'IBM Plex Mono', 'Fira Code', 'Space Mono']),
                        ]),
                    ]),

                // Tanpa ->collapsible(): Filament 5 mengabaikannya pada section ->aside().
                Section::make($t('sections.advanced'))
                    ->description($t('sections.advanced_description'))
                    ->aside()
                    ->schema([
                        KeyValue::make('tokens.light')
                            ->rules([
                                fn (): \Closure => function (string $attribute, mixed $value, \Closure $fail): void {
                                    foreach ((array) $value as $key => $token) {
                                        if (filled($token) && TokenBuilder::safeTokenValue((string) $token) === null) {
                                            $fail(__('filament-shadcn::filament-shadcn.appearance.validation.token', ['key' => $key]));
                                        }
                                    }
                                },
                            ])
                            ->label($t('fields.tokens_light'))
                            ->keyLabel($t('fields.token_key'))
                            ->valueLabel($t('fields.token_value'))
                            ->keyPlaceholder('primary')
                            ->valuePlaceholder('oklch(0.55 0.22 263)'),
                        KeyValue::make('tokens.dark')
                            ->rules([
                                fn (): \Closure => function (string $attribute, mixed $value, \Closure $fail): void {
                                    foreach ((array) $value as $key => $token) {
                                        if (filled($token) && TokenBuilder::safeTokenValue((string) $token) === null) {
                                            $fail(__('filament-shadcn::filament-shadcn.appearance.validation.token', ['key' => $key]));
                                        }
                                    }
                                },
                            ])
                            ->label($t('fields.tokens_dark'))
                            ->keyLabel($t('fields.token_key'))
                            ->valueLabel($t('fields.token_value'))
                            ->keyPlaceholder('primary')
                            ->valuePlaceholder('oklch(0.7 0.15 263)'),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')
                            ->label(__('filament-shadcn::filament-shadcn.appearance.actions.save'))
                            ->submit('save')
                            ->keyBindings(['mod+s']),
                        Action::make('reset')
                            ->label(__('filament-shadcn::filament-shadcn.appearance.actions.reset'))
                            ->color('gray')
                            ->requiresConfirmation()
                            ->modalDescription(__('filament-shadcn::filament-shadcn.appearance.actions.reset_confirm'))
                            ->action(fn () => $this->resetAppearance()),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $primary = match ($state['primary_mode'] ?? 'default') {
            'named' => $state['primary_named'] ?? 'default',
            'custom' => $state['primary_custom'] ?? 'default',
            default => 'default',
        };

        $imagePath = $state['auth_image_path'] ?? null;

        if (is_array($imagePath)) {
            $imagePath = array_values($imagePath)[0] ?? null;
        }

        $repository = app(AppearanceRepository::class);

        // Hapus gambar login lama kalau diganti/dihapus (hanya file di folder milik package).
        $oldImagePath = $repository->all()['auth_image_path'] ?? null;

        if ($oldImagePath !== $imagePath) {
            static::deleteOwnImage($oldImagePath);
        }

        $repository->save([
            'style' => $state['style'] ?? null,
            'base_color' => $state['base_color'] ?? null,
            'primary' => $primary,
            'colors' => array_filter((array) ($state['colors'] ?? [])),
            'radius' => filled($state['radius'] ?? null) ? (float) $state['radius'] : null,
            'density' => filled($state['density'] ?? null) ? (float) $state['density'] : null,
            'sidebar' => $state['sidebar'] ?? null,
            'auth_layout' => $state['auth_layout'] ?? null,
            'auth_image' => filled($imagePath) ? Storage::disk('public')->url($imagePath) : null,
            'auth_image_path' => $imagePath,
            'font_sans' => $state['font_sans'] ?? null,
            'font_mono' => $state['font_mono'] ?? null,
            'tokens' => array_filter([
                'light' => array_filter((array) ($state['tokens']['light'] ?? [])),
                'dark' => array_filter((array) ($state['tokens']['dark'] ?? [])),
            ]),
        ]);

        ShadcnPlugin::get()->flushSettings();

        Notification::make()
            ->success()
            ->title(__('filament-shadcn::filament-shadcn.appearance.notifications.saved'))
            ->body(__('filament-shadcn::filament-shadcn.appearance.notifications.saved_body'))
            ->send();

        // Muat ulang supaya token baru di <head> ikut terpasang.
        $this->redirect(static::getUrl());
    }

    /**
     * Hapus gambar login lama, hanya file di folder milik package.
     */
    protected static function deleteOwnImage(mixed $path): void
    {
        if (is_string($path) && str_starts_with($path, static::imageDirectory() . '/') && ! str_contains($path, '..')) {
            Storage::disk('public')->delete($path);
        }
    }

    protected static function imageDirectory(): string
    {
        return trim((string) config('filament-shadcn.appearance_page.image_directory', 'filament-shadcn'), '/');
    }

    protected function resetAppearance(): void
    {
        $repository = app(AppearanceRepository::class);
        static::deleteOwnImage($repository->all()['auth_image_path'] ?? null);
        $repository->reset();

        ShadcnPlugin::get()->flushSettings();

        Notification::make()
            ->success()
            ->title(__('filament-shadcn::filament-shadcn.appearance.notifications.reset'))
            ->send();

        $this->redirect(static::getUrl());
    }

    /**
     * @return array<string, mixed>
     */
    protected function toFormState(ThemeSettings $settings): array
    {
        $primary = $settings->primaryValue();

        $mode = match (true) {
            $settings->isDefaultPrimary() => 'default',
            Presets::isColorValue($primary) => 'custom',
            default => 'named',
        };

        $stored = app(AppearanceRepository::class)->all();

        return [
            'style' => $settings->styleName(),
            'radius' => $stored['radius'] ?? null,
            'density' => $stored['density'] ?? null,
            'base_color' => $settings->get('base_color'),
            'primary_mode' => $mode,
            'primary_named' => $mode === 'named' ? $primary : null,
            'primary_custom' => $mode === 'custom' ? $primary : null,
            'colors' => $settings->get('colors'),
            'sidebar' => $settings->get('sidebar'),
            'auth_layout' => $settings->get('auth_layout'),
            'auth_image_path' => $stored['auth_image_path'] ?? null,
            'font_sans' => $stored['font_sans'] ?? null,
            'font_mono' => $stored['font_mono'] ?? null,
            'tokens' => [
                'light' => $stored['tokens']['light'] ?? [],
                'dark' => $stored['tokens']['dark'] ?? [],
            ],
        ];
    }
}
