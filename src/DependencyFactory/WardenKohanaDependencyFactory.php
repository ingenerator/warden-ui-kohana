<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\DependencyFactory;


use Ingenerator\KohanaExtras\DependencyFactory\RequestExecutorFactory;
use Ingenerator\Warden\UI\Kohana\Controller\CompleteActivateAccountController;
use Ingenerator\Warden\UI\Kohana\Controller\ChangeEmailController;
use Ingenerator\Warden\UI\Kohana\Controller\ChangePasswordController;
use Ingenerator\Warden\UI\Kohana\Controller\CompleteChangeEmailController;
use Ingenerator\Warden\UI\Kohana\Controller\LoginController;
use Ingenerator\Warden\UI\Kohana\Controller\LogoutController;
use Ingenerator\Warden\UI\Kohana\Controller\ProfileController;
use Ingenerator\Warden\UI\Kohana\Controller\RegisterController;
use Ingenerator\Warden\UI\Kohana\Controller\ResetPasswordController;
use Ingenerator\Warden\UI\Kohana\Controller\VerifyEmailController;

class WardenKohanaDependencyFactory
{

    public static function definitions()
    {
        return [
            'warden' => [
                'config'       => [
                    'configuration' => [
                        '_settings' => [
                            'class'     => \Ingenerator\Warden\Core\Config\Configuration::class,
                            'arguments' => ['@warden.core@'],
                        ],
                    ],
                ],
                'interactor'   => [
                    'activate_account' => [
                        '_settings' => [
                            'class'     => \Ingenerator\Warden\Core\Interactor\ActivateAccountInteractor::class,
                            'arguments' => [
                                '%warden.validator.validator%',
                                '%warden.support.token_service%',
                                '%warden.repository.user%',
                                '%warden.user_session.session%',
                            ],
                        ],
                    ],
                    'change_email' => [
                        '_settings' => [
                            'class'     => \Ingenerator\Warden\Core\Interactor\ChangeEmailInteractor::class,
                            'arguments' => [
                                '%warden.validator.validator%',
                                '%warden.support.token_service%',
                                '%warden.repository.user%',
                                '%warden.user_session.session%',
                            ],
                        ],
                    ],
                    'change_password' => [
                        '_settings' => [
                            'class'     => \Ingenerator\Warden\Core\Interactor\ChangePasswordInteractor::class,
                            'arguments' => [
                                '%warden.validator.validator%',
                                '%warden.support.password_hasher%',
                                '%warden.repository.user%',
                            ],
                        ],
                    ],
                    'email_verification' => [
                        '_settings' => [
                            'class'     => \Ingenerator\Warden\Core\Interactor\EmailVerificationInteractor::class,
                            'arguments' => [
                                '%warden.validator.validator%',
                                '%warden.repository.user%',
                                '%warden.support.token_service%',
                                '%warden.rate_limit.leaky_bucket%',
                                '%warden.support.url_provider%',
                                '%warden.notification.mailer%',
                            ],
                        ],
                    ],
                    'login'              => [
                        '_settings' => [
                            'class'     => \Ingenerator\Warden\Core\Interactor\LoginInteractor::class,
                            'arguments' => [
                                '%warden.validator.validator%',
                                '%warden.rate_limit.leaky_bucket%',
                                '%warden.repository.user%',
                                '%warden.support.password_hasher%',
                                '%warden.user_session.session%',
                                '%warden.interactor.email_verification%',
                            ],
                        ],
                    ],
                    'password_reset'     => [
                        '_settings' => [
                            'class'     => \Ingenerator\Warden\Core\Interactor\PasswordResetInteractor::class,
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
                            'class'     => \Ingenerator\Warden\Core\Interactor\UserRegistrationInteractor::class,
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
                            'class'     => \Ingenerator\Warden\UI\Kohana\Notification\SwiftNotificationMailer::class,
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
                            'class'     => \Ingenerator\Warden\Persistence\Doctrine\Repository\DoctrineUserRepository::class,
                            'arguments' => [
                                '%warden.config.configuration%',
                                '%doctrine.entity_manager%',
                            ],
                        ],
                    ],
                ],
                'rate_limit' => [
                    'leaky_bucket'         => [
                        '_settings' => [
                            'class'     => \Ingenerator\Warden\Core\RateLimit\StorageBackedLeakyBucket::class,
                            'arguments' => [
                                '%warden.rate_limit.leaky_bucket_storage%',
                                '@warden.rate_limits@'
                            ],
                        ],
                    ],
                    'leaky_bucket_storage' => [
                        '_settings' => [
                            'class'     => \Ingenerator\Warden\Core\RateLimit\ApcuBucketStorage::class,
                            'arguments' => []
                        ],
                    ],
                ],
                'support'      => [
                    'interactor_request_factory' => [
                        '_settings' => [
                            'class'     => \Ingenerator\Warden\Core\Support\InteractorRequestFactory::class,
                            'arguments' => ['%warden.config.configuration%'],
                        ],
                    ],
                    'password_hasher'            => [
                        '_settings' => [
                            'class'     => \Ingenerator\Warden\Core\Support\NativePasswordHasher::class,
                            'arguments' => ['@warden.hashing@'],
                        ],

                    ],
                    'token_service'              => [
                        '_settings' => [
                            'class'     => \Ingenerator\Warden\UI\Kohana\Dummy\TokenistaTokenService::class,
                            'arguments' => ['%tokenista.tokenista%'],
                        ],
                    ],
                    'url_provider'               => [
                        '_settings' => [
                            'class'     => \Ingenerator\Warden\UI\Kohana\Routing\WardenConfigBasedRouter::class,
                            'arguments' => ['@warden.url_routing@'],
                        ],
                    ],
                ],
                'user_session' => [
                    'session' => [
                        '_settings' => [
                            'class'     => \Ingenerator\Warden\UI\Kohana\UserSession\KohanaUserSession::class,
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
                            'class'     => \Ingenerator\Warden\Validator\Symfony\SymfonyValidator::class,
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
                                'class'     => \Ingenerator\Warden\UI\Kohana\View\LoginView::class,
                                'arguments' => [
                                    '%view.layout.default%',
                                    '%warden.support.url_provider%',
                                ],
                            ],
                        ],
                        'password_reset' => [
                            '_settings' => [
                                'class'     => \Ingenerator\Warden\UI\Kohana\View\PasswordResetView::class,
                                'arguments' => [
                                    '%view.layout.default%',
                                ],
                            ],
                        ],
                    ],
                    'registration' => [
                        'email_verification' => [
                            '_settings' => [
                                'class'     => \Ingenerator\Warden\UI\Kohana\View\EmailVerificationView::class,
                                'arguments' => [
                                    '%view.layout.default%',
                                ],
                            ],
                        ],
                        'registration'       => [
                            '_settings' => [
                                'class'     => \Ingenerator\Warden\UI\Kohana\View\RegistrationView::class,
                                'arguments' => [
                                    '%view.layout.default%',
                                ],
                            ],
                        ],
                    ],
                    'profile'      => [
                        'change_email' => [
                            '_settings' => [
                                'class'     => \Ingenerator\Warden\UI\Kohana\View\ChangeEmailView::class,
                                'arguments' => [
                                    '%view.layout.default%',
                                ],
                            ],
                        ],
                        'change_password' => [
                            '_settings' => [
                                'class'     => \Ingenerator\Warden\UI\Kohana\View\ChangePasswordView::class,
                                'arguments' => [
                                    '%view.layout.default%',
                                ],
                            ],
                        ],
                        'profile' => [
                            '_settings' => [
                                'class'     => \Ingenerator\Warden\UI\Kohana\View\ProfileView::class,
                                'arguments' => [
                                    '%view.layout.default%',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param string[] Optionally if you only want to publish a subset of the default controllers, specify the class names to include
     *
     * @return array
     */
    public static function controllerDefinitions(array $only_controllers = NULL)
    {
        $controllers = [
            ChangeEmailController::class             => [
                '%warden.support.interactor_request_factory%',
                '%warden.interactor.email_verification%',
                '%warden.view.profile.change_email%',
                '%warden.support.url_provider%',
                '%warden.user_session.session%',
                '%kohana.psr_log%',
            ],
            ChangePasswordController::class => [
                '%warden.support.interactor_request_factory%',
                '%warden.interactor.change_password%',
                '%warden.view.profile.change_password%',
                '%warden.support.url_provider%',
                '%warden.user_session.session%',
                '%kohana.psr_log%',
            ],
            CompleteActivateAccountController::class => [
                '%warden.support.interactor_request_factory%',
                '%warden.interactor.activate_account%',
                '%warden.support.url_provider%',
                '%warden.user_session.session%',
            ],
            CompleteChangeEmailController::class => [
                '%warden.support.interactor_request_factory%',
                '%warden.interactor.change_email%',
                '%warden.support.url_provider%',
                '%warden.user_session.session%',
            ],
            LoginController::class         => [
                '%warden.support.interactor_request_factory%',
                '%warden.interactor.login%',
                '%warden.view.login.login%',
                '%warden.support.url_provider%',
                '%warden.user_session.session%',
                '%kohana.psr_log%',
            ],
            LogoutController::class        => [
                '%warden.support.interactor_request_factory%',
                '%warden.support.url_provider%',
                '%warden.user_session.session%',
            ],
            ProfileController::class       => [
                '%warden.support.interactor_request_factory%',
                '%warden.support.url_provider%',
                '%warden.view.profile.profile%',
                '%warden.user_session.session%',
            ],
            RegisterController::class      => [
                '%warden.support.interactor_request_factory%',
                '%warden.support.url_provider%',
                '%warden.interactor.user_registration%',
                '%warden.view.registration.registration%',
                '%warden.user_session.session%',
            ],
            ResetPasswordController::class => [
                '%warden.support.interactor_request_factory%',
                '%warden.interactor.password_reset%',
                '%warden.view.login.password_reset%',
                '%warden.support.url_provider%',
                '%warden.user_session.session%',
            ],
            VerifyEmailController::class   => [
                '%warden.support.interactor_request_factory%',
                '%warden.interactor.email_verification%',
                '%warden.view.registration.email_verification%',
                '%warden.support.url_provider%',
                '%warden.user_session.session%',
                '%kohana.psr_log%',
            ],
        ];

        if ($only_controllers !== NULL) {
            $controllers = \Arr::extract($controllers, $only_controllers);
        }

        return RequestExecutorFactory::controllerDefinitions($controllers);
    }
}
