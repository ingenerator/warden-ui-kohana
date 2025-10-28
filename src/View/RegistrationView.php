<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\View;



class RegistrationView extends AbstractFormView
{

    public string $is_email_readonly {
        get => $this->fields['email_confirmation_token']['value'] ? 'readonly' : FALSE;
    }
}
