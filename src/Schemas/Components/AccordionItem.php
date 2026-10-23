<?php

namespace Mehuli\FilamentShadcn\Schemas\Components;

use Closure;
use Filament\Schemas\Components\Component;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class AccordionItem extends Component
{
    protected string $view = 'filament-shadcn::schemas.accordion-item';

    protected string | Htmlable | Closure $heading;

    protected string | Closure | null $value = null;

    final public function __construct(string | Htmlable | Closure $heading)
    {
        $this->heading = $heading;
    }

    public static function make(string | Htmlable | Closure $heading): static
    {
        $static = app(static::class, ['heading' => $heading]);
        $static->configure();

        return $static;
    }

    public function value(string | Closure | null $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getHeading(): string | Htmlable
    {
        return $this->evaluate($this->heading);
    }

    public function getValue(): string
    {
        $value = $this->evaluate($this->value);

        if (filled($value)) {
            return (string) $value;
        }

        $heading = $this->getHeading();

        return Str::slug($heading instanceof Htmlable ? strip_tags($heading->toHtml()) : $heading) ?: 'item';
    }
}
