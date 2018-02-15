<?php
/**
 * @author    Craig Gosman <craig@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\View;

use Ingenerator\KohanaView\ViewModel\PageLayout\AbstractPageContentView;
use Ingenerator\Warden\Core\Entity\User;

/**
 * @property-read Ingenerator\Warden\Core\Entity\User $user
 */
class ProfileView extends AbstractPageContentView
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
