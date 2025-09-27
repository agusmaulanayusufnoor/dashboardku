<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use Illuminate\Http\Request;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Session;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        return [
            ...parent::share($request),
            'name' => config('app.name'),

            'quote' => [
                'message' => trim($message),
                'author' => trim($author),
            ],
            // ✅ Flash messages
            'flash' => [
                'success' => Session::get('success'),
                'error' => Session::get('error'),
                'info' => Session::get('info'),
            ],
            // ✅ Auth user data
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'roles' => $request->user()->roles->pluck('name'),
                    'permissions' => $request->user()->getAllPermissions()->pluck('name'),
                ] : null,
            ],

            // ✅ CSRF Token supaya bisa dipakai di Vue
            'csrf_token' => csrf_token(),

            // ✅ Ziggy route helper
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],

            // ✅ Sidebar state
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
