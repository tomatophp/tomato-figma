<?php

namespace TomatoPHP\TomatoFigma;

use Illuminate\Support\ServiceProvider;
use TomatoPHP\TomatoFigma\Menus\FigmaMenu;
use TomatoPHP\TomatoPHP\Services\Menu\TomatoMenuRegister;


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
        ], 'config');

        //Register Migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        //Publish Migrations
        $this->publishes([
           __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'migrations');
        //Register views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'tomato-figma');

        //Publish Views
        $this->publishes([
           __DIR__.'/../resources/views' => resource_path('views/vendor/tomato-figma'),
        ], 'views');

        //Register Langs
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'tomato-figma');

        //Publish Lang
        $this->publishes([
           __DIR__.'/../resources/lang' => resource_path('lang/vendor/tomato-figma'),
        ], 'lang');

        //Register Routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        TomatoMenuRegister::registerMenu(FigmaMenu::class);

    }

    public function boot(): void
    {
        //you boot methods here
    }
}
