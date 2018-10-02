<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Warden\UI\Kohana\UserSession;


use Ingenerator\Warden\Core\Repository\ArrayUserRepository;
use Ingenerator\Warden\Core\Repository\UnknownUserException;
use Ingenerator\Warden\Core\Repository\UserRepository;
use Ingenerator\Warden\UI\Kohana\Entity\LastLoginTrackingUser;
use Ingenerator\Warden\UI\Kohana\UserSession\KohanaUserSession;
use test\mock\Ingenerator\Warden\Core\Entity\UserStub;
use test\unit\Ingenerator\Warden\Core\UserSession\UserSessionTest;

class KohanaUserSessionTest extends UserSessionTest
{
    /**
     * @var \Session_Array
     */
    protected $session_driver;

    /**
     * @var UserRepository
     */
    protected $user_repository;

    public function test_it_is_initialisable()
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf('Ingenerator\Warden\UI\Kohana\UserSession\KohanaUserSession', $subject);
        $this->assertInstanceOf('Ingenerator\Warden\Core\UserSession\UserSession', $subject);
    }

    public function test_it_stores_user_id_on_login()
    {
        $this->newSubject()->login(UserStub::withId(152));
        $this->assertSame(152, $this->session_driver->get('user_id'));
    }

    public function test_it_bumps_and_persists_last_login_for_login_tracking_user()
    {
        $user = LoginTrackingUserStub::fromArray(['id' => 152, 'login_count' => 15]);

        $this->user_repository = $this->getMockBuilder(UserRepository::class)->getMock();
        $this->user_repository->expects($this->once())->method('save')->with($user);
        $this->newSubject()->login($user);
        $this->assertEquals(new \DateTimeImmutable, $user->getLastLogin(), 'Should have login', 1);
        $this->assertSame(16, $user->getLoginCount(), 'Should be called once');
    }

    public function test_it_clears_user_id_on_logout()
    {
        $this->session_driver->set('user_id', 159);
        $this->newSubject()->logout();
        $this->assertNull($this->session_driver->get('user_id'));
    }

    public function test_it_regenerates_session_id_on_login()
    {
        $old_sid = $this->session_driver->id();
        $this->newSubject()->login(UserStub::withId(159));
        $new_sid = $this->session_driver->id();
        $this->assertNotEquals($old_sid, $new_sid);
    }

    public function test_it_regenerates_session_id_on_logout()
    {
        $old_sid = $this->session_driver->id();
        $this->newSubject()->login(UserStub::withId(159));
        $new_sid = $this->session_driver->id();
        $this->assertNotEquals($old_sid, $new_sid);
    }

    public function test_it_is_authenticated_when_user_id_is_set_in_session()
    {
        $this->session_driver->set('user_id', 198);
        $this->assertTrue($this->newSubject()->isAuthenticated());
    }

    public function test_it_loads_and_returns_user_by_id_when_required()
    {
        $this->user_repository = new ArrayUserRepository;
        $user                  = UserStub::withId(158);
        $this->user_repository->save($user);
        $this->session_driver->set('user_id', 158);
        $this->assertSame($user, $this->newSubject()->getUser());
    }

    public function test_it_clears_session_and_throws_when_user_with_user_id_cannot_be_loaded()
    {
        $this->user_repository = new ArrayUserRepository;
        $this->session_driver->set('user_id', 1905);

        try {
            $this->newSubject()->getUser();
            $this->fail('Expected exception, none got');
        } catch (UnknownUserException $e) {
            $this->assertNull($this->session_driver->get('user_id'), 'Session user ID should be cleared');
        }
    }

    public function setUp()
    {
        parent::setUp();
        $this->session_driver  = new \Session_Array;
        $this->user_repository = $this->getMockBuilder(UserRepository::class)->getMock();
        $this->user_repository->expects($this->never())->method($this->anything());
    }

    protected function newSubject()
    {
        return new KohanaUserSession(
            $this->session_driver,
            $this->user_repository
        );
    }

}

class LoginTrackingUserStub extends UserStub implements LastLoginTrackingUser
{
    protected $last_login;
    protected $login_count;

    public function markLoggedInAt(\DateTimeImmutable $now)
    {
        $this->last_login = $now;
        $this->login_count++;
    }

    public function getLastLogin()
    {
        return $this->last_login;
    }

    public function getLoginCount()
    {
        return $this->login_count;
    }

}
