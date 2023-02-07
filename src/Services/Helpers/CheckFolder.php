<?php

namespace TomatoPHP\TomatoFigma\Services\Helpers;

use Illuminate\Support\Facades\File;

trait CheckFolder
{
    public function checkFolder(string $path): void
    {
        $checkIfFolderExists = File::exists($path);
        if(!$checkIfFolderExists){
            File::makeDirectory($path, 0777, true, true);
        }
    }
}
