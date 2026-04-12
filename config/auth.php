<?php

return [
    'defaults' => [
        'guard' => 'token',
    ],

    'guards' => [
        'login'     => ['driver' => 'custom-login'],
        'repo'      => ['driver' => 'custom-repo'],
        'token'     => ['driver' => 'custom-token'],
    ]
];
