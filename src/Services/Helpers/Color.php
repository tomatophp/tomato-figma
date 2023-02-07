<?php

namespace TomatoPHP\TomatoFigma\Services\Helpers;

class Color
{
    /**
     * @var object
     */
    public object $color;

    /**
     * @param object $color
     * @return static
     */
    public static function make(object $color): static
    {
        return (new static)->color($color);
    }

    /**
     * @param object $color
     * @return $this
     */
    public function color(object $color): static
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @return string|object
     */
    public function rgba(): string|object
    {
        return "rgba(" . (int)($this->color->r*255) .",". (int)($this->color->g*255) .",". (int)($this->color->b*255) .",".$this->color->a.")";
    }

    /**
     * @return string|object
     */
    public function hex(): string|object
    {
        return "#" . dechex($this->color->r*255).dechex($this->color->g*255).dechex($this->color->b*255);
    }
}
