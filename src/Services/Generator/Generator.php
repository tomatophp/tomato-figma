<?php

namespace TomatoPHP\TomatoFigma\Services\Generator;

use Illuminate\Support\Collection;
use stdClass;

interface Generator
{
    public static function make(stdClass $json, string $key,array $fonts,string $dir): void;
    public function generate(string $key,array $fonts,string $dir): void;
    public function json(stdClass $json): static;
    public function path(string $path): static;
    public function content(Collection $content): static;
}
