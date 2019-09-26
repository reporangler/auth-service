<?php

return [
    'defaults' => [
        'guard' => 'token',
    ],

    'guards' => [
        'login'     => ['driver' => 'login'],
        'repo'      => ['driver' => 'repo'],
        'token'     => ['driver' => 'token'],
    ]
];
