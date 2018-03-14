<?php
/**
 * @author    Craig Gosman <craig@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;


use Ingenerator\Warden\Core\Support\InteractorRequestFactory;
use Ingenerator\Warden\Core\Support\UrlProvider;
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

    /**
     * @var UrlProvider
     */
    protected $urls;

    public function __construct(
        InteractorRequestFactory $rq_factory,
        UrlProvider $urls,
        ProfileView $profile_view,
        UserSession $session
    ) {
        parent::__construct($rq_factory);
        $this->profile_view = $profile_view;
        $this->session      = $session;
        $this->urls         = $urls;
    }

    public function before()
    {
        parent::before();
        if ( ! $this->session->isAuthenticated()) {
            $this->redirect($this->urls->getLoginUrl());
        }
    }

    public function action_get()
    {
        $this->profile_view->display(['user' => $this->session->getUser()]);

        $this->respondPageContent($this->profile_view);
    }
}
