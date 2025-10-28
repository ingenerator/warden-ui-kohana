<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\View;


use Ingenerator\KohanaView\ViewModel\PageLayout\AbstractPageContentView;
use Ingenerator\Warden\UI\Kohana\Form\Fieldset;

class AbstractFormView extends AbstractPageContentView
{
    public protected(set) Fieldset $fields;

}
