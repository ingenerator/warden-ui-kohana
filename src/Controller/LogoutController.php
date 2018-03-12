<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;


use Ingenerator\Warden\Core\Support\InteractorRequestFactory;
use Ingenerator\Warden\Core\UserSession\UserSession;
use Ingenerator\Warden\UI\Kohana\Message\Authentication\LogoutSuccessMessage;

class LogoutController extends WardenBaseController
{

    /**
     * @var UserSession
     */
    protected $session;

    public function __construct(InteractorRequestFactory $rq_factory, UserSession $session)
    {
        parent::__construct($rq_factory);
        $this->session = $session;
    }

    public function action_get()
    {
        if ($this->session->isAuthenticated()) {
            $this->session->logout();
            $this->getPigeonhole()->add(new LogoutSuccessMessage);
        }
        //@todo: Return url after logout should be customisable
        $this->redirect('/login');
    }
}
