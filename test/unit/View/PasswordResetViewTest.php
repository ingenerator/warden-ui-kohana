<?php

declare(strict_types=1);

/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */
namespace test\unit\Ingenerator\Warden\UI\Kohana\View;


use Ingenerator\KohanaView\ViewModel\NestedChildView;
use Ingenerator\Warden\UI\Kohana\View\PasswordResetView;
use test\mock\View\DummyPageLayoutView;

final class PasswordResetViewTest extends AbstractFormViewTestCase
{

    public function test_it_is_initialisable(): void
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf(PasswordResetView::class, $subject);
        $this->assertInstanceOf(NestedChildView::class, $subject);
    }

    protected function newSubject(): PasswordResetView
    {
        return new PasswordResetView(new DummyPageLayoutView);
    }

}
