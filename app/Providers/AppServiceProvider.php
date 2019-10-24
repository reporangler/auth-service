<?php

namespace App\Providers;

use App\Model\User;
use App\Services\DatabaseAuthenticator;
use App\Services\LDAPAuthenticator;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Relation::morphMap([
            'user' => User::class,
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(DatabaseAuthenticator::class, function(){
            return new DatabaseAuthenticator();
        });

        $this->app->bind(LDAPAuthenticator::class, function(){
            return new LDAPAuthenticator();
        });
    }
}
