<?php

$required = [];
foreach([
    'POSTGRES_HOST',
    'POSTGRES_PORT',
    'POSTGRES_USER',
    'POSTGRES_PASSWORD',
    'POSTGRES_DB'
] as $key){
    $value = env($key);
    if($value === null) throw new Exception("The env-var '$key' cannot be empty'");
    $required[$key] = $value;
}

return [
    'default' => 'postgres',
    'migrations' => 'migrations',
    'connections' => [
        'postgres' => [
            'driver'   => 'pgsql',
            'host'     => $required['POSTGRES_HOST'],
            'port'     => $required['POSTGRES_PORT'],
            'username' => $required['POSTGRES_USER'],
            'password' => $required['POSTGRES_PASSWORD'],
            'database' => $required['POSTGRES_DB'],
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public'
        ]
    ]
];

