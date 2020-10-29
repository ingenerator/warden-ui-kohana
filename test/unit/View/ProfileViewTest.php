<?php
/**
 * @author    Craig Gosman <craig@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Warden\UI\Kohana\View;


use Ingenerator\KohanaView\ViewModel\PageContentView;
use Ingenerator\Warden\UI\Kohana\View\ProfileView;
use Ingenerator\Warden\Core\Entity\SimpleUser;
use InvalidArgumentException;
use test\mock\ViewModel\PageLayout\DummyPageLayoutView;

class ProfileViewTest extends \PHPUnit\Framework\TestCase
{

    public function test_it_is_initialisable()
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf('Ingenerator\Warden\UI\Kohana\View\ProfileView', $subject);
        $this->assertInstanceOf(PageContentView::class, $subject);
    }

    public function test_it_throws_if_user_is_not_a_user()
    {
        $this->expectException(InvalidArgumentException::class);
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
        return new ProfileView(new DummyPageLayoutView);
    }

}
