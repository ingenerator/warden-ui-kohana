<?php
/**
 * @author    Craig Gosman <craig@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;


use Ingenerator\Warden\UI\Kohana\View\ProfileView;

class Profile extends WardenBaseController
{
    public function action_profile()
    {
        if ( ! $this->getUserSession()->isAuthenticated()) {
            $this->redirect('/login');
        }

        /** @var ProfileView $view */
        $view = $this->getService('warden.view.profile.profile');
        $view->display(['user' => $this->getUserSession()->getUser()]);

        $this->respondPageContent($view);
    }
}
