<?php
return [
    'core'                => [
        'classmap'     => [
            'entity' => [
            ],
        ],
        'registration' => [
            'require_confirmed_email' => TRUE,
        ],
    ],
    'email_notifications' => [
        'email_sender'      => 'nobody@nowhere.com',
        'email_sender_name' => 'Nobody',
    ],
    'hashing'             => [
        'algorithm' => PASSWORD_DEFAULT,
        'options'   => [
            'cost' => 10,
        ],
    ],
];
