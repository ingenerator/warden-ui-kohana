<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Warden\UI\Kohana\DependencyFactory;


use Doctrine\ORM\EntityManager;
use Ingenerator\KohanaExtras\DependencyContainer\DependencyContainer;
use Ingenerator\KohanaExtras\Message\KohanaMessageProvider;
use Ingenerator\KohanaView\ViewModel\PageLayoutView;
use Ingenerator\Tokenista;
use Ingenerator\Warden\UI\Kohana\Controller\LoginController;
use Ingenerator\Warden\UI\Kohana\Controller\RegisterController;
use Ingenerator\Warden\UI\Kohana\DependencyFactory\WardenKohanaDependencyFactory;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WardenKohanaDependencyFactoryTest extends \PHPUnit\Framework\TestCase
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
                            'kohana.psr_log'          => \PSR\Log\LoggerInterface::class,
                            'kohana.session'          => \Session::class,
                            'swiftmailer.mailer'      => \Swift_Mailer::class,
                            'tokenista.tokenista'     => Tokenista::class,
                            'validation.validator'    => ValidatorInterface::class,
                            'view.layout.default'     => PageLayoutView::class,
                        ]
                    ),
                ],
            ]
        );

        $this->assertNotNull($container->get($service_name));
    }

    public function provider_controller_subsets()
    {
        return [
            [
                NULL,
                [LoginController::class => TRUE, RegisterController::class => TRUE],
            ],
            [
                [],
                [LoginController::class => FALSE, RegisterController::class => FALSE],
            ],
            [
                [LoginController::class],
                [LoginController::class => TRUE, RegisterController::class => FALSE],
            ],
            [
                [LoginController::class, RegisterController::class],
                [LoginController::class => TRUE, RegisterController::class => TRUE],
            ],
        ];
    }

    /**
     * @dataProvider  provider_controller_subsets
     */
    public function test_it_optionally_allows_to_define_a_subset_of_controllers($args, $expect_has)
    {
        $definitions = WardenKohanaDependencyFactory::controllerDefinitions($args);

        $actual_has = [];
        foreach (\array_keys($expect_has) as $controller) {
            $actual_has[$controller] = isset($definitions['controller']['\\'.$controller]['_settings']);
        }

        $this->assertEquals($expect_has, $actual_has);
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
