<?php

namespace Mehuli\FilamentShadcn\Infolists\Components;

use Closure;
use Filament\Infolists\Components\Entry;

/**
 * Menampilkan teks/kode/log sebagai Code Block shadcn.
 * CodeBlockEntry::make('payload')->title('webhook.json')->language('json')->lineNumbers()->maxHeight('18rem')
 */
class CodeBlockEntry extends Entry
{
    protected string $view = 'filament-shadcn::infolists.code-block-entry';

    protected string | Closure | null $title = null;

    protected string | Closure | null $language = null;

    protected bool | Closure $hasLineNumbers = false;

    protected bool | Closure $isDiff = false;

    protected bool | Closure $shouldWrap = false;

    protected string | Closure | null $maxHeight = null;

    public function title(string | Closure | null $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function language(string | Closure | null $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function lineNumbers(bool | Closure $condition = true): static
    {
        $this->hasLineNumbers = $condition;

        return $this;
    }

    public function diff(bool | Closure $condition = true): static
    {
        $this->isDiff = $condition;

        return $this;
    }

    public function wrap(bool | Closure $condition = true): static
    {
        $this->shouldWrap = $condition;

        return $this;
    }

    public function maxHeight(string | Closure | null $height): static
    {
        $this->maxHeight = $height;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->evaluate($this->title);
    }

    public function getLanguage(): ?string
    {
        return $this->evaluate($this->language);
    }

    public function hasLineNumbers(): bool
    {
        return (bool) $this->evaluate($this->hasLineNumbers);
    }

    public function isDiff(): bool
    {
        return (bool) $this->evaluate($this->isDiff);
    }

    public function shouldWrap(): bool
    {
        return (bool) $this->evaluate($this->shouldWrap);
    }

    public function getMaxHeight(): ?string
    {
        return $this->evaluate($this->maxHeight);
    }

    public function getCode(): string
    {
        $state = $this->getState();

        if (is_array($state) || is_object($state)) {
            return (string) json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return (string) $state;
    }
}
