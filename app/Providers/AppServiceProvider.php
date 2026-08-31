<?php

namespace App\Providers;

use App\Listeners\LogMailDeliveryFailure;
use App\Support\Demo\DemoGuard;
use App\Queries\Navigation\MenuNavigationQuery;
use App\Services\Settings\SiteSettings;
use Illuminate\Mail\Events\MessageSendingFailed;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
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
        DemoGuard::ensureSafeConfiguration();

        Event::listen(MessageSendingFailed::class, [LogMailDeliveryFailure::class, 'handleMessageSendingFailed']);
        Event::listen(NotificationFailed::class, [LogMailDeliveryFailure::class, 'handleNotificationFailed']);

        View::composer('components.layouts.public', function ($view): void {
            $settings = app(SiteSettings::class)->get();
            $settings->loadMissing(['logoMedia', 'faviconMedia']);

            $navItems = app(MenuNavigationQuery::class)
                ->publicItemsForMenu('primary')
                ->filter(fn (array $item): bool => $item['is_publicly_visible'] && filled($item['url']))
                ->values();

            $view->with([
                'navItems' => $navItems,
                'siteSettings' => $settings,
            ]);
        });
    }
}
