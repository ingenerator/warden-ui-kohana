<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Warden\UI\Kohana\View;


use Ingenerator\KohanaView\ViewModel\PageContentView;
use Ingenerator\Warden\UI\Kohana\View\LoginView;
use test\mock\ViewModel\PageLayout\DummyPageLayoutView;

class LoginViewTest extends AbstractFormViewTest
{

    public function test_it_is_initialisable()
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf('Ingenerator\Warden\UI\Kohana\View\LoginView', $subject);
        $this->assertInstanceOf(PageContentView::class, $subject);
    }
    
    protected function newSubject()
    {
        return new LoginView(new DummyPageLayoutView);
    }

}
