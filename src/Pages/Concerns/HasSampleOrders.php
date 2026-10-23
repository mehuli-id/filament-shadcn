<?php

namespace Mehuli\FilamentShadcn\Pages\Concerns;

use Filament\Support\Icons\Heroicon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Data pesanan contoh (statis) untuk tabel di halaman Showcase.
 */
trait HasSampleOrders
{
    /**
     * @return array<string, array{label: string, color: string, icon: Heroicon}>
     */
    protected function orderStatuses(): array
    {
        return [
            'new' => ['label' => 'Baru', 'color' => 'info', 'icon' => Heroicon::OutlinedSparkles],
            'processing' => ['label' => 'Diproses', 'color' => 'warning', 'icon' => Heroicon::OutlinedArrowPath],
            'shipped' => ['label' => 'Dikirim', 'color' => 'info', 'icon' => Heroicon::OutlinedTruck],
            'done' => ['label' => 'Selesai', 'color' => 'success', 'icon' => Heroicon::OutlinedCheckCircle],
            'cancelled' => ['label' => 'Dibatalkan', 'color' => 'danger', 'icon' => Heroicon::OutlinedXCircle],
        ];
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
