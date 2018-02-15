<?php
/**
 * Dependency definitions for zeelot3k/kohana-dependencies dependency container
 */
return [
    'warden' => [
        'config'       => [
            'configuration' => [
                '_settings' => [
                    'class'     => Ingenerator\Warden\Core\Config\Configuration::class,
                    'arguments' => ['@warden.core@'],
                ],
            ],
        ],
        'interactor'   => [
            'email_verification' => [
                '_settings' => [
                    'class'     => Ingenerator\Warden\Core\Interactor\EmailVerificationInteractor::class,
                    'arguments' => [
                        '%warden.validator.validator%',
                        '%warden.repository.user%',
                        '%warden.support.token_service%',
                        '%warden.support.url_provider%',
                        '%warden.notification.mailer%',
                    ],
                ],
            ],
            'login'              => [
                '_settings' => [
                    'class'     => Ingenerator\Warden\Core\Interactor\LoginInteractor::class,
                    'arguments' => [
                        '%warden.validator.validator%',
                        '%warden.repository.user%',
                        '%warden.support.password_hasher%',
                        '%warden.user_session.session%',
                        '%warden.interactor.email_verification%',
                    ],
                ],
            ],
            'password_reset'     => [
                '_settings' => [
                    'class'     => Ingenerator\Warden\Core\Interactor\PasswordResetInteractor::class,
                    'arguments' => [
                        '%warden.validator.validator%',
                        '%warden.support.password_hasher%',
                        '%warden.support.token_service%',
                        '%warden.repository.user%',
                        '%warden.user_session.session%',
                    ],
                ],
            ],
            'user_registration'  => [
                '_settings' => [
                    'class'     => Ingenerator\Warden\Core\Interactor\UserRegistrationInteractor::class,
                    'arguments' => [
                        '%warden.config.configuration%',
                        '%warden.validator.validator%',
                        '%warden.support.password_hasher%',
                        '%warden.support.token_service%',
                        '%warden.repository.user%',
                        '%warden.user_session.session%',
                    ],
                ],
            ],
        ],
        'notification' => [
            'mailer' => [
                '_settings' => [
                    'class'     => Ingenerator\Warden\UI\Kohana\Notification\SwiftNotificationMailer::class,
                    'arguments' => [
                        '%swiftmailer.mailer%',
                        '%kohana.message_provider%',
                        '@warden.email_notifications@',
                    ],
                ],
            ],
        ],
        'repository'   => [
            'user' => [
                '_settings' => [
                    'class'     => Ingenerator\Warden\Persistence\Doctrine\Repository\DoctrineUserRepository::class,
                    'arguments' => [
                        '%warden.config.configuration%',
                        '%doctrine.entity_manager%',
                    ],
                ],
            ],
        ],
        'support'      => [
            'interactor_request_factory' => [
                '_settings' => [
                    'class'     => Ingenerator\Warden\Core\Support\InteractorRequestFactory::class,
                    'arguments' => ['%warden.config.configuration%'],
                ],
            ],
            'password_hasher'            => [
                '_settings' => [
                    'class'     => Ingenerator\Warden\Core\Support\NativePasswordHasher::class,
                    'arguments' => ['@warden.hashing@'],
                ],

            ],
            'token_service'              => [
                '_settings' => [
                    'class'     => Ingenerator\Warden\UI\Kohana\Dummy\TokenistaTokenService::class,
                    'arguments' => ['%tokenista.tokenista%'],
                ],
            ],
            'url_provider'               => [
                '_settings' => [
                    'class' => Ingenerator\Warden\UI\Kohana\Dummy\ReverseRouteURLProvider::class,
                ],
            ],
        ],
        'user_session' => [
            'session' => [
                '_settings' => [
                    'class'     => Ingenerator\Warden\UI\Kohana\UserSession\KohanaUserSession::class,
                    'arguments' => [
                        '%kohana.session%',
                        '%warden.repository.user%',
                    ],
                ],
            ],
        ],
        'validator'    => [
            'validator' => [
                '_settings' => [
                    'class'     => Ingenerator\Warden\Validator\Symfony\SymfonyValidator::class,
                    'arguments' => [
                        '%validation.validator%',
                    ],
                ],
            ],
        ],
        'view'         => [
            'login'        => [
                'login'          => [
                    '_settings' => [
                        'class'     => Ingenerator\Warden\UI\Kohana\View\LoginView::class,
                        'arguments' => [
                            '%view.layout.default%',
                        ],
                    ],
                ],
                'password_reset' => [
                    '_settings' => [
                        'class'     => Ingenerator\Warden\UI\Kohana\View\PasswordResetView::class,
                        'arguments' => [
                            '%view.layout.default%',
                        ],
                    ],
                ],
            ],
            'registration' => [
                'email_verification' => [
                    '_settings' => [
                        'class'     => Ingenerator\Warden\UI\Kohana\View\EmailVerificationView::class,
                        'arguments' => [
                            '%view.layout.default%',
                        ],
                    ],
                ],
                'registration'       => [
                    '_settings' => [
                        'class'     => Ingenerator\Warden\UI\Kohana\View\RegistrationView::class,
                        'arguments' => [
                            '%view.layout.default%',
                        ],
                    ],
                ],
            ],
            'profile' => [
                'profile' => [
                    '_settings' => [
                        'class'     => Ingenerator\Warden\UI\Kohana\View\ProfileView::class,
                        'arguments' => [
                            '%view.layout.default%',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
