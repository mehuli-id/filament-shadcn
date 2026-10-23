<?php

namespace Mehuli\FilamentShadcn\Schemas\Components;

use Closure;
use Filament\Schemas\Components\Component;

/**
 * Accordion untuk form/infolist Filament.
 *
 * Accordion::make([
 *     AccordionItem::make('Pengiriman')->schema([...]),
 *     AccordionItem::make('Pembayaran')->schema([...]),
 * ])->multiple()->defaultOpen('pengiriman')
 */
class Accordion extends Component
{
    protected string $view = 'filament-shadcn::schemas.accordion';

    protected bool | Closure $isMultiple = false;

    protected bool | Closure $isCollapsible = true;

    protected bool | Closure $isContained = true;

    /**
     * @var string | array<string> | Closure | null
     */
    protected string | array | Closure | null $defaultOpen = null;

    /**
     * @param  array<Component> | Closure  $schema
     */
    final public function __construct(array | Closure $schema = [])
    {
        $this->schema($schema);
    }

    /**
     * @param  array<Component> | Closure  $schema
     */
    public static function make(array | Closure $schema = []): static
    {
        $static = app(static::class, ['schema' => $schema]);
        $static->configure();

        return $static;
    }

    public function multiple(bool | Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function collapsible(bool | Closure $condition = true): static
    {
        $this->isCollapsible = $condition;

        return $this;
    }

    public function contained(bool | Closure $condition = true): static
    {
        $this->isContained = $condition;

        return $this;
    }

    /**
     * @param  string | array<string> | Closure | null  $items  value item (default: slug judul)
     */
    public function defaultOpen(string | array | Closure | null $items): static
    {
        $this->defaultOpen = $items;

        return $this;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->isMultiple);
    }

    public function isCollapsible(): bool
    {
        return (bool) $this->evaluate($this->isCollapsible);
    }

    public function isContained(): bool
    {
        return (bool) $this->evaluate($this->isContained);
    }

    /**
     * @return array<string>
     */
    public function getDefaultOpen(): array
    {
        return array_values((array) ($this->evaluate($this->defaultOpen) ?? []));
    }
}
