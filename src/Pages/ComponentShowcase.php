<?php

namespace Mehuli\FilamentShadcn\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\EmptyState;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Mehuli\FilamentShadcn\Infolists\Components\CodeBlockEntry;
use Mehuli\FilamentShadcn\Infolists\Components\ProgressEntry;
use Mehuli\FilamentShadcn\Schemas\Components\Accordion;
use Mehuli\FilamentShadcn\Schemas\Components\AccordionItem;
use Mehuli\FilamentShadcn\Tables\Columns\ProgressColumn;
use Mehuli\FilamentShadcn\Widgets\ShowcaseChart;
use Mehuli\FilamentShadcn\Widgets\ShowcaseStats;
use UnitEnum;

/**
 * Satu halaman berisi semua komponen Filament untuk mengecek tema:
 * tombol, badge, callout, semua field form, wizard, tabel, infolist,
 * widget, modal, slide-over, dropdown, notifikasi, empty state.
 *
 * Aktifkan lewat SHADCN_SHOWCASE=true atau ShadcnPlugin::make()->showcase().
 *
 * @property-read Schema $form
 */
class ComponentShowcase extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?string $slug = 'component-showcase';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('filament-shadcn::filament-shadcn.showcase.navigation_label');
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
        return __('filament-shadcn::filament-shadcn.showcase.title');
    }

    public function getSubheading(): string | Htmlable | null
    {
        return __('filament-shadcn::filament-shadcn.showcase.subheading');
    }

    public function mount(): void
    {
        $this->form->fill([
            'name' => 'Tumbler Stainless 500 ml',
            'slug' => 'tumbler-stainless-500ml',
            'sku' => 'TMB-500-01',
            'price' => 189000,
            'weight' => 420,
            'category' => 'drinkware',
            'collections' => ['new', 'office'],
            'summary' => 'Tumbler stainless 500 ml, tahan dingin 24 jam.',
            'tax' => 'ppn',
            'unit' => 'pcs',
            'couriers' => ['jne', 'jnt', 'sicepat'],
            'is_visible' => true,
            'is_featured' => false,
            'accept_terms' => true,
            'discount' => 10,
            'published_at' => now()->addDays(5)->setTime(9, 0)->toDateTimeString(),
            'label_color' => '#2563eb',
            'tags' => ['tumbler', 'botol minum', 'hadiah kantor'],
            'specs' => ['Bahan' => 'Stainless steel 304', 'Kapasitas' => '500 ml'],
            'variants' => [
                ['size' => '500 ml', 'color' => '#1e3a5f', 'stock' => 48],
                ['size' => '750 ml', 'color' => '#0f766e', 'stock' => 15],
            ],
            'content' => [
                ['type' => 'heading', 'data' => ['text' => 'Kenapa memilih tumbler ini?']],
                ['type' => 'paragraph', 'data' => ['text' => 'Lapisan vakum ganda menjaga suhu minuman seharian.']],
            ],
            'embed' => "<script>\n  dataLayer.push({ event: 'view_item' });\n</script>",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Aksi di header: modal, slide-over, dropdown, notifikasi
    |--------------------------------------------------------------------------
    */

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('notifySuccess')
                    ->label('Notifikasi sukses')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->action(fn () => Notification::make()->success()->title('Tersimpan')->body('Perubahan berhasil disimpan.')->send()),
                Action::make('notifyWarning')
                    ->label('Notifikasi peringatan')
                    ->icon(Heroicon::OutlinedExclamationTriangle)
                    ->action(fn () => Notification::make()->warning()->title('Stok menipis')->body('Kaos Polos Hitam tersisa 4 unit.')->send()),
                Action::make('notifyDanger')
                    ->label('Notifikasi gagal')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->action(fn () => Notification::make()->danger()->title('Gagal menyimpan')->body('Periksa kembali isian Anda.')->send()),
            ])
                ->label('Notifikasi')
                ->icon(Heroicon::OutlinedBell)
                ->button()
                ->color('gray'),

            Action::make('slideOver')
                ->label('Slide-over')
                ->color('gray')
                ->slideOver()
                ->modalHeading('Edit cepat')
                ->modalDescription('Contoh panel samping (Sheet).')
                ->schema([
                    TextInput::make('name')->label('Nama')->default('Tumbler Stainless 500 ml'),
                    Select::make('status')->label('Status')->options(['draft' => 'Draf', 'published' => 'Terbit'])->default('draft'),
                    Toggle::make('visible')->label('Tampilkan di toko')->default(true),
                ])
                ->action(fn () => Notification::make()->success()->title('Disimpan')->send()),

            Action::make('dialog')
                ->label('Hapus produk')
                ->color('danger')
                ->icon(Heroicon::OutlinedTrash)
                ->requiresConfirmation()
                ->modalDescription('Tindakan ini tidak bisa dibatalkan. Produk akan dihapus permanen.')
                ->action(fn () => Notification::make()->success()->title('Produk dihapus')->send()),

            Action::make('create')
                ->label('Produk baru')
                ->icon(Heroicon::OutlinedPlus)
                ->modalHeading('Buat produk')
                ->modalWidth('lg')
                ->schema([
                    TextInput::make('name')->label('Nama')->required(),
                    TextInput::make('price')->label('Harga')->prefix('Rp')->numeric(),
                ])
                ->action(fn () => Notification::make()->success()->title('Produk dibuat')->send()),
        ];
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

    /*
    |--------------------------------------------------------------------------
    | Isi halaman
    |--------------------------------------------------------------------------
    */

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Tombol')
                ->description('Varian shadcn: default, outline, destructive, status, ghost (ikon), link, ukuran.')
                ->schema([
                    Actions::make([
                        Action::make('btnPrimary')->label('Default'),
                        Action::make('btnGray')->label('Outline')->color('gray'),
                        Action::make('btnDanger')->label('Destructive')->color('danger'),
                        Action::make('btnSuccess')->label('Sukses')->color('success'),
                        Action::make('btnWarning')->label('Peringatan')->color('warning'),
                        Action::make('btnInfo')->label('Info')->color('info'),
                        Action::make('btnOutlined')->label('Outlined')->outlined(),
                        Action::make('btnIcon')->label('Dengan ikon')->icon(Heroicon::OutlinedArrowDownTray),
                        Action::make('btnLink')->label('Link')->link(),
                        Action::make('btnDisabled')->label('Nonaktif')->disabled(),
                    ]),
                    Actions::make([
                        Action::make('sizeXs')->label('XS')->size(Size::ExtraSmall),
                        Action::make('sizeSm')->label('Small')->size(Size::Small),
                        Action::make('sizeMd')->label('Medium'),
                        Action::make('sizeLg')->label('Large')->size(Size::Large),
                        Action::make('iconEdit')->iconButton()->icon(Heroicon::OutlinedPencilSquare)->label('Edit'),
                        Action::make('iconCopy')->iconButton()->icon(Heroicon::OutlinedDocumentDuplicate)->label('Duplikat'),
                        Action::make('iconDelete')->iconButton()->icon(Heroicon::OutlinedTrash)->color('danger')->label('Hapus'),
                        Action::make('badgeBtn')->label('Kotak masuk')->badge(3),
                        ActionGroup::make([
                            Action::make('menuView')->label('Lihat')->icon(Heroicon::OutlinedEye),
                            Action::make('menuEdit')->label('Edit')->icon(Heroicon::OutlinedPencilSquare),
                            Action::make('menuDelete')->label('Hapus')->icon(Heroicon::OutlinedTrash)->color('danger'),
                        ]),
                    ]),
                ]),

            Section::make('Badge & callout')
                ->schema([
                    Flex::make([
                        Text::make('Secondary')->badge(),
                        Text::make('Default')->badge()->color('primary'),
                        Text::make('Sukses')->badge()->color('success'),
                        Text::make('Peringatan')->badge()->color('warning'),
                        Text::make('Bahaya')->badge()->color('danger'),
                        Text::make('Info')->badge()->color('info'),
                    ])->from('sm'),
                    Grid::make(['default' => 1, 'lg' => 2])->schema([
                        Callout::make('Sinkronisasi berjalan')
                            ->description('Data pesanan diperbarui setiap 5 menit.')
                            ->icon(Heroicon::OutlinedInformationCircle)
                            ->color('info'),
                        Callout::make('Pembayaran gagal')
                            ->description('Kartu ditolak. Minta pelanggan memakai metode lain.')
                            ->icon(Heroicon::OutlinedExclamationCircle)
                            ->color('danger'),
                    ]),
                ]),

            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('submit')
                ->footer([
                    Actions::make([
                        Action::make('submit')->label('Simpan')->submit('submit'),
                        Action::make('cancel')->label('Batal')->color('gray'),
                    ]),
                ]),

            EmbeddedTable::make(),

            Section::make('Infolist')
                ->description('TextEntry, IconEntry, ColorEntry, KeyValueEntry.')
                ->schema([
                    Grid::make(['default' => 1, 'md' => 3])->schema([
                        TextEntry::make('showcase_name')->label('Nama')->state('Tumbler Stainless 500 ml'),
                        TextEntry::make('showcase_price')->label('Harga')->state(189000)->money('IDR', locale: 'id'),
                        TextEntry::make('showcase_status')->label('Status')->state('Terjadwal')->badge()->color('info'),
                        TextEntry::make('showcase_sku')->label('SKU')->state('TMB-500-01')->fontFamily('mono')->copyable(),
                        IconEntry::make('showcase_visible')->label('Tampil')->state(true)->boolean(),
                        ColorEntry::make('showcase_color')->label('Warna label')->state('#2563eb'),
                    ]),
                    KeyValueEntry::make('showcase_specs')
                        ->label('Spesifikasi')
                        ->state(['Bahan' => 'Stainless steel 304', 'Kapasitas' => '500 ml', 'Garansi' => '6 bulan']),
                    Grid::make(['default' => 1, 'md' => 2])->schema([
                        ProgressEntry::make('showcase_stock')->label('Stok gudang (ProgressEntry)')->state(36)->maxValue(120)->color(fn (float $state): string => $state < 40 ? 'warning' : 'success'),
                        ProgressEntry::make('showcase_completion')->label('Kelengkapan data')->state(80),
                    ]),
                    CodeBlockEntry::make('showcase_payload')
                        ->label('Payload webhook (CodeBlockEntry)')
                        ->state(['event' => 'order.paid', 'invoice' => 'INV-2041', 'amount' => 1250000, 'channel' => 'QRIS'])
                        ->title('webhook.json')
                        ->language('json')
                        ->lineNumbers(),
                ]),

            Section::make('Accordion (komponen schema)')
                ->description('Mehuli\\FilamentShadcn\\Schemas\\Components\\Accordion — isi tiap item bisa berupa field form atau entry.')
                ->schema([
                    Accordion::make([
                        AccordionItem::make('Bagaimana cara mengubah warna panel?')->schema([
                            Text::make('Buka menu Tampilan, pilih warna utama, lalu simpan. Semua pengguna langsung melihat perubahannya.'),
                        ]),
                        AccordionItem::make('Apakah bisa memakai tema dari tweakcn?')->schema([
                            Text::make('Bisa. Salin variabel CSS-nya ke bagian Lanjutan di halaman Tampilan atau ke config tokens.'),
                        ]),
                        AccordionItem::make('Siapa yang boleh mengubah tampilan?')->schema([
                            TextEntry::make('accordion_access')->hiddenLabel()->state('Atur lewat ShadcnPlugin::make()->canCustomizeAppearance(...).')->fontFamily('mono'),
                        ]),
                    ])->defaultOpen('bagaimana-cara-mengubah-warna-panel')->contained(false),
                ]),

            EmptyState::make('Belum ada ulasan')
                ->description('Ulasan pelanggan akan muncul di sini setelah pesanan pertama selesai.')
                ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                ->footer([
                    Action::make('requestReview')->label('Minta ulasan')->color('gray'),
                ]),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Form: semua field
    |--------------------------------------------------------------------------
    */

    public function form(Schema $schema): Schema
    {
        $colorOptions = ['red' => 'Merah', 'blue' => 'Biru', 'green' => 'Hijau'];

        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('Form')
                    ->contained()
                    ->tabs([
                        Tab::make('Input dasar')
                            ->icon(Heroicon::OutlinedPencil)
                            ->schema([
                                Grid::make(['default' => 1, 'md' => 2])->schema([
                                    TextInput::make('name')->label('Nama produk')->required()->maxLength(120),
                                    TextInput::make('slug')->label('Slug')->prefix('toko.id/p/')->disabled(),
                                    TextInput::make('sku')->label('SKU')->hint('Harus unik')->helperText('Contoh status error: kosongkan lalu simpan.')->required(),
                                    Select::make('category')->label('Kategori')->options([
                                        'drinkware' => 'Peralatan minum',
                                        'apparel' => 'Pakaian',
                                        'bags' => 'Tas & aksesori',
                                    ])->required(),
                                    TextInput::make('price')->label('Harga')->prefix('Rp')->numeric(),
                                    TextInput::make('weight')->label('Berat')->suffix('gram')->numeric(),
                                    TextInput::make('password')->label('Kata sandi')->password()->revealable(),
                                    TextInput::make('email')->label('Email')->email()->prefixIcon(Heroicon::OutlinedEnvelope)->placeholder('nama@contoh.id'),
                                ]),
                                Textarea::make('summary')->label('Ringkasan')->rows(3)->maxLength(160),
                                RichEditor::make('description')->label('Deskripsi (RichEditor)'),
                                MarkdownEditor::make('notes')->label('Catatan (MarkdownEditor)'),
                            ]),

                        Tab::make('Pilihan')
                            ->icon(Heroicon::OutlinedAdjustmentsHorizontal)
                            ->schema([
                                Grid::make(['default' => 1, 'md' => 2])->schema([
                                    Select::make('collections')->label('Koleksi (multiple, searchable)')->multiple()->searchable()->options([
                                        'new' => 'Produk baru',
                                        'office' => 'Perlengkapan kantor',
                                        'gift' => 'Ide hadiah',
                                        'promo' => 'Promo akhir tahun',
                                    ]),
                                    Select::make('color')->label('Warna (native)')->options($colorOptions)->native(),
                                    Radio::make('tax')->label('Pajak')->options([
                                        'ppn' => 'PPN 11%',
                                        'free' => 'Bebas pajak',
                                        'custom' => 'Tarif khusus',
                                    ])->descriptions([
                                        'ppn' => 'Termasuk dalam harga',
                                        'free' => 'Produk dikecualikan',
                                        'custom' => 'Atur per wilayah',
                                    ]),
                                    CheckboxList::make('couriers')->label('Kurir')->options([
                                        'jne' => 'JNE',
                                        'jnt' => 'J&T',
                                        'sicepat' => 'SiCepat',
                                        'pos' => 'Pos Indonesia',
                                    ])->columns(2)->bulkToggleable(),
                                ]),
                                ToggleButtons::make('unit')->label('Satuan (ToggleButtons)')->options([
                                    'pcs' => 'Satuan',
                                    'dozen' => 'Lusin',
                                    'bulk' => 'Grosir',
                                ])->inline(),
                                Fieldset::make('Status')->schema([
                                    Toggle::make('is_visible')->label('Tampilkan di toko'),
                                    Toggle::make('is_featured')->label('Produk unggulan'),
                                    Checkbox::make('accept_terms')->label('Saya menyetujui ketentuan'),
                                ])->columns(3),
                                Slider::make('discount')->label('Diskon anggota (%)')->range(minValue: 0, maxValue: 50)->step(5)->tooltips(),
                            ]),

                        Tab::make('Tanggal & media')
                            ->icon(Heroicon::OutlinedCalendarDays)
                            ->schema([
                                Grid::make(['default' => 1, 'md' => 3])->schema([
                                    DatePicker::make('date')->label('Tanggal (native)'),
                                    DateTimePicker::make('published_at')->label('Jadwal terbit')->native(false)->seconds(false),
                                    TimePicker::make('time')->label('Jam')->seconds(false),
                                ]),
                                Grid::make(['default' => 1, 'md' => 2])->schema([
                                    ColorPicker::make('label_color')->label('Warna label'),
                                    TagsInput::make('tags')->label('Kata kunci'),
                                ]),
                                FileUpload::make('images')->label('Gambar produk')->image()->multiple()->reorderable()->imageEditor()->disk('public')->directory('showcase'),
                            ]),

                        Tab::make('Struktur')
                            ->icon(Heroicon::OutlinedRectangleStack)
                            ->schema([
                                Repeater::make('variants')
                                    ->label('Varian (Repeater)')
                                    ->schema([
                                        TextInput::make('size')->label('Ukuran')->required(),
                                        ColorPicker::make('color')->label('Warna'),
                                        TextInput::make('stock')->label('Stok')->numeric(),
                                    ])
                                    ->columns(3)
                                    ->collapsible()
                                    ->cloneable()
                                    ->itemLabel(fn (array $state): ?string => $state['size'] ?? null),
                                Builder::make('content')
                                    ->label('Konten halaman (Builder)')
                                    ->blocks([
                                        Block::make('heading')->label('Judul')->icon(Heroicon::OutlinedH1)->schema([
                                            TextInput::make('text')->label('Teks')->required(),
                                        ]),
                                        Block::make('paragraph')->label('Paragraf')->icon(Heroicon::OutlinedBars3BottomLeft)->schema([
                                            Textarea::make('text')->label('Teks'),
                                        ]),
                                        Block::make('image')->label('Gambar')->icon(Heroicon::OutlinedPhoto)->schema([
                                            FileUpload::make('url')->label('Gambar')->image()->disk('public')->directory('showcase'),
                                        ]),
                                    ])
                                    ->collapsible(),
                                KeyValue::make('specs')->label('Spesifikasi (KeyValue)'),
                                CodeEditor::make('embed')->label('Embed (CodeEditor)')->language(Language::Html),
                            ]),
                    ]),

                Wizard::make([
                    Step::make('Informasi')
                        ->description('Data dasar')
                        ->icon(Heroicon::OutlinedInformationCircle)
                        ->schema([
                            TextInput::make('wizard_name')->label('Nama toko'),
                        ]),
                    Step::make('Pembayaran')
                        ->description('Rekening & QRIS')
                        ->icon(Heroicon::OutlinedCreditCard)
                        ->schema([
                            Select::make('wizard_bank')->label('Bank')->options(['bca' => 'BCA', 'bri' => 'BRI', 'mandiri' => 'Mandiri']),
                        ]),
                    Step::make('Selesai')
                        ->description('Tinjau & terbitkan')
                        ->icon(Heroicon::OutlinedCheckBadge)
                        ->schema([
                            Checkbox::make('wizard_confirm')->label('Data sudah benar'),
                        ]),
                ])->contained(),
            ]);
    }

    public function submit(): void
    {
        $this->form->getState();

        Notification::make()
            ->success()
            ->title('Form valid')
            ->body('Semua isian lolos validasi.')
            ->send();
    }

    /*
    |--------------------------------------------------------------------------
    | Tabel dengan data statis
    |--------------------------------------------------------------------------
    */

    public function table(Table $table): Table
    {
        $statuses = [
            'new' => ['label' => 'Baru', 'color' => 'info', 'icon' => Heroicon::OutlinedSparkles],
            'processing' => ['label' => 'Diproses', 'color' => 'warning', 'icon' => Heroicon::OutlinedArrowPath],
            'shipped' => ['label' => 'Dikirim', 'color' => 'info', 'icon' => Heroicon::OutlinedTruck],
            'done' => ['label' => 'Selesai', 'color' => 'success', 'icon' => Heroicon::OutlinedCheckCircle],
            'cancelled' => ['label' => 'Dibatalkan', 'color' => 'danger', 'icon' => Heroicon::OutlinedXCircle],
        ];

        return $table
            ->heading('Pesanan terbaru')
            ->description('Tabel dengan pencarian, filter, urutan, aksi baris & aksi massal.')
            ->records(fn (?string $search, ?array $filters, ?string $sortColumn, ?string $sortDirection, int | string $page, int | string $recordsPerPage): LengthAwarePaginator => $this->orders(
                $search,
                $filters['status']['value'] ?? null,
                $sortColumn,
                $sortDirection,
                (int) $page,
                $recordsPerPage === 'all' ? 1000 : (int) $recordsPerPage,
            ))
            ->columns([
                TextColumn::make('id')->label('No.')->fontFamily('mono')->sortable(),
                TextColumn::make('customer')->label('Pelanggan')->description(fn (array $record): string => $record['email'])->searchable()->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $statuses[$state]['label'] ?? $state)
                    ->color(fn (string $state): string => $statuses[$state]['color'] ?? 'gray')
                    ->icon(fn (string $state): ?Heroicon => $statuses[$state]['icon'] ?? null),
                TextColumn::make('channel')->label('Kanal'),
                IconColumn::make('paid')->label('Lunas')->boolean(),
                ProgressColumn::make('fulfilled')->label('Terpenuhi')->color(fn (float $state): string => $state >= 100 ? 'success' : 'primary'),
                TextColumn::make('total')->label('Total')->money('IDR', locale: 'id')->alignEnd()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Status')->options(collect($statuses)->map(fn (array $status): string => $status['label'])->all()),
            ])
            ->headerActions([
                Action::make('export')->label('Ekspor')->icon(Heroicon::OutlinedArrowDownTray)->color('gray'),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('view')->label('Lihat')->icon(Heroicon::OutlinedEye),
                    Action::make('edit')->label('Edit')->icon(Heroicon::OutlinedPencilSquare),
                    Action::make('delete')->label('Hapus')->icon(Heroicon::OutlinedTrash)->color('danger')->requiresConfirmation(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markDone')->label('Tandai selesai')->icon(Heroicon::OutlinedCheck)->action(fn () => null),
                    BulkAction::make('deleteSelected')->label('Hapus terpilih')->icon(Heroicon::OutlinedTrash)->color('danger')->requiresConfirmation()->action(fn () => null),
                ]),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }

    protected function orders(?string $search, ?string $status, ?string $sortColumn, ?string $sortDirection, int $page, int $perPage): LengthAwarePaginator
    {
        $rows = $this->sampleOrders()
            ->when(filled($search), fn (Collection $rows): Collection => $rows->filter(
                fn (array $row): bool => str_contains(strtolower($row['customer'] . ' ' . $row['email'] . ' ' . $row['id']), strtolower((string) $search)),
            ))
            ->when(filled($status), fn (Collection $rows): Collection => $rows->where('status', $status))
            ->when(filled($sortColumn), fn (Collection $rows): Collection => $rows->sortBy($sortColumn, SORT_NATURAL, $sortDirection === 'desc'));

        return new LengthAwarePaginator(
            $rows->forPage($page, $perPage),
            $rows->count(),
            $perPage,
            $page,
        );
    }

    protected function sampleOrders(): Collection
    {
        $names = ['Rina Sembiring', 'Budi Santoso', 'Sari Ginting', 'Yohanes Tarigan', 'Maria Wijaya', 'Dedi Kurniawan', 'Lia Sitepu', 'Andreas Pratama', 'Nina Purba', 'Tono Saragih', 'Eva Barus', 'Rudi Hartono'];
        $statuses = ['new', 'processing', 'shipped', 'done', 'done', 'cancelled'];
        $channels = ['Website', 'Marketplace', 'WhatsApp', 'Offline'];

        return collect($names)->mapWithKeys(function (string $name, int $index) use ($statuses, $channels): array {
            $id = 'INV-' . (2041 - $index);

            return [$id => [
                'id' => $id,
                'customer' => $name,
                'email' => strtolower(explode(' ', $name)[0]) . '@contoh.id',
                'status' => $statuses[$index % count($statuses)],
                'channel' => $channels[$index % count($channels)],
                'paid' => $index % 3 !== 0,
                'total' => 125000 + (($index * 173000) % 2200000),
                'fulfilled' => [100, 60, 85, 100, 100, 0, 40, 75, 100, 20, 90, 55][$index % 12],
            ]];
        });
    }
}
