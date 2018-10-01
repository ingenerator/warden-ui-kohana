<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\View;

use Ingenerator\Warden\Core\Entity\User;

/**
 * @property-read \Ingenerator\Warden\Core\Entity\User $user
 */
class ChangeEmailView extends AbstractFormView
{
    protected $variables = [
        'user' => NULL,
    ];

    protected function validateDisplayVariables(array $variables)
    {
        $errors = parent::validateDisplayVariables($variables);
        if ( ! \Arr::get($variables, 'user') instanceof User) {
            $errors[] = "'user' must be an instance of Ingenerator\Warden\Core\Entity\User";
        }

        return $errors;
    }


}
