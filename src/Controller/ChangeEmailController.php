<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;


use Ingenerator\Warden\Core\Interactor\EmailVerificationInteractor;
use Ingenerator\Warden\Core\Interactor\EmailVerificationRequest;
use Ingenerator\Warden\Core\Interactor\EmailVerificationResponse;
use Ingenerator\Warden\Core\Support\InteractorRequestFactory;
use Ingenerator\Warden\Core\Support\UrlProvider;
use Ingenerator\Warden\Core\UserSession\UserSession;
use Ingenerator\Warden\UI\Kohana\Form\Fieldset;
use Ingenerator\Warden\UI\Kohana\Message\ChangeEmail\NewEmailAlreadyRegisteredMessage;
use Ingenerator\Warden\UI\Kohana\Message\ChangeEmail\VerifyNewEmailLinkSentMessage;
use Ingenerator\Warden\UI\Kohana\View\ChangeEmailView;
use Psr\Log\LoggerInterface;

class ChangeEmailController extends WardenBaseController
{
    /**
     * @var EmailVerificationInteractor
     */
    protected $email_interactor;

    /**
     * @var UserSession
     */
    protected $session;

    /**
     * @var UrlProvider
     */
    protected $urls;

    /**
     * @var \Ingenerator\Warden\UI\Kohana\View\ChangeEmailView
     */
    protected $form_view;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        InteractorRequestFactory $rq_factory,
        EmailVerificationInteractor $email_interactor,
        ChangeEmailView $form_view,
        UrlProvider $urls,
        UserSession $session,
        LoggerInterface $logger
    ) {
        parent::__construct($rq_factory);
        $this->urls             = $urls;
        $this->session          = $session;
        $this->email_interactor = $email_interactor;
        $this->form_view        = $form_view;
        $this->logger           = $logger;
    }

    public function before()
    {
        parent::before();
        if ( ! $this->session->isAuthenticated()) {
            $this->redirect($this->urls->getLoginUrl());
        }
    }

    protected function getUserToUpdate()
    {
        return $this->session->getUser();
    }

    public function action_get()
    {
        $this->displayEmailVerificationForm(new Fieldset([], []));
    }

    protected function displayEmailVerificationForm(Fieldset $fieldset)
    {
        $this->form_view->display(
            [
                'fields' => $fieldset,
                'user'   => $this->getUserToUpdate(),
            ]
        );
        $this->respondPageContent($this->form_view);
    }

    public function action_post()
    {
        $result = $this->email_interactor->execute(
            EmailVerificationRequest::forChangeEmail(
                $this->getUserToUpdate(),
                $this->request->post('email')
            )
        );

        if ($result->wasSuccessful()) {
            $this->handleEmailVerificationSent($result);
        } else {
            $this->handleEmailVerificationFailed($result);
        }
    }

    protected function handleEmailVerificationSent(EmailVerificationResponse $result)
    {
        $this->getPigeonhole()->add(new VerifyNewEmailLinkSentMessage($result->getEmail()));
        $this->redirect($this->urls->getAfterVerifyEmailSentUrl());
    }

    protected function handleEmailVerificationFailed(EmailVerificationResponse $result)
    {
        if ($result->isFailureCode(EmailVerificationResponse::ERROR_RATE_LIMITED)) {
            $this->handleThrottledChangeEmailAttempt($result);

        } elseif ($result->isFailureCode(EmailVerificationResponse::ERROR_ALREADY_REGISTERED)) {
            $this->getPigeonhole()->add(new NewEmailAlreadyRegisteredMessage($result->getEmail()));
            $this->displayEmailVerificationForm(new Fieldset($this->request->post(), []));

        } elseif ($result->isFailureCode(EmailVerificationResponse::ERROR_DETAILS_INVALID)) {
            $this->displayEmailVerificationForm(
                new Fieldset($this->request->post(), $result->getValidationErrors())
            );
        } else {
            throw new \UnexpectedValueException(
                'Unexpected email verification failure: '.$result->getFailureCode()
            );
        }
    }


    protected function handleThrottledChangeEmailAttempt(EmailVerificationResponse $result)
    {
        $this->logger->debug(
            sprintf(
                'Skipped sending change-email to %s (rate limit will clear %s)',
                $result->getEmail(),
                $result->canRetryAfter()->format(\DateTime::ATOM)
            )
        );
        $this->handleEmailVerificationSent($result);
    }

}
