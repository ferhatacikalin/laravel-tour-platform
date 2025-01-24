<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
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
        Relation::enforceMorphMap([
            'user' => 'App\Models\User'
        ]);
        Gate::define('view-tours', fn(?User $user) => true);
        Gate::define('view-tour', fn(?User $user, Tour $tour) => true);

        // Only admin and tour operators can create tours
        Gate::define('create-tour', fn(User $user) => 
            $user->isAdmin() || $user->isTourOperator()
        );

        // Only admin and tour owner can update/delete tours
        Gate::define('update-tour', fn(User $user, Tour $tour) => 
            $user->isAdmin() || $tour->user_id === $user->id
        );

        Gate::define('delete-tour', fn(User $user, Tour $tour) => 
            $user->isAdmin() || $tour->user_id === $user->id
        );
    }
}
