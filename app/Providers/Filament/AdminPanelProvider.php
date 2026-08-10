<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->favicon(asset('images/favicon.png'))
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('4rem')
            ->brandname('Techstrota Admin')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->renderHook('panels::body.start', fn () =>'
                <style>
                    /* =======================
                    SIDEBAR – LIGHT MODE
                    ======================= */
                    .fi-sidebar {
                        background-color: #FFFAFA !important;
                        box-shadow: inset -1px 0 0 #D1D5DB;
                    }

                    /* Main content */
                    /* Light mode */
                    .fi-main {
                        background: #FFFFFF !important;
                    }

                    /* Dark mode */
                    .dark .fi-main {
                        background: #000000 !important;
                    }

                    /* Active menu item */

                    .fi-sidebar-item-active .fi-sidebar-item-button {
                        background: #F8F2E8 !important;
                        border-left: 4px solid #D97706;
                        color: #D97706 !important;

                        border-radius: 10px;
                        font-weight: 600;

                        box-shadow: 0 2px 8px rgba(0,0,0,.05);
                    }

                    .fi-sidebar-item-active svg {
                        color: #D97706 !important;
                    }

                    .fi-sidebar-item-button {
                        border-radius: 10px;
                        margin: 4px 10px;
                        transition: all .25s ease;
                    }

                    .fi-sidebar-item-button:hover {
                        background: #F5F5F4;
                    }

                    .fi-sidebar-group-label {
                        color: #6B7280;
                        font-weight: 700;
                        font-size: 13px;
                        letter-spacing: .08em;
                        text-transform: uppercase;
                    }

                    /* =======================
                    SIDEBAR – DARK MODE
                    ======================= */
                    .dark .fi-sidebar {
                        background-color: #28282B !important; /* slate-900 */
                        box-shadow: inset -1px 0 0 #1F2937;   /* slate-800 */
                    }

                    .dark .fi-sidebar-item-active .fi-sidebar-item-button {
                        background: #1E293B !important;
                        border-left: 4px solid #F59E0B;
                        color: #FBBF24 !important;
                    }

                    .dark .fi-sidebar-item-active svg {
                        color: #FBBF24 !important;
                    }
                </style>'
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                \App\Filament\Widgets\AdminStats::class, // above first 3 cards
                \App\Filament\Widgets\InvoiceCard::class, // for invoices card
                \App\Filament\Widgets\AdminStatBottom::class, // below first 3 cards                
                \App\Filament\Widgets\AnalyticsStats::class, // for analytics overview
                \App\Filament\Widgets\AnalyticsChart::class, // for analytics chart
                \App\Filament\Widgets\LiveActivePagesTable::class, // for live active pages table
                \App\Filament\Widgets\PopularPagesTable::class, // for popular pages table
                
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
