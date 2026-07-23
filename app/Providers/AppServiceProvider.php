<?php

namespace App\Providers;

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
        view()->composer('*', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $menus = \App\Models\Menu::whereNull('parent_id')
                    ->with(['children' => function($query) use ($user) {
                        $query->orderBy('order', 'asc');
                    }])
                    ->orderBy('order', 'asc')
                    ->get()
                    ->filter(function ($menu) use ($user) {
                        if (empty($menu->permission_name)) {
                            return true;
                        }
                        // Admin has all access
                        if ($user->hasRole('admin')) {
                            return true;
                        }
                        return $user->hasPermissionTo($menu->permission_name);
                    });
                $view->with('sidebarMenus', $menus);
            }
        });
    }
}
