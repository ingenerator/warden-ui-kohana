<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Warden\UI\Kohana\View;


use Ingenerator\KohanaView\ViewModel\AbstractViewModel;
use Ingenerator\Warden\UI\Kohana\Form\Fieldset;

abstract class AbstractFormViewTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_it_throws_if_fields_is_not_a_fieldset()
    {
        $this->newSubjectDisplaying(['fields' => 'random stuff']);
    }

    public function test_it_exposes_fields_variable()
    {
        $fields  = new Fieldset([], []);
        $subject = $this->newSubjectDisplaying(['fields' => $fields]);
        $this->assertSame($fields, $subject->fields);
    }

    /**
     * @return AbstractViewModel
     */
    abstract protected function newSubject();

    /**
     * @param array $custom_vars
     *
     * @return AbstractViewModel
     */
    protected function newSubjectDisplaying(array $custom_vars)
    {
        $vars    = \array_merge(['fields' => new Fieldset([], [])], $custom_vars);
        $subject = $this->newSubject();
        $subject->display($vars);
        return $subject;
    }

}
