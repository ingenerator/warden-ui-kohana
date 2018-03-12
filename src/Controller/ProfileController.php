<?php
/**
 * @author    Craig Gosman <craig@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;


use Ingenerator\Warden\UI\Kohana\View\ProfileView;

class ProfileController extends WardenBaseController
{
    public function before()
    {
        parent::before();
        if ( ! $this->getUserSession()->isAuthenticated()) {
            $this->redirect('/login');
        }
    }

    public function action_get()
    {
        /** @var ProfileView $view */
        $view = $this->getService('warden.view.profile.profile');
        $view->display(['user' => $this->getUserSession()->getUser()]);

        $this->respondPageContent($view);
    }
}
