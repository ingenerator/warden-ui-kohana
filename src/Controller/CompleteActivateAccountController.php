<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;


use Ingenerator\Warden\Core\Interactor\ActivateAccountInteractor;
use Ingenerator\Warden\Core\Interactor\ActivateAccountResponse;
use Ingenerator\Warden\Core\Support\InteractorRequestFactory;
use Ingenerator\Warden\Core\Support\UrlProvider;
use Ingenerator\Warden\Core\UserSession\UserSession;
use Ingenerator\Warden\UI\Kohana\Message\Register\AccountActivatedMessage;
use Ingenerator\Warden\UI\Kohana\Message\Register\InvalidActivationLinkMessage;

class CompleteActivateAccountController extends WardenBaseController
{
    /**
     * @var \Ingenerator\Warden\Core\Interactor\ActivateAccountInteractor
     */
    protected $interactor;

    /**
     * @var UserSession
     */
    protected $session;

    /**
     * @var UrlProvider
     */
    protected $urls;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        InteractorRequestFactory $rq_factory,
        ActivateAccountInteractor $interactor,
        UrlProvider $urls,
        UserSession $session
    ) {
        parent::__construct($rq_factory);
        $this->urls       = $urls;
        $this->session    = $session;
        $this->interactor = $interactor;
    }

    public function action_get()
    {
        $values = [
            'user_id' => $this->request->query('user_id'),
            'token'   => $this->request->query('token'),
        ];

        $result = $this->interactor->execute(
            $this->makeInteractorRequest('activate_account', 'fromArray', $values)
        );

        if ($result->wasSuccessful()) {
            $this->handleActivationSuccess($result);
        } else {
            $this->handleActivationFailure($result);
        }
    }

    protected function handleActivationSuccess(ActivateAccountResponse $result)
    {
        $this->getPigeonhole()->add(new AccountActivatedMessage);
        $this->redirect($this->urls->getAfterLoginUrl($this->session->getUser()));
    }

    protected function handleActivationFailure(ActivateAccountResponse $result)
    {
        if ($result->isFailureCode(ActivateAccountResponse::ERROR_TOKEN_INVALID)) {
            $this->getPigeonhole()->add(new InvalidActivationLinkMessage);
            $this->redirect($this->urls->getLoginUrl());
        } else {
            throw new \UnexpectedValueException(
                'Unexpected activation failure: '.$result->getFailureCode()
            );
        }
    }
}
