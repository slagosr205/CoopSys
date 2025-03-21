<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
       
            FilamentView::registerRenderHook(
                'panels::auth.login.form.after',
            
                fn (): string => Blade::render(string: '@vite(\'resources/css/custom_login.css\')'),
            );
        

       FilamentView::registerRenderHook(
            'panels::body.end',
        
            fn (): string => Blade::render(string: '@vite(\'resources/css/custom_login.css\')'),
        );

        


    }
}
