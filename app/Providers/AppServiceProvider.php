<?php

namespace App\Providers;

use App\Model\User;
use App\Services\DatabaseAuthenticatorService;
use App\Services\LDAPAuthenticatorService;
use App\Services\PackageGroupService;
use App\Services\RepositoryService;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use RepoRangler\Services\MetadataClient;

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
        $this->app->bind(DatabaseAuthenticatorService::class, function(){
            return new DatabaseAuthenticatorService();
        });

        $this->app->bind(LDAPAuthenticatorService::class, function(){
            return new LDAPAuthenticatorService();
        });

        $this->app->bind(PackageGroupService::class, function(){
            return new PackageGroupService(app(MetadataClient::class));
        });

        $this->app->bind(RepositoryService::class, function(){
            return new RepositoryService(app(MetadataClient::class));
        });

        $this->app->bind('user-token', function(){
            return Auth::guard('token')->user()->token;
        });
    }
}
