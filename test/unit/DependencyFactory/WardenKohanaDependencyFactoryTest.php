<?php

declare(strict_types=1);

/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */
namespace test\unit\Ingenerator\Warden\UI\Kohana\DependencyFactory;


use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Session;
use Ingenerator\KohanaView\ViewModel\NestedParentView;
use Doctrine\ORM\EntityManager;
use Ingenerator\KohanaExtras\DependencyContainer\DependencyContainer;
use Ingenerator\KohanaExtras\Message\KohanaMessageProvider;
use Ingenerator\Tokenista;
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
use Ingenerator\Warden\UI\Kohana\DependencyFactory\WardenKohanaDependencyFactory;
use InvalidArgumentException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class WardenKohanaDependencyFactoryTest extends TestCase
{

    /**
     * @return array{mixed}[]
     */
    public static function provider_service_names(): array
    {
        $container = new DependencyContainer(
            [
                '_include' => [
                    WardenKohanaDependencyFactory::definitions(),
                    WardenKohanaDependencyFactory::controllerDefinitions(),
                ],
            ]
        );
        $services  = [];
        foreach ($container->listServices() as $service_key) {
            $services[] = [$service_key];
        }

        return $services;
    }

    #[DataProvider('provider_service_names')]
    public function test_all_service_definitions_are_valid($service_name): void
    {
        $container = new DependencyContainer(
            [
                '_include' => [
                    WardenKohanaDependencyFactory::definitions(),
                    WardenKohanaDependencyFactory::controllerDefinitions(),
                    $this->dummy_dependencies(
                        [
                            'doctrine.entity_manager' => EntityManager::class,
                            'kohana.message_provider' => KohanaMessageProvider::class,
                            'kohana.psr_log'          => LoggerInterface::class,
                            'kohana.session'          => Session::class,
                            'symfonymailer.mailer'    => MailerInterface::class,
                            'tokenista.tokenista'     => Tokenista::class,
                            'validation.validator'    => ValidatorInterface::class,
                            'view.layout.default'     => NestedParentView::class,
                        ]
                    ),
                ],
            ]
        );

        $this->assertNotNull($container->get($service_name));
    }

    public static function provider_controller_subsets(): Iterator
    {
        $all_controllers = [
            '\\'.ChangeEmailController::class,
            '\\'.ChangePasswordController::class,
            '\\'.CompleteActivateAccountController::class,
            '\\'.CompleteChangeEmailController::class,
            '\\'.LoginController::class,
            '\\'.LogoutController::class,
            '\\'.ProfileController::class,
            '\\'.RegisterController::class,
            '\\'.ResetPasswordController::class,
            '\\'.VerifyEmailController::class,
        ];
        yield [
            [],
            $all_controllers,
        ];
        yield [
            ['only_controllers' => NULL],
            $all_controllers,
        ];
        yield [
            ['only_controllers' => []],
            [],
        ];
        yield [
            ['only_controllers' => [LoginController::class]],
            ['\\'.LoginController::class],
        ];
        yield [
            ['only_controllers' => [LoginController::class, RegisterController::class]],
            ['\\'.LoginController::class, '\\'.RegisterController::class],
        ];
        yield [
            [
                'only_controllers' => [LoginController::class, RegisterController::class],
                'not_controllers' => [],
            ],
            ['\\'.LoginController::class, '\\'.RegisterController::class],
        ];
        yield [
            ['not_controllers' => [LoginController::class]],
            array_values(
                array_filter(
                    $all_controllers,
                    fn(string $c): bool => $c !== '\\'.LoginController::class
                )
            ),
        ];
        yield [
            ['not_controllers' => [LoginController::class, RegisterController::class]],
            array_values(
                array_filter(
                    $all_controllers,
                    fn(string $c): bool => $c !== '\\'.LoginController::class && $c !== '\\'.RegisterController::class
                )
            ),
        ];
    }

    #[DataProvider('provider_controller_subsets')]
    public function test_it_optionally_allows_to_define_a_subset_of_controllers(array $args, array $expect_has): void
    {
        $definitions = WardenKohanaDependencyFactory::controllerDefinitions(...$args);

        $actual_has = [];
        foreach ($definitions['controller'] as $controller => $controller_def) {
            $this->assertSame(['_settings'], array_keys($controller_def), 'Should have settings for '.$controller);
            $actual_has[]  = $controller;
        }

        $this->assertEqualsCanonicalizing($expect_has, $actual_has);
    }

    public static function provider_invalid_controller_subsets(): Iterator
    {
        yield 'empty allow with a blacklist' => [
            ['only_controllers' => [], 'not_controllers' => [LoginController::class]],
        ];
        yield 'any allow with a blacklist' => [
            ['only_controllers' => [LoginController::class], 'not_controllers' => [RegisterController::class]],
        ];
        yield 'unknown only_controller' => [
            ['only_controllers' => ['foobar']],
        ];
        yield 'unknown not_controller' => [
            ['not_controllers' => ['foobar']],
        ];
    }

    #[DataProvider('provider_invalid_controller_subsets')]
    public function test_it_throws_on_invalid_combination_of_white_and_blacklist_controllers(array $args): void
    {
        $this->expectException(InvalidArgumentException::class);
        WardenKohanaDependencyFactory::controllerDefinitions(...$args);
    }

    protected function dummy_dependencies(array $dependencies): array
    {
        return array_map(
            function ($class_or_interface) {
                return [
                    '_settings' => [
                        'class' => $this->make_dummy_mock_class($class_or_interface),
                    ],
                ];
            },
            $dependencies
        );
    }

    protected function make_dummy_mock_class($class_or_interface)
    {
        $dummy_name = 'DummyDependency_'.\str_replace('\\', '_', $class_or_interface);
        if ( ! \class_exists($dummy_name)) {
            $this->getStubBuilder($class_or_interface)
                ->setStubClassName($dummy_name.'Raw')
                ->disableOriginalConstructor()
                ->getStub();

            // Ouch. This appears to be the simplest way to use phpunit's stubbing but splat the constructor
            // Which is required when the class we're mocking has either a private constructor or a set
            // of required arguments
            eval('class '.$dummy_name.' extends '.$dummy_name.'Raw { public function __construct() {} }');
        }

        return $dummy_name;
    }

}
