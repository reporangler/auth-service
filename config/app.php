<?php

$required = [];
foreach(['APP_NAME', 'APP_PROTOCOL', 'APP_DOMAIN'] as $key){
    $value = env($key);
    if($value === null) throw new Exception("The env-var '$key' cannot be empty'");
    $required[$key] = $value;
}

return [
    'name' => env('APP_NAME', 'auth'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'UTC',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'key' => env('APP_KEY', 'base64:' . base64_encode(random_bytes(32))),
    'cipher' => 'AES-256-CBC',

    'protocol' => $required['APP_PROTOCOL'],
    'domain' => env('APP_DOMAIN', $required['APP_DOMAIN']),

    // The number of hours a token can live before being erased
    'token_life_hours' => 6,

    'php_base_url'      => env('APP_PHP_URL',       "{$required['APP_PROTOCOL']}://php.{$required['APP_DOMAIN']}"),
    'npm_base_url'      => env('APP_NPM_URL',       "{$required['APP_PROTOCOL']}://npm.{$required['APP_DOMAIN']}"),
    'auth_base_url'     => env('APP_AUTH_URL',      "{$required['APP_PROTOCOL']}://auth.{$required['APP_DOMAIN']}"),
    'metadata_base_url' => env('APP_METADATA_URL',  "{$required['APP_PROTOCOL']}://metadata.{$required['APP_DOMAIN']}"),
    'storage_base_url'  => env('APP_STORAGE_URL',   "{$required['APP_PROTOCOL']}://storage.{$required['APP_DOMAIN']}"),

    'providers' => \Illuminate\Support\ServiceProvider::defaultProviders()->merge([
        Illuminate\Database\DatabaseServiceProvider::class,
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        RepoRangler\Providers\AppServiceProvider::class,
        RepoRangler\Providers\TokenServiceProvider::class,
    ])->toArray(),

    'aliases' => [],
];
