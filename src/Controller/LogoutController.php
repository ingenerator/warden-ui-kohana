<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;


use Ingenerator\Warden\UI\Kohana\Controller\WardenBaseController;
use Ingenerator\Warden\UI\Kohana\Message\Authentication\LogoutSuccessMessage;

class LogoutController extends WardenBaseController
{
    public function action_get()
    {
        if ($this->getUserSession()->isAuthenticated()) {
            $this->getUserSession()->logout();
            $this->getPigeonhole()->add(new LogoutSuccessMessage);
        }
        //@todo: Return url after logout should be customisable
        $this->redirect('/login');
    }
}
