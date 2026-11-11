<?php

declare(strict_types=1);

/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */
namespace Ingenerator\Warden\UI\Kohana\DependencyFactory;


use Ingenerator\Warden\Core\Config\Configuration;
use Ingenerator\Warden\Core\Interactor\ActivateAccountInteractor;
use Ingenerator\Warden\Core\Interactor\ChangeEmailInteractor;
use Ingenerator\Warden\Core\Interactor\ChangePasswordInteractor;
use Ingenerator\Warden\Core\Interactor\EmailVerificationInteractor;
use Ingenerator\Warden\Core\Interactor\LoginInteractor;
use Ingenerator\Warden\Core\Interactor\PasswordResetInteractor;
use Ingenerator\Warden\Core\Interactor\UserRegistrationInteractor;
use Ingenerator\Warden\UI\Kohana\Notification\SymfonyMailerNotificationMailer;
use Ingenerator\Warden\Persistence\Doctrine\Repository\DoctrineUserRepository;
use Ingenerator\Warden\Core\RateLimit\StorageBackedLeakyBucket;
use Ingenerator\Warden\Core\RateLimit\ApcuBucketStorage;
use Ingenerator\Warden\Core\Support\InteractorRequestFactory;
use Ingenerator\Warden\Core\Support\NativePasswordHasher;
use Ingenerator\Warden\UI\Kohana\Dummy\TokenistaTokenService;
use Ingenerator\Warden\UI\Kohana\Routing\WardenConfigBasedRouter;
use Ingenerator\Warden\UI\Kohana\UserSession\KohanaUserSession;
use Ingenerator\Warden\UI\Kohana\View\LoginView;
use Ingenerator\Warden\UI\Kohana\View\PasswordResetView;
use Ingenerator\Warden\UI\Kohana\View\EmailVerificationView;
use Ingenerator\Warden\UI\Kohana\View\RegistrationView;
use Ingenerator\Warden\UI\Kohana\View\ChangeEmailView;
use Ingenerator\Warden\UI\Kohana\View\ChangePasswordView;
use Ingenerator\Warden\UI\Kohana\View\ProfileView;
use Ingenerator\KohanaExtras\DependencyFactory\RequestExecutorFactory;
use Ingenerator\Warden\Core\Validator\SymfonyValidatorFactory;
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
use InvalidArgumentException;

class WardenKohanaDependencyFactory
{

