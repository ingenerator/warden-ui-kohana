<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Warden\UI\Kohana\View;


use Ingenerator\KohanaView\ViewModel\PageContentView;
use Ingenerator\Warden\UI\Kohana\Form\Fieldset;
use Ingenerator\Warden\UI\Kohana\View\RegistrationView;
use test\mock\ViewModel\PageLayout\DummyPageLayoutView;

class RegistrationViewTest extends AbstractFormViewTest
{

    public function test_it_is_initialisable()
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf('Ingenerator\Warden\UI\Kohana\View\RegistrationView', $subject);
        $this->assertInstanceOf(PageContentView::class, $subject);
    }

    /**
     * @testWith ["122412323", "readonly"]
     *           ["", FALSE]
     */
    public function test_it_makes_email_readonly_if_confirmation_token_is_provided($token, $expect_readonly)
    {
        $subject = $this->newSubject();
        $subject->display(
            [
                'fields' => new Fieldset(
                    [
                        'email'                    => 'foo@bar.com',
                        'email_confirmation_token' => $token,
                    ], []
                ),
            ]
        );
        $this->assertSame($expect_readonly, $subject->is_email_readonly);
    }

    protected function newSubject()
    {
        return new RegistrationView(new DummyPageLayoutView);
    }

}
