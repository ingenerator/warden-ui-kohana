<?php

declare(strict_types=1);

/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */
namespace test\unit\Ingenerator\Warden\UI\Kohana\Routing;


use Iterator;
use URL;
use Arr;
use Route;
use Request;
use Ingenerator\Warden\Core\Support\UrlProvider;
use Ingenerator\Warden\UI\Kohana\Controller\LoginController;
use Ingenerator\Warden\UI\Kohana\Routing\WardenConfigBasedRouter;
use InvalidArgumentException;
use OutOfBoundsException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use test\mock\Entity\UserStub;

final class WardenConfigBasedRouterTest extends TestCase
{
    private array $config;

    public function test_it_is_initialisable(): void
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf(WardenConfigBasedRouter::class, $subject);
        $this->assertInstanceOf(UrlProvider::class, $subject);
    }

    public function test_it_throws_if_attempting_to_access_url_defined_as_null(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->config['after-logout']['url'] = NULL;
        $this->newSubject()->getAfterLogoutUrl();
    }

    public static function provider_config_url_map(): Iterator
    {
        yield ['after-login', 'getAfterLoginUrl', [UserStub::withId(5)], '/shiny-page'];
        yield ['after-logout', 'getAfterLogoutUrl', [], '/shiny-page'];
        yield ['after-verify-email', 'getAfterVerifyEmailSentUrl', [], '/shiny-page'];
        yield [
            'complete-password-reset',
            'getCompletePasswordResetUrl',
            [['email' => 'fop', 'token' => '123']],
            '/shiny-page?email=fop&token=123',
        ];
        yield [
            'complete-registration',
            'getCompleteRegistrationUrl',
            [['email' => 'fop', 'token' => '123']],
            '/shiny-page?email=fop&token=123',
        ];
        yield ['default-user-home', 'getDefaultUserHomeUrl', [UserStub::withId(5)], '/shiny-page'];
        yield ['login', 'getLoginUrl', [], '/shiny-page'];
        yield ['login', 'getLoginUrl', ['some@some.test'], '/shiny-page?email=some%40some.test'];
        yield ['logout', 'getLogoutUrl', [], '/shiny-page'];
        yield ['register-verify-email', 'getRegisterVerifyEmailUrl', [], '/shiny-page'];
        yield [
            'register-verify-email',
            'getRegisterVerifyEmailUrl',
            ['some@warden.test'],
            '/shiny-page?email=some%40warden.test',
        ];
    }


    #[DataProvider('provider_config_url_map')]
    public function test_it_generates_urls_based_on_config(string $key, string $method, array $args, string $expect): void
    {
        $this->config[$key]['url'] = '/shiny-page';
        $subject                   = $this->newSubject();
        $result                    = \call_user_func_array([$subject, $method], $args);
        $this->assertEquals(URL::site($expect), $result);
    }

    public static function provider_url_routing(): Iterator
    {
        yield [
            ['after-login' => ['url' => '/whole-new-controller', 'route_controller' => self::class]],
            ['/whole-new-controller' => '\\'.self::class],
        ];
        yield [
            [
                'after-login' => ['url' => '/whole-new-controller', 'route_controller' => FALSE],
                'login'       => ['url' => '/login', 'route_controller' => LoginController::class],
            ],
            [
                '/whole-new-controller' => FALSE,
                '/login'                => '\\'.LoginController::class,
            ],
        ];
        yield [
            [
                'after-login' => ['url' => '/whole-new-controller', 'route_controller' => FALSE],
                'login'       => ['url' => '/login', 'route_controller' => FALSE],
            ],
            [
                '/whole-new-controller' => FALSE,
                '/login'                => FALSE,
            ],
        ];
    }

    #[DataProvider('provider_url_routing')]
    public function test_it_defines_explicit_routes_for_urls_only_if_mapped_to_controllers(array $config, array $expect_routes): void
    {
        try {
            $old_routes   = RouteStateAccess::reset([]);
            $this->config = Arr::merge($this->config, $config);
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

    public function test_it_throws_if_routing_controller_with_no_url(): void
    {
        try {
            $old_routes                   = RouteStateAccess::reset([]);
            $this->config['after-logout'] = ['url' => FALSE, 'route_controller' => self::class];
            $this->expectException(InvalidArgumentException::class);
            $this->newSubject()->registerGlobalRoutes();
        } finally {
            RouteStateAccess::reset($old_routes);
        }
    }

    public function setUp(): void
    {
        parent::setUp();
        $config       = require(__DIR__.'/../../../config/warden.php');
        $this->config = $config['url_routing'];
    }

    protected function findRoutedController($url)
    {
        foreach (Route::all() as $route) {
            /** @var \Route $route */
            if ($params = $route->matches(Request::with(['uri' => $url]))) {
                return $params['controller'];
            }
        }

        return FALSE;
    }

    protected function newSubject(): WardenConfigBasedRouter
    {
        return new WardenConfigBasedRouter($this->config);
    }
}

class RouteStateAccess extends Route
{

    public static function reset($routes)
    {
        $old_routes      = Route::$_routes;
        Route::$_routes = $routes;

        return $old_routes;
    }
}