    /**
     * @return array<string, array<string, array<string, array<string, array<string, mixed[]|string>>>>>
     */
    public static function definitions(): array
    {
        return [
            'warden' => [
                'config' => [
                    'configuration' => [
                        '_settings' => [
                            'class' => Configuration::class,
                            'arguments' => ['@warden.core@'],
                        ],
                    ],
                ],
                'interactor' => [
                    'activate_account' => [
                        '_settings' => [
                            'class' => ActivateAccountInteractor::class,
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
                            'class' => ChangeEmailInteractor::class,
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
                            'class' => ChangePasswordInteractor::class,
                            'arguments' => [
                                '%warden.validator.validator%',
                                '%warden.support.password_hasher%',
                                '%warden.repository.user%',
                            ],
                        ],
                    ],
                    'email_verification' => [
                        '_settings' => [
                            'class' => EmailVerificationInteractor::class,
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
                    'login' => [
                        '_settings' => [
                            'class' => LoginInteractor::class,
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
                    'password_reset' => [
                        '_settings' => [
                            'class' => PasswordResetInteractor::class,
                            'arguments' => [
                                '%warden.validator.validator%',
                                '%warden.support.password_hasher%',
                                '%warden.support.token_service%',
                                '%warden.repository.user%',
                                '%warden.user_session.session%',
                            ],
                        ],
                    ],
                    'user_registration' => [
                        '_settings' => [
                            'class' => UserRegistrationInteractor::class,
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
                            'class' => SymfonyMailerNotificationMailer::class,
                            'arguments' => [
                                '%symfonymailer.mailer%',
                                '%kohana.message_provider%',
                                '@warden.email_notifications@',
                            ],
                        ],
                    ],
                ],
                'repository' => [
                    'user' => [
                        '_settings' => [
                            'class' => DoctrineUserRepository::class,
                            'arguments' => [
                                '%warden.config.configuration%',
                                '%doctrine.entity_manager%',
                            ],
                        ],
                    ],
                ],
                'rate_limit' => [
                    'leaky_bucket' => [
                        '_settings' => [
                            'class' => StorageBackedLeakyBucket::class,
                            'arguments' => [
                                '%warden.rate_limit.leaky_bucket_storage%',
                                '@warden.rate_limits@',
                            ],
                        ],
                    ],
                    'leaky_bucket_storage' => [
                        '_settings' => [
                            'class' => ApcuBucketStorage::class,
                            'arguments' => [],
                        ],
                    ],
                ],
                'support' => [
                    'interactor_request_factory' => [
                        '_settings' => [
                            'class' => InteractorRequestFactory::class,
                            'arguments' => ['%warden.config.configuration%'],
                        ],
                    ],
                    'password_hasher' => [
                        '_settings' => [
                            'class' => NativePasswordHasher::class,
                            'arguments' => ['@warden.hashing@'],
                        ],

                    ],
                    'token_service' => [
                        '_settings' => [
                            'class' => TokenistaTokenService::class,
                            'arguments' => ['%tokenista.tokenista%'],
                        ],
                    ],
                    'url_provider' => [
                        '_settings' => [
                            'class' => WardenConfigBasedRouter::class,
                            'arguments' => ['@warden.url_routing@'],
                        ],
                    ],
                ],
                'user_session' => [
                    'session' => [
                        '_settings' => [
                            'class' => KohanaUserSession::class,
                            'arguments' => [
                                '%kohana.session%',
                                '%warden.repository.user%',
                            ],
                        ],
                    ],
                ],
                'validator' => [
                    'validator' => [
                        '_settings' => [
                            'class' => SymfonyValidatorFactory::class,
                            'constructor' => 'factory',
                        ],
                    ],
                ],
                'view' => [
                    'login' => [
                        'login' => [
                            '_settings' => [
                                'class' => LoginView::class,
                                'arguments' => [
                                    '%view.layout.default%',
                                    '%warden.support.url_provider%',
                                ],
                            ],
                        ],
                        'password_reset' => [
                            '_settings' => [
                                'class' => PasswordResetView::class,
                                'arguments' => [
                                    '%view.layout.default%',
                                ],
                            ],
                        ],
                    ],
                    'registration' => [
                        'email_verification' => [
                            '_settings' => [
                                'class' => EmailVerificationView::class,
                                'arguments' => [
                                    '%view.layout.default%',
                                ],
                            ],
                        ],
                        'registration' => [
                            '_settings' => [
                                'class' => RegistrationView::class,
                                'arguments' => [
                                    '%view.layout.default%',
                                ],
                            ],
                        ],
                    ],
                    'profile' => [
                        'change_email' => [
                            '_settings' => [
                                'class' => ChangeEmailView::class,
                                'arguments' => [
                                    '%view.layout.default%',
                                ],
                            ],
                        ],
                        'change_password' => [
                            '_settings' => [
                                'class' => ChangePasswordView::class,
                                'arguments' => [
                                    '%view.layout.default%',
                                ],
                            ],
                        ],
                        'profile' => [
                            '_settings' => [
                                'class' => ProfileView::class,
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
     * @return array
     */
    public static function controllerDefinitions(
        ?array $only_controllers = NULL,
        array $not_controllers = [],
    ) {
        $controllers = [
            ChangeEmailController::class => [
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
            LoginController::class => [
                '%warden.support.interactor_request_factory%',
                '%warden.interactor.login%',
                '%warden.view.login.login%',
                '%warden.support.url_provider%',
                '%warden.user_session.session%',
                '%kohana.psr_log%',
            ],
            LogoutController::class => [
                '%warden.support.interactor_request_factory%',
                '%warden.support.url_provider%',
                '%warden.user_session.session%',
            ],
            ProfileController::class => [
                '%warden.support.interactor_request_factory%',
                '%warden.support.url_provider%',
                '%warden.view.profile.profile%',
                '%warden.user_session.session%',
            ],
            RegisterController::class => [
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
            VerifyEmailController::class => [
                '%warden.support.interactor_request_factory%',
                '%warden.interactor.email_verification%',
                '%warden.view.registration.email_verification%',
                '%warden.support.url_provider%',
                '%warden.user_session.session%',
                '%kohana.psr_log%',
            ],
        ];

        if ($only_controllers !== NULL) {
            // Filter based on the whitelist, first checking that the caller's arguments are valid
            if ($not_controllers !== []) {
                throw new InvalidArgumentException('Either pass `only_controllers` *or* `not_controllers`');
            }

            if ($undefined = array_diff($only_controllers, array_keys($controllers))) {
                throw new InvalidArgumentException(
                    'Some `only_controllers` were not defined: '.implode(', ', $undefined)
                );
            }

            $controllers = array_intersect_key($controllers, array_fill_keys($only_controllers, TRUE));
        } else if ($not_controllers !== []) {
            // Filter based on the blacklist, first checking that the caller's arguments are valid
            if ($undefined = array_diff($not_controllers, array_keys($controllers))) {
                throw new InvalidArgumentException(
                    'Some `not_controllers` were not defined: '.implode(', ', $undefined)
                );
            }
            $controllers = array_diff_key($controllers, array_fill_keys($not_controllers, TRUE));
        }

        return RequestExecutorFactory::controllerDefinitions($controllers);
    }
}
