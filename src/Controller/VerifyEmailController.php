<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;


use Ingenerator\Pigeonhole\Message;
use Ingenerator\Warden\Core\Interactor\EmailVerificationResponse;
use Ingenerator\Warden\UI\Kohana\Form\Fieldset;
use Ingenerator\Warden\UI\Kohana\Message\Register\EmailVerificationSentMessage;
use Ingenerator\Warden\UI\Kohana\View\EmailVerificationView;

class VerifyEmailController extends WardenBaseController
{
    public function before()
    {
        parent::before();
        if ($this->getUserSession()->isAuthenticated()) {
            $this->redirect('/profile');
        }
    }

    public function action_get()
    {
        $this->displayEmailVerificationForm(new Fieldset(['email' => $this->request->query('email')], []));
    }

    protected function displayEmailVerificationForm(Fieldset $fieldset)
    {
        $view = $this->getService('warden.view.registration.email_verification');
        /** @var EmailVerificationView $view */
        $view->display(['fields' => $fieldset]);
        $this->respondPageContent($view);
    }

    public function action_post()
    {
        $result = $this->getInteractorEmailVerification()
            ->execute(
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
        //@todo: make the email-verification url customisable
        $this->redirect('/');
    }

    protected function handleEmailVerificationFailed(EmailVerificationResponse $result)
    {
        if ($result->isFailureCode(EmailVerificationResponse::ERROR_ALREADY_REGISTERED)) {
            $this->handleRegisterAttemptForExistingUser($result->getEmail());
        } elseif ($result->isFailureCode(EmailVerificationResponse::ERROR_DETAILS_INVALID)) {
            $this->displayEmailVerificationForm(new Fieldset($this->request->post(), $result->getValidationErrors()));
        } else {
            throw new \UnexpectedValueException('Unexpected email verification failure: '.$result->getFailureCode());
        }
    }

}
