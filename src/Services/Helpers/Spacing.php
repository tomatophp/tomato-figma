<?php

namespace TomatoPHP\TomatoFigma\Services\Helpers;

trait Spacing
{
    private array $spacing = [];

    public function collectSpace(array $space): void
    {
        $this->spacing[] = $space;
    }

    public function getLastSpace(): array
    {
        return $this->spacing[count($this->spacing)-1];
    }
}
