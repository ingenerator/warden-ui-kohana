<?php

declare(strict_types=1);

/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */
namespace test\unit\Ingenerator\Warden\UI\Kohana\View;


use Ingenerator\KohanaView\ViewModel\NestedChildView;
use Ingenerator\Warden\Core\Entity\SimpleUser;
use Ingenerator\Warden\UI\Kohana\View\ChangeEmailView;
use InvalidArgumentException;
use test\mock\View\DummyPageLayoutView;

final class ChangeEmailViewTest extends AbstractFormViewTestCase
{

    public function test_it_is_initialisable(): void
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf(ChangeEmailView::class, $subject);
        $this->assertInstanceOf(NestedChildView::class, $subject);
    }

    public function test_it_throws_if_user_is_not_a_user(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->newSubjectDisplaying(['user' => 'string']);
    }

    public function test_it_exposes_user_variable(): void
    {
        $user = new SimpleUser();
        $subject = $this->newSubjectDisplaying(['user' => $user]);
        $this->assertSame($user, $subject->user);
    }

    protected function newSubject(): ChangeEmailView
    {
        return new ChangeEmailView(new DummyPageLayoutView);
    }

    protected function newSubjectDisplaying(array $custom_vars)
    {
        return parent::newSubjectDisplaying(\array_merge(['user' => new SimpleUser], $custom_vars));
    }


}
