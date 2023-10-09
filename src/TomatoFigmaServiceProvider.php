<?php

namespace TomatoPHP\TomatoFigma;

use Illuminate\Support\ServiceProvider;
use TomatoPHP\TomatoAdmin\Facade\TomatoMenu;
use TomatoPHP\TomatoAdmin\Services\Contracts\Menu;
use TomatoPHP\TomatoFigma\Menus\FigmaMenu;


class TomatoFigmaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //Register generate command
        $this->commands([
           \TomatoPHP\TomatoFigma\Console\TomatoFigmaInstall::class,
        ]);

        //Register Config file
        $this->mergeConfigFrom(__DIR__.'/../config/tomato-figma.php', 'tomato-figma');

        //Publish Config
        $this->publishes([
           __DIR__.'/../config/tomato-figma.php' => config_path('tomato-figma.php'),
        ], 'tomato-figma-config');

        //Register Migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        //Publish Migrations
        $this->publishes([
           __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'tomato-figma-migrations');
        //Register views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'tomato-figma');

        //Publish Views
        $this->publishes([
           __DIR__.'/../resources/views' => resource_path('views/vendor/tomato-figma'),
        ], 'tomato-figma-views');

        //Register Langs
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'tomato-figma');

        //Publish Lang
        $this->publishes([
           __DIR__.'/../resources/lang' => resource_path('lang/vendor/tomato-figma'),
        ], 'tomato-figma-lang');

        //Register Routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }

    public function boot(): void
    {
       TomatoMenu::register(
           Menu::make()
               ->label("Figma")
               ->group(__('Tools'))
               ->icon("bx bxl-figma")
               ->route("admin.figma.index")
       );
    }
}
