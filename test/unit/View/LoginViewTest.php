<?php

declare(strict_types=1);

/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */
namespace test\unit\Ingenerator\Warden\UI\Kohana\View;


use Ingenerator\KohanaView\ViewModel\NestedChildView;
use Ingenerator\Warden\Core\Support\FixedUrlProviderStub;
use Ingenerator\Warden\UI\Kohana\View\LoginView;
use test\mock\View\DummyPageLayoutView;

final class LoginViewTest extends AbstractFormViewTestCase
{

    public function test_it_is_initialisable(): void
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf(LoginView::class, $subject);
        $this->assertInstanceOf(NestedChildView::class, $subject);
    }
    
    protected function newSubject(): LoginView
    {
        return new LoginView(new DummyPageLayoutView, new FixedUrlProviderStub());
    }

}
