<?php

namespace Mehuli\FilamentShadcn\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\EmptyState;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Pagination\LengthAwarePaginator;
use Mehuli\FilamentShadcn\Pages\Concerns\HasSampleOrders;
use Mehuli\FilamentShadcn\Widgets\ShowcaseChart;
use Mehuli\FilamentShadcn\Widgets\ShowcaseStats;
use UnitEnum;

/**
 * Contoh dasbor dengan susunan artboard docs/mockup/gambar/Shadcn.png:
 * kartu statistik, grafik per kanal, lalu tab + tabel pesanan.
 * Dipakai untuk membandingkan tema dengan mockup di 1440 / 820 / 390px.
 */
class ShowcaseDashboard extends Page implements HasTable
{
    use HasSampleOrders;
    use InteractsWithTable;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static ?string $slug = 'showcase-dashboard';

    public static function getNavigationLabel(): string
    {
        return __('filament-shadcn::filament-shadcn.showcase.dashboard_label');
    }

    public static function getNavigationGroup(): string | UnitEnum | null
    {
        return config('filament-shadcn.showcase.navigation_group');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-shadcn.showcase.navigation_sort');
    }

    public function getTitle(): string | Htmlable
    {
        return __('filament-shadcn::filament-shadcn.showcase.dashboard_label');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ShowcaseStats::class,
            ShowcaseChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Data')
                ->contained(false)
                ->tabs([
                    Tab::make('Pesanan')->schema([EmbeddedTable::make()]),
                    Tab::make('Retur')->badge(3)->schema([
                        EmptyState::make('Belum ada retur yang perlu diproses')
                            ->description('Pengajuan retur dari pelanggan akan muncul di sini.')
                            ->icon(Heroicon::OutlinedArrowUturnLeft),
                    ]),
                    Tab::make('Ulasan')->badge(2)->schema([
                        EmptyState::make('2 ulasan menunggu moderasi')
                            ->description('Ulasan baru tampil setelah disetujui.')
                            ->icon(Heroicon::OutlinedChatBubbleLeftEllipsis),
                    ]),
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        $statuses = $this->orderStatuses();

        return $table
            ->records(fn (?string $search, ?string $sortColumn, ?string $sortDirection, int | string $page, int | string $recordsPerPage): LengthAwarePaginator => $this->orders(
                $search,
                null,
                $sortColumn,
                $sortDirection,
                (int) $page,
                $recordsPerPage === 'all' ? 1000 : (int) $recordsPerPage,
            ))
            ->searchPlaceholder('Saring pelanggan…')
            ->columnManagerTriggerAction(fn (Action $action): Action => $action->button()->label('Kolom')->icon(Heroicon::OutlinedViewColumns)->color('gray'))
            ->columns([
                TextColumn::make('id')->label('No.')->fontFamily('mono'),
                TextColumn::make('customer')->label('Pelanggan')->description(fn (array $record): string => $record['email'])->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $statuses[$state]['label'] ?? $state)
                    ->color(fn (string $state): string => $statuses[$state]['color'] ?? 'gray')
                    ->icon(fn (string $state): ?Heroicon => $statuses[$state]['icon'] ?? null),
                TextColumn::make('channel')->label('Kanal')->color('gray')->toggleable(),
                TextColumn::make('total')->label('Total')->money('IDR', locale: 'id')->alignEnd()->weight('medium')->sortable(),
            ])
            ->headerActions([
                Action::make('add')->label('Tambah')->icon(Heroicon::OutlinedPlus)->color('gray'),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('view')->label('Lihat')->icon(Heroicon::OutlinedEye),
                    Action::make('edit')->label('Edit')->icon(Heroicon::OutlinedPencilSquare),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markDone')->label('Tandai selesai')->icon(Heroicon::OutlinedCheck)->action(fn () => null),
                ]),
            ])
            ->stackedOnMobile()
            ->paginated([7, 10, 25])
            ->defaultPaginationPageOption(7);
    }
}
