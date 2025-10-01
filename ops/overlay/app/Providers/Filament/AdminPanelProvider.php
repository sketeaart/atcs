<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Support\Facades\Gate;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('ATCS Admin')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->plugins([])
            ->navigationGroups([
                'ATCS',
            ])
            ->authGuard(fn() => 'web')
            ->middleware([
                // default middleware
            ])
            ->authorize(fn() => auth()->check() && auth()->user()->hasRole('Super Admin'));
    }
}

