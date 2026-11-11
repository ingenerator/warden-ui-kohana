<?php

declare(strict_types=1);

/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */
namespace test\unit\Ingenerator\Warden\UI\Kohana\UserSession;

use BadMethodCallException;
use Ingenerator\Warden\Core\Entity\SimpleUser;
use Ingenerator\Warden\Core\UserSession\UserSession;
use PHPUnit\Framework\TestCase;
use test\mock\Entity\UserStub;

abstract class UserSessionTestCase extends TestCase
{

    public function test_it_is_initialisable(): void
    {
        $this->assertInstanceOf(UserSession::class, $this->newSubject());
    }

    public function test_it_is_not_authenticated_by_default(): void
    {
        $this->assertFalse($this->newSubject()->isAuthenticated());
    }

    public function test_it_throws_when_attempting_to_access_user_if_not_authenticated(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->newSubject()->getUser();
    }

    public function test_it_is_authenticated_after_login(): void
    {
        $subject = $this->newSubject();
        $user    = UserStub::withId(198);
        $subject->login($user);
        $this->assertTrue($subject->isAuthenticated());
        $this->assertSame($user, $subject->getUser());
    }

    public function test_it_is_not_authenticated_after_logout(): void
    {
        $subject = $this->newSubject();
        $subject->login(new SimpleUser);
        $subject->logout();
        $this->assertFalse($subject->isAuthenticated());
    }

    /**
     * @return UserSession
     */
    abstract protected function newSubject();

}
