<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\View;


use Ingenerator\KohanaView\ViewModel\NestedParentView;
use Override;
use Arr;
use Ingenerator\KohanaView\ViewModel\PageLayout\AbstractPageContentView;
use Ingenerator\Warden\UI\Kohana\Form\Fieldset;

class AbstractFormView extends AbstractPageContentView
{
    public protected(set) Fieldset $fields;
    public function __construct(NestedParentView $page)
    {
        $this->fields = NULL;
        parent::__construct($page);
    }
    
    #[Override]
    protected function validateDisplayVariables(array $variables)
    {
        // @todo: This method has been removed from AbstractViewModel and will never be called
        $errors = parent::validateDisplayVariables($variables);
        if ( ! Arr::get($variables, 'fields') instanceof Fieldset) {
            $errors[] = "'fields' must be an instance of Fieldset";
        }

        return $errors;
    }

}
