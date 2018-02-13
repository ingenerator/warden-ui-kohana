<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Warden\UI\Kohana\View;


use Ingenerator\KohanaView\ViewModel\AbstractViewModel;
use Ingenerator\Warden\UI\Kohana\Form\Fieldset;

abstract class AbstractFormViewTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_it_throws_if_fields_is_not_a_fieldset()
    {
        $this->newSubject()->display(['fields' => 'random stuff']);
    }

    public function test_it_exposes_fields_variable()
    {
        $fields = new Fieldset([], []);
        $subject = $this->newSubject();
        $subject->display(['fields' => $fields]);
        $this->assertSame($fields, $subject->fields);
    }

    /**
     * @return AbstractViewModel
     */
    abstract protected function newSubject();

}
