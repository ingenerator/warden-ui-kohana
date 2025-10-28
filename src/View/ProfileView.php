<?php
/**
 * @author    Craig Gosman <craig@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\View;

use Override;
use Arr;
use Ingenerator\KohanaView\ViewModel\PageLayout\AbstractPageContentView;
use Ingenerator\Warden\Core\Entity\User;

class ProfileView extends AbstractPageContentView
{

    public protected(set) User $user;

    #[Override]
    protected function validateDisplayVariables(array $variables)
    {
        // @todo: This method has been removed from AbstractViewModel and will never be called
        $errors = parent::validateDisplayVariables($variables);
        if ( ! Arr::get($variables, 'user') instanceof User) {
            $errors[] = "'user' must be an instance of Ingenerator\Warden\Core\Entity\User";
        }

        return $errors;
    }

}
