<?php


namespace test\unit\Ingenerator\Warden\UI\Kohana\Logging;

use Ingenerator\KohanaExtras\DependencyContainer\DependencyContainer;
use Ingenerator\PHPUtils\Logging\LogMetadataProvider;
use Ingenerator\PHPUtils\Object\SingletonNotInitialisedException;
use Ingenerator\Warden\Core\UserSession\SimplePropertyUserSession;
use Ingenerator\Warden\Core\UserSession\UserSession;
use Ingenerator\Warden\UI\Kohana\Logging\WardenUserLogMetadataProvider;
use PHPUnit\Framework\TestCase;
use test\mock\Ingenerator\Warden\Core\Entity\UserStub;

class WardenUserLogMetadataProviderTest extends TestCase
{

    /**
     * @testWith []
     *           ["some invalid class"]
     *           ["Dependencies", "any.old.key"]
     */
    public function test_it_is_initialisable_with_any_args(...$args)
    {
        $subject = $this->newSubject(...$args);
        $this->assertInstanceOf(WardenUserLogMetadataProvider::class, $subject);
        $this->assertInstanceOf(LogMetadataProvider::class, $subject);
    }

    public function test_it_captures_error_if_dependencies_class_does_not_exist()
    {
        $class = \uniqid('deps-');
        $this->assertSame(
            ['context' => ['user' => '#ERR# No class '.$class]],
            $this->newSubject($class)->getMetadata()
        );
    }

    public function test_it_captures_error_if_dependencies_not_initialised()
    {
        $class = new class extends DependencyContainer {
            public function __construct() { }

            public static function instance()
            {
                throw new SingletonNotInitialisedException('Not initialised');
            }
        };

        $this->assertSame(
            ['context' => ['user' => '#ERR# '.\get_class($class).' Not initialised']],
            $this->newSubject(get_class($class))->getMetadata()
        );
    }

    public function test_it_bubbles_exception_if_dependencies_initialised_but_dep_does_not_exist()
    {
        $class   = $this->fakeDependencyContainerClass([]);
        $subject = $this->newSubject($class);
        $this->expectException(\Dependency_Exception::class);
        $subject->getMetadata();
    }

    public function test_it_bubbles_exception_if_user_session_dep_is_not_user_session()
    {
        $class   = $this->fakeDependencyContainerClass(['any.old.key' => new \stdClass]);
        $subject = $this->newSubject($class, 'any.old.key');
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(UserSession::class);
        $subject->getMetadata();
    }

    public function provider_can_access_session()
    {
        return [
            [['session_status' => PHP_SESSION_NONE, 'headers_sent' => FALSE], TRUE],
            [['session_status' => PHP_SESSION_NONE, 'headers_sent' => TRUE], FALSE],
            [['session_status' => PHP_SESSION_DISABLED, 'headers_sent' => FALSE], FALSE],
            [['session_status' => PHP_SESSION_DISABLED, 'headers_sent' => TRUE], FALSE],
            [['session_status' => PHP_SESSION_ACTIVE, 'headers_sent' => FALSE], TRUE],
            [['session_status' => PHP_SESSION_ACTIVE, 'headers_sent' => TRUE], TRUE],
        ];
    }

    /**
     * @dataProvider provider_can_access_session
     */
    public function test_it_only_attempts_to_access_warden_if_session_started_or_headers_not_sent(
        $state,
        $expect_access
    ) {
        $class = $this->fakeDependencyContainerClass(
            [
                'any.old.key' => $expect_access ? new SimplePropertyUserSession : NULL,
            ]
        );

        $subject                = $this->newSubject($class, 'any.old.key');
        $subject->session_state = $state;

        $this->assertSame(
            ['context' => ['user' => $expect_access ? 'guest' : '#SESSION-INACCESSIBLE#']],
            $subject->getMetadata()
        );
    }

    public function test_it_returns_guest_if_user_not_authenticated()
    {
        $class = $this->fakeDependencyContainerClass(
            [
                'any.old.key' => new SimplePropertyUserSession,
            ]
        );

        $this->assertSame(
            ['context' => ['user' => 'guest']],
            $this->newSubject($class, 'any.old.key')->getMetadata()
        );
    }

    public function test_it_returns_email_address_if_user_authenticated()
    {
        $session = new SimplePropertyUserSession;
        $session->login(UserStub::fromArray(['email' => 'john.doe@ingenerator.com']));
        $class = $this->fakeDependencyContainerClass(
            [
                'any.old.key' => $session,
            ]
        );

        $this->assertSame(
            ['context' => ['user' => 'john.doe@ingenerator.com']],
            $this->newSubject($class, 'any.old.key')->getMetadata()
        );
    }

    public function test_it_loads_session_from_default_dependency_name_by_default()
    {
        $class = $this->fakeDependencyContainerClass(
            [
                'warden.user_session.session' => new SimplePropertyUserSession,
            ]
        );

        $this->assertSame(
            ['context' => ['user' => 'guest']],
            $this->newSubject($class)->getMetadata()
        );
    }

    protected function fakeDependencyContainerClass(array $services): string
    {
        // @todo: should probably ship a standard mock in kohana-extras
        $class = new class($services) extends DependencyContainer {

            protected static $this_instance;

            public function __construct(array $services)
            {
                parent::__construct([]);
                $this->_cache          = $services;
                static::$this_instance = $this;
            }

            public static function instance()
            {
                return static::$this_instance;
            }
        };

        return \get_class($class);
    }

    /**
     * @param string $dependencies_class
     * @param string $session_dependency_key
     *
     * @return SessionStateStubbedLogMetadataProvider
     */
    protected function newSubject(...$args): SessionStateStubbedLogMetadataProvider
    {
        return new SessionStateStubbedLogMetadataProvider(...$args);
    }


}

class SessionStateStubbedLogMetadataProvider extends WardenUserLogMetadataProvider
{
    public $session_state = ['session_status' => PHP_SESSION_ACTIVE, 'headers_sent' => FALSE];

    protected function sessionStatus(): int
    {
        return $this->session_state['session_status'];
    }

    protected function headersSent(): bool
    {
        return $this->session_state['headers_sent'];
    }

}
