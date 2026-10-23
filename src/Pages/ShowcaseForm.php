<?php

namespace Mehuli\FilamentShadcn\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

/**
 * Contoh form dengan susunan artboard docs/mockup/gambar/Form.png:
 * kolom utama (informasi, harga, media, varian, konten, SEO) dan kolom samping
 * (publikasi, organisasi, pengiriman, label warna, info). Data contoh, tidak disimpan.
 */
class ShowcaseForm extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $slug = 'showcase-form';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('filament-shadcn::filament-shadcn.showcase.form_label');
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
        return 'Buat produk';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Isi detail produk. Kolom bertanda * wajib diisi.';
    }

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            static::getUrl() => 'Produk',
            'Buat',
        ];
    }

    public function mount(): void
    {
        $this->form->fill([
            'category' => 'drinkware',
            'description' => '<h3>Tetap dingin 24 jam, panas 12 jam</h3><p>Tumbler dinding ganda dari <strong>stainless steel 304</strong> food grade. Tutup <em>anti-tumpah</em>, muat di cup holder mobil.</p><ul><li>Kapasitas 500 ml</li><li>Bebas BPA, mudah dicuci</li></ul>',
            'summary' => 'Tumbler stainless 500 ml, tahan dingin 24 jam. Cocok untuk kerja dan perjalanan.',
            'discount' => 10,
            'unit' => 'pcs',
            'tax' => 'ppn',
            'variants' => [
                ['size' => '500 ml', 'color' => '#1e3a5f', 'stock' => null],
                ['size' => '500 ml', 'color' => '#e5e7eb', 'stock' => null],
                ['size' => '750 ml', 'color' => '#0f766e', 'stock' => null],
            ],
            'content' => [
                ['type' => 'heading', 'data' => ['text' => 'Kenapa memilih tumbler ini?']],
                ['type' => 'paragraph', 'data' => ['text' => 'Lapisan vakum ganda menjaga suhu minuman tetap stabil seharian…']],
            ],
            'tags' => ['tumbler', 'botol minum', 'stainless', 'hadiah kantor'],
            'is_visible' => true,
            'is_featured' => false,
            'status' => 'scheduled',
            'published_at' => now()->addDays(5)->setTime(9, 0)->toDateTimeString(),
            'collections' => ['new', 'office'],
            'couriers' => ['jne', 'jnt', 'sicepat', 'gosend'],
            'fragile' => true,
            'label_color' => '#2563eb',
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')->label('Pratinjau')->color('gray'),
            ActionGroup::make([
                Action::make('duplicate')->label('Duplikat')->icon(Heroicon::OutlinedDocumentDuplicate),
                Action::make('archive')->label('Arsipkan')->icon(Heroicon::OutlinedArchiveBox),
                Action::make('delete')->label('Hapus')->icon(Heroicon::OutlinedTrash)->color('danger'),
            ])
                ->label('Lainnya')
                ->icon(Heroicon::ChevronDown)
                ->iconPosition('after')
                ->button()
                ->color('gray'),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])->schema([
                    Group::make()->columnSpan(['lg' => 2])->schema([
                        Section::make('Informasi produk')
                            ->description('Section · TextInput, Select, RichEditor, Textarea')
                            ->collapsible()
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')->label('Nama produk')->required()->helperText('Status fokus (focus ring).'),
                                TextInput::make('slug')->label('Slug')->prefix('toko.id/p/')->disabled()->helperText('Prefix + status nonaktif (disabled).'),
                                Select::make('category')->label('Kategori')->required()->native(false)->options([
                                    'drinkware' => 'Peralatan minum',
                                    'kitchen' => 'Dapur',
                                    'office' => 'Kantor',
                                ]),
                                TextInput::make('sku')->label('SKU')->required()->hint('Hint: harus unik'),
                                RichEditor::make('description')->label('Deskripsi')->columnSpanFull()->helperText('RichEditor (Tiptap). Alternatif: MarkdownEditor.'),
                                Textarea::make('summary')->label('Ringkasan singkat')->rows(3)->maxLength(160)->columnSpanFull()->helperText('Textarea dengan batas karakter (160).'),
                            ]),

                        Section::make('Harga & stok')
                            ->description('Prefix/suffix, Slider, ToggleButtons, Radio')
                            ->columns(3)
                            ->schema([
                                TextInput::make('price')->label('Harga jual')->prefix('Rp')->numeric(),
                                TextInput::make('compare_price')->label('Harga coret')->prefix('Rp')->numeric(),
                                TextInput::make('weight')->label('Berat')->suffix('gram')->numeric(),
                                Slider::make('discount')->label('Diskon anggota')->range(minValue: 0, maxValue: 50)->step(1)->columnSpanFull(),
                                ToggleButtons::make('unit')->label('Satuan jual')->inline()->options([
                                    'pcs' => 'Satuan',
                                    'dozen' => 'Lusin',
                                    'wholesale' => 'Grosir',
                                ])->icons([
                                    'pcs' => Heroicon::OutlinedCube,
                                    'dozen' => Heroicon::OutlinedSquares2x2,
                                    'wholesale' => Heroicon::OutlinedTruck,
                                ])->helperText('ToggleButtons'),
                                Radio::make('tax')->label('Pajak')->inline()->columnSpan(2)->options([
                                    'ppn' => 'PPN 11%',
                                    'none' => 'Bebas pajak',
                                    'custom' => 'Tarif khusus',
                                ])->descriptions([
                                    'ppn' => 'Termasuk dalam harga',
                                    'none' => 'Produk dikecualikan',
                                    'custom' => 'Atur per wilayah',
                                ]),
                            ]),

                        Section::make('Media')
                            ->description('FileUpload · multiple, image, reorderable')
                            ->schema([
                                FileUpload::make('images')
                                    ->hiddenLabel()
                                    ->image()
                                    ->multiple()
                                    ->reorderable()
                                    ->maxFiles(6)
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                                    ->panelLayout('grid'),
                            ]),

                        Section::make('Varian')
                            ->description('Repeater · bisa diurutkan, diciutkan, dikloning · ColorPicker di dalamnya')
                            ->schema([
                                Repeater::make('variants')
                                    ->hiddenLabel()
                                    ->columns(3)
                                    ->collapsible()
                                    ->cloneable()
                                    ->itemLabel(fn (array $state): string => trim(($state['size'] ?? '') . ' · ' . strtoupper((string) ($state['color'] ?? '')), ' ·'))
                                    ->addActionLabel('Tambah varian')
                                    ->schema([
                                        TextInput::make('size')->label('Ukuran'),
                                        ColorPicker::make('color')->label('Warna'),
                                        TextInput::make('stock')->label('Stok')->numeric(),
                                    ]),
                            ]),

                        Section::make('Konten halaman produk')
                            ->description('Builder · susun blok konten bebas')
                            ->schema([
                                Builder::make('content')
                                    ->hiddenLabel()
                                    ->addActionLabel('Tambah blok')
                                    ->blocks([
                                        Block::make('heading')->label('Judul')->icon(Heroicon::OutlinedH1)->schema([TextInput::make('text')->hiddenLabel()]),
                                        Block::make('paragraph')->label('Paragraf')->icon(Heroicon::OutlinedBars3BottomLeft)->schema([Textarea::make('text')->hiddenLabel()]),
                                        Block::make('gallery')->label('Galeri')->icon(Heroicon::OutlinedPhoto)->schema([TextInput::make('caption')->label('Keterangan')]),
                                        Block::make('video')->label('Video')->icon(Heroicon::OutlinedVideoCamera)->schema([TextInput::make('url')->label('URL')->url()]),
                                        Block::make('faq')->label('Tanya jawab')->icon(Heroicon::OutlinedQuestionMarkCircle)->schema([TextInput::make('question')->label('Pertanyaan')]),
                                    ]),
                            ]),

                        Tabs::make('Lainnya')->tabs([
                            Tab::make('SEO')->schema([
                                TextInput::make('meta_title')->label('Judul meta'),
                                TagsInput::make('tags')->label('Kata kunci')->placeholder('Ketik lalu Enter…')->helperText('TagsInput'),
                            ]),
                            Tab::make('Spesifikasi')->schema([
                                TextInput::make('material')->label('Bahan'),
                            ]),
                            Tab::make('Embed')->schema([
                                Textarea::make('embed')->label('Kode embed')->rows(3),
                            ]),
                        ]),
                    ]),

                    Group::make()->columnSpan(['lg' => 1])->schema([
                        Section::make('Publikasi')
                            ->description('Toggle, Select, DateTimePicker')
                            ->schema([
                                Toggle::make('is_visible')->label('Tampilkan di toko')->helperText('Produk bisa dilihat pembeli'),
                                Toggle::make('is_featured')->label('Produk unggulan')->helperText('Muncul di beranda'),
                                Select::make('status')->label('Status')->native(false)->options([
                                    'draft' => 'Draf',
                                    'scheduled' => 'Terjadwal',
                                    'published' => 'Terbit',
                                ]),
                                DateTimePicker::make('published_at')->label('Jadwal terbit')->native(false)->seconds(false),
                            ]),

                        Section::make('Organisasi')
                            ->description('Select multiple + searchable')
                            ->schema([
                                Select::make('collections')->label('Koleksi')->multiple()->searchable()->options([
                                    'new' => 'Produk baru',
                                    'office' => 'Perlengkapan kantor',
                                    'gift' => 'Ide hadiah',
                                    'promo' => 'Promo akhir tahun',
                                    'outdoor' => 'Aktivitas luar ruang',
                                ]),
                            ]),

                        Section::make('Pengiriman')
                            ->description('CheckboxList, Checkbox, Fieldset')
                            ->schema([
                                CheckboxList::make('couriers')->label('Kurir tersedia')->columns(2)->options([
                                    'jne' => 'JNE',
                                    'jnt' => 'J&T',
                                    'sicepat' => 'SiCepat',
                                    'pos' => 'Pos Indonesia',
                                    'gosend' => 'GoSend',
                                    'grab' => 'GrabExpress',
                                ]),
                                Checkbox::make('fragile')->label('Barang mudah pecah')->helperText('Tambahkan label dan packing kayu.'),
                                Fieldset::make('Dimensi paket')->columns(3)->schema([
                                    TextInput::make('length')->label('Panjang')->suffix('cm')->numeric(),
                                    TextInput::make('width')->label('Lebar')->suffix('cm')->numeric(),
                                    TextInput::make('height')->label('Tinggi')->suffix('cm')->numeric(),
                                ]),
                            ]),

                        Section::make('Label warna')
                            ->description('ColorPicker')
                            ->schema([
                                ColorPicker::make('label_color')->hiddenLabel(),
                            ]),

                        Section::make()
                            ->schema([
                                Text::make('Placeholder / TextEntry')->color('gray'),
                                TextEntry::make('created_by')->label('Dibuat oleh')->state('Admin Demo')->inlineLabel(),
                                TextEntry::make('saved_at')->label('Terakhir disimpan')->state('Draf · 1 menit lalu')->inlineLabel(),
                                TextEntry::make('record_id')->label('ID')->state('— (belum dibuat)')->inlineLabel(),
                            ]),
                    ]),
                ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('create')
                ->footer([
                    Actions::make([
                        Action::make('create')->label('Buat')->submit('create'),
                        Action::make('createAnother')->label('Buat & buat lagi')->color('gray')->action(fn () => $this->create()),
                        Action::make('cancel')->label('Batal')->link()->color('gray')->url(static::getUrl()),
                    ]),
                ]),
        ]);
    }

    public function create(): void
    {
        $this->form->getState();

        Notification::make()
            ->success()
            ->title('Produk dibuat (contoh)')
            ->body('Halaman ini hanya contoh; data tidak disimpan.')
            ->send();
    }
}
