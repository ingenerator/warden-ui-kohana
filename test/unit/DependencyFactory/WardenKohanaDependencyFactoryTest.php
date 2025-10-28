<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Warden\UI\Kohana\DependencyFactory;


use PHPUnit\Framework\TestCase;
use PSR\Log\LoggerInterface;
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

class WardenKohanaDependencyFactoryTest extends TestCase
{

    public function provider_service_names()
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

    /**
     * @dataProvider provider_service_names
     */
    public function test_all_service_definitions_are_valid($service_name)
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
                            'symfonymailer.mailer'      => MailerInterface::class,
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

    public function provider_controller_subsets()
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

        return [
            [
                [],
                $all_controllers,
            ],
            [
                ['only_controllers' => NULL],
                $all_controllers,
            ],
            [
                ['only_controllers' => []],
                [],
            ],
            [
                ['only_controllers' => [LoginController::class]],
                ['\\'.LoginController::class],
            ],
            [
                ['only_controllers' => [LoginController::class, RegisterController::class]],
                ['\\'.LoginController::class, '\\'.RegisterController::class],
            ],
            [
                [
                    'only_controllers' => [LoginController::class, RegisterController::class],
                    'not_controllers' => [],
                ],
                ['\\'.LoginController::class, '\\'.RegisterController::class],
            ],
            [
                ['not_controllers' => [LoginController::class]],
                array_filter(
                    $all_controllers,
                    fn($c) => $c !== '\\'.LoginController::class
                ),
            ],
            [
                ['not_controllers' => [LoginController::class, RegisterController::class]],
                array_filter(
                    $all_controllers,
                    fn($c) => $c !== '\\'.LoginController::class && $c !== '\\'.RegisterController::class
                ),
            ],
        ];
    }

    /**
     * @dataProvider  provider_controller_subsets
     */
    public function test_it_optionally_allows_to_define_a_subset_of_controllers($args, $expect_has)
    {
        $definitions = WardenKohanaDependencyFactory::controllerDefinitions(...$args);

        $actual_has = [];
        foreach ($definitions['controller'] as $controller => $controller_def) {
            $this->assertSame(['_settings'], array_keys($controller_def), 'Should have settings for '.$controller);
            $actual_has[]  = $controller;
        }

        $this->assertEqualsCanonicalizing($expect_has, $actual_has);
    }

    public static function provider_invalid_controller_subsets()
    {
        return [
            'empty allow with a blacklist' => [
                ['only_controllers' => [], 'not_controllers' => [LoginController::class]],
            ],
            'any allow with a blacklist' => [
                ['only_controllers' => [LoginController::class], 'not_controllers' => [RegisterController::class]],
            ],
            'unknown only_controller' => [
                ['only_controllers' => ['foobar']],
            ],
            'unknown not_controller' => [
                ['not_controllers' => ['foobar']],
            ],
        ];
    }

    /**
     * @dataProvider provider_invalid_controller_subsets
     */
    public function test_it_throws_on_invalid_combination_of_white_and_blacklist_controllers(array $args)
    {
        $this->expectException(InvalidArgumentException::class);
        WardenKohanaDependencyFactory::controllerDefinitions(...$args);
    }

    protected function dummy_dependencies(array $dependencies)
    {
        $definitions = [];
        foreach ($dependencies as $key => $class_or_interface) {
            $definitions[$key] = [
                '_settings' => [
                    'class' => $this->make_dummy_mock_class($class_or_interface),
                ],
            ];
        }

        return $definitions;
    }

    protected function make_dummy_mock_class($class_or_interface)
    {
        $dummy_name = 'DummyDependency_'.\str_replace('\\', '_', $class_or_interface);
        if ( ! \class_exists($dummy_name)) {
            $this->getMockBuilder($class_or_interface)
                ->setMockClassName($dummy_name.'Raw')
                ->disableOriginalConstructor()
                ->getMock();

            // Ouch. This appears to be the simplest way to use phpunit's mocking but splat the constructor
            // Which is required when the class we're mocking has either a private constructor or a set
            // of required arguments
            eval('class '.$dummy_name.' extends '.$dummy_name.'Raw { public function __construct() {} }');
        }

        return $dummy_name;
    }

}
