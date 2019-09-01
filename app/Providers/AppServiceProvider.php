<?php

namespace App\Providers;

use App\Services\DatabaseAuthenticator;
use App\Services\LDAPAuthenticator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
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
