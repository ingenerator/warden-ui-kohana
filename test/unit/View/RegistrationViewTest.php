<?php

declare(strict_types=1);

/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */
namespace test\unit\Ingenerator\Warden\UI\Kohana\View;


use Iterator;
use Ingenerator\KohanaView\ViewModel\NestedChildView;
use Ingenerator\Warden\UI\Kohana\Form\Fieldset;
use Ingenerator\Warden\UI\Kohana\View\RegistrationView;
use PHPUnit\Framework\Attributes\DataProvider;
use test\mock\View\DummyPageLayoutView;

final class RegistrationViewTest extends AbstractFormViewTestCase
{

    /**
     * @return Iterator<int, mixed[]>
     */
    public static function dataProvider_it_makes_email_readonly_if_confirmation_token_is_provided(): Iterator
    {
        yield ["122412323", "readonly"];
        yield ["", ""];
    }
    public function test_it_is_initialisable(): void
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf(RegistrationView::class, $subject);
        $this->assertInstanceOf(NestedChildView::class, $subject);
    }

    #[DataProvider('dataProvider_it_makes_email_readonly_if_confirmation_token_is_provided')]
    public function test_it_makes_email_readonly_if_confirmation_token_is_provided(string $token, string $expect_readonly): void
    {
        $subject = $this->newSubject();
        $subject->display(
            [
                'fields' => new Fieldset(
                    [
                        'email'                    => 'test@warden.test',
                        'email_confirmation_token' => $token,
                    ], []
                ),
            ]
        );
        $this->assertSame($expect_readonly, $subject->is_email_readonly);
    }

    protected function newSubject(): RegistrationView
    {
        return new RegistrationView(new DummyPageLayoutView);
    }

}
