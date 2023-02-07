<?php


use Illuminate\Support\Facades\Route;

Route::middleware(["web", "auth",'splade'])->prefix('/admin/figma' )->name(  "admin.figma.")->group(static function () {
    Route::get("/", [\TomatoPHP\TomatoFigma\Http\Controllers\FigmaController::class, 'index'])->name('index');
    Route::post("/", [\TomatoPHP\TomatoFigma\Http\Controllers\FigmaController::class, 'files'])->name('files');
    Route::post("/image", [\TomatoPHP\TomatoFigma\Http\Controllers\FigmaController::class, 'image'])->name('image');
});
