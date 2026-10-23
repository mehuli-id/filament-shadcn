<?php

namespace Mehuli\FilamentShadcn\Infolists\Components;

use Closure;
use Filament\Infolists\Components\Entry;
use Filament\Support\Concerns\HasColor;

/**
 * ProgressEntry::make('completion')->maxValue(100)->color(fn ($state) => $state >= 100 ? 'success' : 'primary')
 */
class ProgressEntry extends Entry
{
    use HasColor;

    protected string $view = 'filament-shadcn::infolists.progress-entry';

    protected int | float | Closure $maxValue = 100;

    protected bool | Closure $shouldShowValue = true;

    public function maxValue(int | float | Closure $value): static
    {
        $this->maxValue = $value;

        return $this;
    }

    public function showValue(bool | Closure $condition = true): static
    {
        $this->shouldShowValue = $condition;

        return $this;
    }

    public function getMaxValue(): int | float
    {
        return $this->evaluate($this->maxValue) ?: 100;
    }

    public function shouldShowValue(): bool
    {
        return (bool) $this->evaluate($this->shouldShowValue);
    }

    public function getPercentage(): float
    {
        $max = $this->getMaxValue();

        return $max > 0 ? max(0, min(100, ((float) $this->getState() / $max) * 100)) : 0;
    }
}
