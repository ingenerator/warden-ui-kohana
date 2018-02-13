<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\View;



/**
 * @property-read string $is_email_readonly
 */
class RegistrationView extends AbstractFormView
{

    protected function var_is_email_readonly()
    {
        return $this->fields['email_confirmation_token']['value'] ? 'readonly' : FALSE;
    }
}
