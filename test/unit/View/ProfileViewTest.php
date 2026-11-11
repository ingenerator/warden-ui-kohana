<?php

declare(strict_types=1);

/**
 * @author    Craig Gosman <craig@ingenerator.com>
 * @licence   proprietary
 */
namespace test\unit\Ingenerator\Warden\UI\Kohana\View;


use PHPUnit\Framework\TestCase;
use Ingenerator\KohanaView\ViewModel\NestedChildView;
use Ingenerator\Warden\Core\Entity\SimpleUser;
use Ingenerator\Warden\UI\Kohana\View\ProfileView;
use InvalidArgumentException;
use test\mock\View\DummyPageLayoutView;

final class ProfileViewTest extends TestCase
{

    public function test_it_is_initialisable(): void
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf(ProfileView::class, $subject);
        $this->assertInstanceOf(NestedChildView::class, $subject);
    }

    public function test_it_throws_if_user_is_not_a_user(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->newSubject()->display(['user' => 'string']);
    }

    public function test_it_exposes_user_variable(): void
    {
        $user = new SimpleUser();
        $subject = $this->newSubject();
        $subject->display(['user' => $user]);
        $this->assertSame($user, $subject->user);
    }

    protected function newSubject(): ProfileView
    {
        return new ProfileView(new DummyPageLayoutView);
    }

}
