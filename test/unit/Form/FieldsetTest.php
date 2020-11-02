<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Warden\UI\Kohana\Form;


use BadMethodCallException;
use Ingenerator\Warden\UI\Kohana\Form\Fieldset;
use PHPUnit\Framework\TestCase;

class FieldsetTest extends TestCase
{

    public function test_it_is_initialisable_array_access()
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf('Ingenerator\Warden\UI\Kohana\Form\Fieldset', $subject);
        $this->assertInstanceOf('ArrayAccess', $subject);
    }

    public function test_it_throws_on_unset()
    {
        $this->expectException(BadMethodCallException::class);
        unset($this->newSubject()['field']);
    }

    public function test_it_throws_on_set()
    {
        $this->expectException(BadMethodCallException::class);
        $this->newSubject()['field'] = 'foo';
    }

    public function test_any_offest_exists()
    {
        $this->assertTrue(isset($this->newSubject()['anything']));
    }

    public function test_it_returns_empty_value_for_unknown_field()
    {
        $this->assertSame(NULL, $this->newSubject()['field']['value']);
    }

    public function test_it_returns_empty_validation_class_for_unknown_field()
    {
        $this->assertSame(NULL, $this->newSubject()['field']['validation_class']);
    }

    public function test_it_returns_empty_error_message_for_unknown_field()
    {
        $this->assertSame(NULL, $this->newSubject()['field']['errors']);
    }

    public function test_it_returns_value_for_field_with_value()
    {
        $subject = $this->newSubject(['name' => 'Mr Jones']);
        $this->assertSame('Mr Jones', $subject['name']['value']);
    }

    public function test_it_returns_errors_for_field_with_errors()
    {
        $subject = $this->newSubject([], ['email' => 'No, that\'s not an email']);
        $this->assertSame('No, that\'s not an email', $subject['email']['errors']);
    }

    public function test_it_returns_error_validation_class_when_field_has_error()
    {
        $subject = $this->newSubject([], ['email' => 'That is a phone number']);
        $this->assertSame('has-error', $subject['email']['validation_class']);
    }

    public function test_it_returns_success_validation_class_when_field_has_no_error_and_other_fields_have_errors()
    {
        $subject = $this->newSubject([], ['email' => 'You are bad at forms']);
        $this->assertSame('has-success', $subject['name']['validation_class']);
    }

    protected function newSubject(array $values = [], array $errors = [])
    {
        return new Fieldset($values, $errors);
    }

}
