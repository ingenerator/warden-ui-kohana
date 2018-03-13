<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;


use Ingenerator\Warden\Core\Support\InteractorRequestFactory;
use Ingenerator\Warden\Core\Support\UrlProvider;
use Ingenerator\Warden\Core\UserSession\UserSession;
use Ingenerator\Warden\UI\Kohana\Message\Authentication\LogoutSuccessMessage;

class LogoutController extends WardenBaseController
{

    /**
     * @var UserSession
     */
    protected $session;

    /**
     * @var UrlProvider
     */
    protected $urls;

    public function __construct(InteractorRequestFactory $rq_factory, UrlProvider $urls, UserSession $session)
    {
        parent::__construct($rq_factory);
        $this->session = $session;
        $this->urls    = $urls;
    }

    public function action_get()
    {
        if ($this->session->isAuthenticated()) {
            $this->session->logout();
            $this->getPigeonhole()->add(new LogoutSuccessMessage);
        }
        $this->redirect($this->urls->getAfterLogoutUrl());
    }
}
