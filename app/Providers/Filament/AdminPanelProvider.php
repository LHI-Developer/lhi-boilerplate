<?php

namespace App\Providers\Filament;


use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Modules\Core\Services\SettingService;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // Get dynamic settings with fallbacks
        $appName = $this->getAppName();
        $primaryColor = $this->getPrimaryColor();

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName($appName)
            ->colors([
                'primary' => $primaryColor,
            ])
            ->resources([
                \Modules\Core\Filament\Resources\UserResource::class,
                \Modules\Core\Filament\Resources\SystemSettingResource::class,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,

            ]);
    }

    /**
     * Get application name from settings with fallback.
     */
    private function getAppName(): string
    {
        try {
            $settingService = app(SettingService::class);
            return $settingService->get('app_name', 'SIT LHI Admin');
        } catch (\Exception $e) {
            // Fallback during initial setup or database issues
            return 'SIT LHI Admin';
        }
    }

    /**
     * Get primary color from settings with fallback.
     */
    private function getPrimaryColor(): Color|array
    {
        try {
            $settingService = app(SettingService::class);
            $hexColor = $settingService->get('panel_color');

            if ($hexColor) {
                return Color::hex($hexColor);
            }
        } catch (\Exception $e) {
            // Fallback during initial setup or database issues
        }

        return Color::Amber;
    }
}
