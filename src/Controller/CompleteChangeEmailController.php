<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;


use Ingenerator\Warden\Core\Interactor\ChangeEmailInteractor;
use Ingenerator\Warden\Core\Interactor\ChangeEmailResponse;
use Ingenerator\Warden\Core\Support\InteractorRequestFactory;
use Ingenerator\Warden\Core\Support\UrlProvider;
use Ingenerator\Warden\Core\UserSession\UserSession;
use Ingenerator\Warden\UI\Kohana\Message\ChangeEmail\EmailChangedMessage;
use Ingenerator\Warden\UI\Kohana\Message\ChangeEmail\InvalidVerifyEmailLinkMessage;
use Ingenerator\Warden\UI\Kohana\Message\ChangeEmail\NewEmailAlreadyRegisteredMessage;

class CompleteChangeEmailController extends WardenBaseController
{
    /**
     * @var ChangeEmailInteractor
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
        ChangeEmailInteractor $interactor,
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
            'email'   => $this->request->query('email'),
            'token'   => $this->request->query('token'),
        ];

        $result = $this->interactor->execute(
            $this->makeInteractorRequest('change_email', 'fromArray', $values)
        );

        if ($result->wasSuccessful()) {
            $this->handleChangeEmailSuccess($result);
        } else {
            $this->handleChangeEmailFailure($result);
        }
    }

    protected function handleChangeEmailSuccess(ChangeEmailResponse $result)
    {
        $this->getPigeonhole()->add(new EmailChangedMessage($result->getNewEmail()));
        $this->redirect($this->getCompletionUrl());
    }

    protected function handleChangeEmailFailure(ChangeEmailResponse $result)
    {
        if ($result->isFailureCode(ChangeEmailResponse::ERROR_TOKEN_INVALID)) {
            $this->getPigeonhole()->add(new InvalidVerifyEmailLinkMessage);
            $this->redirect($this->getCompletionUrl());
        } elseif ($result->isFailureCode(ChangeEmailResponse::ERROR_ALREADY_REGISTERED)) {
            $this->getPigeonhole()->add(
                new NewEmailAlreadyRegisteredMessage($result->getNewEmail())
            );
            $this->redirect($this->getCompletionUrl());
        } else {
            throw new \UnexpectedValueException(
                'Unexpected email change failure: '.$result->getFailureCode()
            );
        }
    }

    protected function getCompletionUrl()
    {
        if ($this->session->isAuthenticated()) {
            return $this->urls->getDefaultUserHomeUrl($this->session->getUser());
        }
        return $this->urls->getLoginUrl();
    }

}
