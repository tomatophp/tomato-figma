<?php

namespace TomatoPHP\TomatoFigma\Services\Helpers;

trait IDGenerator
{
    public function id(string $id): string
    {
        return "ID".substr(sha1($id), -8, -1);
    }
}
