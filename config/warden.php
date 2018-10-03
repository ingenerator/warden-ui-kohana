<?php

use Ingenerator\Warden\UI\Kohana\Controller\ChangeEmailController;
use Ingenerator\Warden\UI\Kohana\Controller\ChangePasswordController;
use Ingenerator\Warden\UI\Kohana\Controller\CompleteActivateAccountController;
use Ingenerator\Warden\UI\Kohana\Controller\CompleteChangeEmailController;
use Ingenerator\Warden\UI\Kohana\Controller\LoginController;
use Ingenerator\Warden\UI\Kohana\Controller\LogoutController;
use Ingenerator\Warden\UI\Kohana\Controller\ProfileController;
use Ingenerator\Warden\UI\Kohana\Controller\RegisterController;
use Ingenerator\Warden\UI\Kohana\Controller\ResetPasswordController;
use Ingenerator\Warden\UI\Kohana\Controller\VerifyEmailController;

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
    'rate_limits' => [
        'bucket_lock'  => [
            'timeout_ms'    => 500,
            'retry_wait_ms' => 5,
            'lock_ttl_secs' => 5,
        ],
        'bucket_types' => [
            'warden.email.activate-account'       => [
                // Send max of one email every 20 minutes
                'bucket_size'       => 1,
                'leak_time_seconds' => 20 * 60,
            ],
            'warden.email.change-email'       => [
                // Send max of one email every 20 minutes
                'bucket_size'       => 1,
                'leak_time_seconds' => 20 * 60,
            ],
            'warden.email.register'       => [
                // Send max of one email every 20 minutes
                'bucket_size'       => 1,
                'leak_time_seconds' => 20 * 60,
            ],
            'warden.email.reset-password' => [
                // Send max of one email every 20 minutes
                'bucket_size'       => 1,
                'leak_time_seconds' => 20 * 60
            ],
            'warden.login.global' => [
                // Total login attempts by all accounts from all clients
                // You should customise this for each site both to match usage and so that
                // the value isn't publicly known
                'bucket_size'       => 90,
                'leak_time_seconds' => 0.2
            ],
            'warden.login.user'   => [
                // Login attempts by an individual user
                // You should customise this for each site both to match usage and so that
                // the value isn't publicly known
                'bucket_size'       => 10,
                'leak_time_seconds' => 1,
            ],
        ],
    ],
    'url_routing'         => [
        'after-login'             => [
            'url'              => '/profile',
            'route_controller' => FALSE,
        ],
        'after-logout'            => [
            'url'              => '/login',
            'route_controller' => FALSE,
        ],
        'after-verify-email'      => [
            'url'              => '/',
            'route_controller' => FALSE,
        ],
        'change-email' => [
            'url'              => '/profile/change-email',
            'route_controller' => ChangeEmailController::class,
        ],
        'change-password' => [
            'url'              => '/profile/change-password',
            'route_controller' => ChangePasswordController::class,
        ],
        'complete-activation' => [
            'url'              => '/login/activate',
            'route_controller' => CompleteActivateAccountController::class,
        ],
        'complete-change-email' => [
            'url'              => '/profile/change-email-confirm',
            'route_controller' => CompleteChangeEmailController::class,
        ],
        'default-user-home'       => [
            'url'              => '/profile',
            'route_controller' => ProfileController::class,
        ],
        'complete-password-reset' => [
            'url'              => '/login/reset-password',
            'route_controller' => ResetPasswordController::class,
        ],
        'complete-registration'   => [
            'url'              => '/register',
            'route_controller' => RegisterController::class,
        ],
        'login'                   => [
            'url'              => '/login',
            'route_controller' => LoginController::class,
        ],
        'logout'                  => [
            'url'              => '/logout',
            'route_controller' => LogoutController::class,
        ],
        'register-verify-email'   => [
            'url'              => '/register/verify-email',
            'route_controller' => VerifyEmailController::class,
        ],
    ],
];
