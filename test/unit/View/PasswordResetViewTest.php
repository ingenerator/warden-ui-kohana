<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Warden\UI\Kohana\View;


use Ingenerator\KohanaView\ViewModel\NestedChildView;
use Ingenerator\Warden\UI\Kohana\View\PasswordResetView;
use test\mock\View\DummyPageLayoutView;

class PasswordResetViewTest extends AbstractFormViewTest
{

    public function test_it_is_initialisable()
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf('Ingenerator\Warden\UI\Kohana\View\PasswordResetView', $subject);
        $this->assertInstanceOf(NestedChildView::class, $subject);
    }

    protected function newSubject()
    {
        return new PasswordResetView(new DummyPageLayoutView);
    }

}
