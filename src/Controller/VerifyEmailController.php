<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;


use Ingenerator\Warden\Core\Interactor\EmailVerificationInteractor;
use Ingenerator\Warden\Core\Interactor\EmailVerificationResponse;
use Ingenerator\Warden\Core\Support\InteractorRequestFactory;
use Ingenerator\Warden\Core\Support\UrlProvider;
use Ingenerator\Warden\Core\UserSession\UserSession;
use Ingenerator\Warden\UI\Kohana\Form\Fieldset;
use Ingenerator\Warden\UI\Kohana\Message\Register\EmailVerificationSentMessage;
use Ingenerator\Warden\UI\Kohana\View\EmailVerificationView;
use Psr\Log\LoggerInterface;

class VerifyEmailController extends WardenBaseController
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
     * @var EmailVerificationView
     */
    protected $verify_view;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        InteractorRequestFactory $rq_factory,
        EmailVerificationInteractor $email_interactor,
        EmailVerificationView $verify_view,
        UrlProvider $urls,
        UserSession $session,
        LoggerInterface $logger
    ) {
        parent::__construct($rq_factory);
        $this->urls             = $urls;
        $this->session          = $session;
        $this->email_interactor = $email_interactor;
        $this->verify_view      = $verify_view;
        $this->logger           = $logger;
    }

    public function before()
    {
        parent::before();
        $this->redirectHomeIfLoggedIn($this->session, $this->urls);
    }

    public function action_get()
    {
        $this->displayEmailVerificationForm(new Fieldset(['email' => $this->request->query('email')], []));
    }

    protected function displayEmailVerificationForm(Fieldset $fieldset)
    {
        $this->verify_view->display(['fields' => $fieldset]);
        $this->respondPageContent($this->verify_view);
    }

    public function action_post()
    {
        $result = $this->email_interactor->execute(
            $this->makeInteractorRequest(
                'email_verification',
                'forRegistration',
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
        $this->getPigeonhole()->add(new EmailVerificationSentMessage($result->getEmail()));
        $this->redirect($this->urls->getAfterVerifyEmailSentUrl());
    }

    protected function handleEmailVerificationFailed(EmailVerificationResponse $result)
    {
        if ($result->isFailureCode(EmailVerificationResponse::ERROR_RATE_LIMITED)) {
            $this->handleThrottledRegisterAttempt($result);
        } elseif ($result->isFailureCode(EmailVerificationResponse::ERROR_ALREADY_REGISTERED)) {
            $this->handleRegisterAttemptForExistingUser($this->urls, $result->getEmail());
        } elseif ($result->isFailureCode(EmailVerificationResponse::ERROR_DETAILS_INVALID)) {
            $this->displayEmailVerificationForm(new Fieldset($this->request->post(), $result->getValidationErrors()));
        } else {
            throw new \UnexpectedValueException('Unexpected email verification failure: '.$result->getFailureCode());
        }
    }


    protected function handleThrottledRegisterAttempt(EmailVerificationResponse $result)
    {
        $this->logger->debug(
            sprintf(
                'Skipped sending verification to %s (rate limit will clear %s)',
                $result->getEmail(),
                $result->canRetryAfter()->format(\DateTime::ATOM)
            )
        );
        $this->handleEmailVerificationSent($result);
    }

}
