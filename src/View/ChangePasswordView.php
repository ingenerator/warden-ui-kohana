<?php

declare(strict_types=1);

/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */
namespace Ingenerator\Warden\UI\Kohana\View;

use Ingenerator\Warden\Core\Entity\User;

class ChangePasswordView extends AbstractFormView
{
    public protected(set) User $user;

}
