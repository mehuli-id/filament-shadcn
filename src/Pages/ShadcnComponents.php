<?php

namespace Mehuli\FilamentShadcn\Pages;

use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

/**
 * Showcase komponen Blade <x-shadcn::…> yang tidak ada padanannya di Filament:
 * Accordion, Aspect Ratio, Attachment, Bubble, Carousel, Code Block, Command,
 * Context Menu, Drawer, Hover Card, Item, Kbd, Marker, Menubar, Message,
 * Message Scroller, Navigation Menu, Popover, Progress, Questionnaire,
 * Resizable, Scroll Area, Separator, Skeleton, Spinner, Trace, Typography, Direction.
 */
class ShadcnComponents extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedPuzzlePiece;

    protected static ?string $slug = 'shadcn-components';

    protected string $view = 'filament-shadcn::pages.shadcn-components';

    /**
     * @var array<int, array{author: string, initials: string, text: string, mine: bool, time: string}>
     */
    public array $messages = [];

    public static function getNavigationLabel(): string
    {
        return __('filament-shadcn::filament-shadcn.components.navigation_label');
    }

    public static function getNavigationGroup(): string | UnitEnum | null
    {
        return config('filament-shadcn.showcase.navigation_group');
    }

    public static function getNavigationSort(): ?int
    {
        $sort = config('filament-shadcn.showcase.navigation_sort');

        return $sort === null ? null : $sort + 1;
    }

    public function getTitle(): string | Htmlable
    {
        return __('filament-shadcn::filament-shadcn.components.title');
    }

    public function getSubheading(): string | Htmlable | null
    {
        return __('filament-shadcn::filament-shadcn.components.subheading');
    }

    public function mount(): void
    {
        $this->messages = [
            ['author' => 'Rina', 'initials' => 'RS', 'text' => 'Halo, pesanan INV-2041 sudah dikirim?', 'mine' => false, 'time' => '09.10'],
            ['author' => 'Admin', 'initials' => 'AD', 'text' => 'Sudah, Bu. Resinya JNE 0123456789.', 'mine' => true, 'time' => '09.12'],
            ['author' => 'Rina', 'initials' => 'RS', 'text' => 'Terima kasih banyak!', 'mine' => false, 'time' => '09.13'],
        ];
    }

    /**
     * Contoh pesan baru masuk (Message Scroller ikut menggulir).
     */
    public function addMessage(): void
    {
        $replies = [
            'Barangnya sudah sampai, kondisinya bagus.',
            'Apakah ada warna lain untuk tumbler ini?',
            'Saya mau pesan 10 lagi untuk kantor.',
            'Bisa minta faktur pajaknya?',
        ];

        $this->messages[] = [
            'author' => 'Rina',
            'initials' => 'RS',
            'text' => $replies[count($this->messages) % count($replies)],
            'mine' => false,
            'time' => now()->format('H.i'),
        ];
    }

    /**
     * Dipanggil oleh <x-shadcn::questionnaire wire="saveAnswers" />.
     *
     * @param  array{answers: array<string, mixed>, texts: array<string, string>}  $data
     */
    public function saveAnswers(array $data): void
    {
        Notification::make()
            ->success()
            ->title('Jawaban tersimpan')
            ->body(collect($data['answers'] ?? [])
                ->map(fn (mixed $value, int | string $key): string => $key . ': ' . (is_array($value) ? implode(', ', $value) : $value))
                ->implode(' · '))
            ->send();
    }
}
