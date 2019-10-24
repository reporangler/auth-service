<?php

namespace App\Providers;

use App\Model\User;
use App\Services\DatabaseAuthenticatorService;
use App\Services\LDAPAuthenticatorService;
use App\Services\PackageGroupService;
use App\Services\RepositoryService;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
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
        $this->app->bind('user-token', function(){
            return Auth::guard('token')->user()->token;
        });
    }
}
