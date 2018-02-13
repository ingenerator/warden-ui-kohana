<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\View;


use Ingenerator\KohanaView\ViewModel\PageLayout\AbstractPageContentView;
use Ingenerator\KohanaView\ViewModel\PageLayoutView;
use Ingenerator\Warden\UI\Kohana\Form\Fieldset;

/**
 * @property-read Fieldset $fields
 */
class AbstractFormView extends AbstractPageContentView
{
    public function __construct(PageLayoutView $page)
    {
        $this->variables['fields'] = NULL;
        parent::__construct($page);
    }
    
    protected function validateDisplayVariables(array $variables)
    {
        $errors = parent::validateDisplayVariables($variables);
        if ( ! \Arr::get($variables, 'fields') instanceof Fieldset) {
            $errors[] = "'fields' must be an instance of Fieldset";
        }

        return $errors;
    }

}
