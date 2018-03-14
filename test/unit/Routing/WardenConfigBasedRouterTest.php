<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Warden\UI\Kohana\Routing;


use Ingenerator\Warden\Core\Support\UrlProvider;
use Ingenerator\Warden\UI\Kohana\Controller\LoginController;
use Ingenerator\Warden\UI\Kohana\Routing\WardenConfigBasedRouter;
use test\mock\Ingenerator\Warden\Core\Entity\UserStub;

class WardenConfigBasedRouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $config;

    public function test_it_is_initialisable()
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf(WardenConfigBasedRouter::class, $subject);
        $this->assertInstanceOf(UrlProvider::class, $subject);
    }


    /**
     * @expectedException \OutOfBoundsException
     */
    public function test_it_throws_if_attempting_to_access_url_defined_as_null()
    {
        $this->config['after-logout']['url'] = NULL;
        $this->newSubject()->getAfterLogoutUrl();
    }

    public function provider_config_url_map()
    {
        return [
            ['after-login', 'getAfterLoginUrl', [UserStub::withId(5)], '/shiny-page'],
            ['after-logout', 'getAfterLogoutUrl', [], '/shiny-page'],
            ['after-verify-email', 'getAfterVerifyEmailSentUrl', [], '/shiny-page'],
            [
                'complete-password-reset',
                'getCompletePasswordResetUrl',
                [['email' => 'fop', 'token' => '123']],
                '/shiny-page?email=fop&token=123',
            ],
            [
                'complete-registration',
                'getCompleteRegistrationUrl',
                [['email' => 'fop', 'token' => '123']],
                '/shiny-page?email=fop&token=123',
            ],
            ['default-user-home', 'getDefaultUserHomeUrl', [UserStub::withId(5)], '/shiny-page'],
            ['login', 'getLoginUrl', [], '/shiny-page'],
            ['login', 'getLoginUrl', ['some@some.net'], '/shiny-page?email=some%40some.net'],
            ['logout', 'getLogoutUrl', [], '/shiny-page'],
            ['register-verify-email', 'getRegisterVerifyEmailUrl', [], '/shiny-page'],
            [
                'register-verify-email',
                'getRegisterVerifyEmailUrl',
                ['some@some.net'],
                '/shiny-page?email=some%40some.net',
            ],
        ];
    }


    /**
     * @dataProvider provider_config_url_map
     */
    public function test_it_generates_urls_based_on_config($key, $method, $args, $expect)
    {
        $this->config[$key]['url'] = '/shiny-page';
        $subject                   = $this->newSubject();
        $result                    = call_user_func_array([$subject, $method], $args);
        $this->assertEquals(\URL::site($expect), $result);
    }

    public function provider_url_routing()
    {
        return [
            [
                ['after-login' => ['url' => '/whole-new-controller', 'route_controller' => static::class]],
                ['/whole-new-controller' => '\\'.static::class],
            ],
            [
                [
                    'after-login' => ['url' => '/whole-new-controller', 'route_controller' => FALSE],
                    'login'       => ['url' => '/login', 'route_controller' => LoginController::class],
                ],
                [
                    '/whole-new-controller' => FALSE,
                    '/login'                => '\\'.LoginController::class,
                ],
            ],
            [
                [
                    'after-login' => ['url' => '/whole-new-controller', 'route_controller' => FALSE],
                    'login'       => ['url' => '/login', 'route_controller' => FALSE],
                ],
                [
                    '/whole-new-controller' => FALSE,
                    '/login'                => FALSE,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provider_url_routing
     */
    public function test_it_defines_explicit_routes_for_urls_only_if_mapped_to_controllers($config, $expect_routes)
    {
        try {
            $old_routes   = RouteStateAccess::reset([]);
            $this->config = \Arr::merge($this->config, $config);
            $this->newSubject()->registerGlobalRoutes();
            $actual = [];
            foreach ($expect_routes as $url => $expect_controller) {
                $actual[$url] = $this->findRoutedController($url);
            }
            $this->assertEquals($expect_routes, $actual);
        } finally {
            RouteStateAccess::reset($old_routes);
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_it_throws_if_routing_controller_with_no_url()
    {
        try {
            $old_routes                   = RouteStateAccess::reset([]);
            $this->config['after-logout'] = ['url' => FALSE, 'route_controller' => static::class];
            $this->newSubject()->registerGlobalRoutes();
        } finally {
            RouteStateAccess::reset($old_routes);
        }
    }

    public function setUp()
    {
        parent::setUp();
        $config       = require(__DIR__.'/../../../config/warden.php');
        $this->config = $config['url_routing'];
    }

    protected function findRoutedController($url)
    {
        foreach (\Route::all() as $route) {
            /** @var \Route $route */
            if ($params = $route->matches(\Request::with(['uri' => $url]))) {
                return $params['controller'];
            }
        }

        return FALSE;
    }

    protected function newSubject()
    {
        return new WardenConfigBasedRouter($this->config);
    }
}

class RouteStateAccess extends \Route
{

    public static function reset($routes)
    {
        $old_routes      = \Route::$_routes;
        \Route::$_routes = $routes;

        return $old_routes;
    }
}
