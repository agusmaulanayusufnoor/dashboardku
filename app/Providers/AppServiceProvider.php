<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

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
        Inertia::share([
            'auth.user' => fn () => Auth::check() ? [
                'id' => Auth::id(),
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'username' => Auth::user()->username,
                'kantor' => Auth::user()->kantor
                    ? [
                        'id' => Auth::user()->kantor->id,
                        'nama_kantor' => Auth::user()->kantor->nama_kantor
                    ]
                    : null,
                'divisi' => Auth::user()->divisi
                    ? [
                        'id' => Auth::user()->divisi->id,
                        'nama_divisi' => Auth::user()->divisi->nama_divisi
                    ]
                    : null,
                'roles' => Auth::user()->roles->map(fn ($r) => [
                    'id' => $r->id,
                    'name' => $r->name
                ]),
            ] : null,
        ]);
    }
}
