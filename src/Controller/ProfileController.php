<?php
/**
 * @author    Craig Gosman <craig@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;


use Ingenerator\Warden\Core\Support\InteractorRequestFactory;
use Ingenerator\Warden\Core\UserSession\UserSession;
use Ingenerator\Warden\UI\Kohana\View\ProfileView;

class ProfileController extends WardenBaseController
{

    /**
     * @var ProfileView
     */
    protected $profile_view;

    /**
     * @var UserSession
     */
    protected $session;

    public function __construct(InteractorRequestFactory $rq_factory, ProfileView $profile_view, UserSession $session)
    {
        parent::__construct($rq_factory);
        $this->profile_view = $profile_view;
        $this->session      = $session;
    }

    public function before()
    {
        parent::before();
        if ( ! $this->session->isAuthenticated()) {
            $this->redirect('/login');
        }
    }

    public function action_get()
    {
        $this->profile_view->display(['user' => $this->session->getUser()]);

        $this->respondPageContent($this->profile_view);
    }
}
