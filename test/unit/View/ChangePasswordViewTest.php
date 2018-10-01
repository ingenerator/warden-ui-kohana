<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Warden\UI\Kohana\View;


use Ingenerator\KohanaView\ViewModel\PageContentView;
use Ingenerator\Warden\Core\Entity\SimpleUser;
use Ingenerator\Warden\UI\Kohana\View\ChangeEmailView;
use Ingenerator\Warden\UI\Kohana\View\ChangePasswordView;
use Ingenerator\Warden\UI\Kohana\View\EmailVerificationView;
use test\mock\ViewModel\PageLayout\DummyPageLayoutView;

class ChangePasswordViewTest extends AbstractFormViewTest
{

    public function test_it_is_initialisable()
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf(ChangePasswordView::class, $subject);
        $this->assertInstanceOf(PageContentView::class, $subject);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_it_throws_if_user_is_not_a_user()
    {
        $this->newSubject()->display(['user' => 'string']);
    }

    public function test_it_exposes_user_variable()
    {
        $user = new SimpleUser();
        $subject = $this->newSubject();
        $subject->display(['user' => $user]);
        $this->assertSame($user, $subject->user);
    }

    protected function newSubject()
    {
        return new ChangePasswordView(new DummyPageLayoutView);
    }

}
