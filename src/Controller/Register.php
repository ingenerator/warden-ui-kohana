<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;

use Ingenerator\Pigeonhole\Message;
use Ingenerator\Warden\Core\Entity\User;
use Ingenerator\Warden\Core\Interactor\EmailVerificationResponse;
use Ingenerator\Warden\Core\Interactor\UserRegistrationResponse;
use Ingenerator\Warden\UI\Kohana\Form\Fieldset;
use Ingenerator\Warden\UI\Kohana\Message\Register\EmailVerificationSentMessage;
use Ingenerator\Warden\UI\Kohana\Message\Register\ExistingUserRegistrationMessage;
use Ingenerator\Warden\UI\Kohana\Message\Register\RegistrationSuccessMessage;
use Ingenerator\Warden\UI\Kohana\View\EmailVerificationView;
use Ingenerator\Warden\UI\Kohana\View\RegistrationView;

class Register extends WardenBaseController
{

    public function action_register()
    {
        if ($this->getUserSession()->isAuthenticated()) {
            $this->redirect('/profile');
        }
        // if action is to confirm registration and there is a token
//        $this->handleRegistrationActivation();
        // if configured to require email confirm before registration and there is no token param
        if ( ! $this->request->query('token')) {
            $this->handleEmailConfirmationForRegistration();
        } else {
            $this->handleRegistration();
        }
    }

    protected function handleEmailConfirmationForRegistration()
    {
        if ($this->request->method() === \Request::POST) {
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
        } else {
            $this->displayEmailVerificationForm(new Fieldset(['email' => $this->request->query('email')], []));
        }
    }

    protected function displayEmailVerificationForm(Fieldset $fieldset)
    {
        $view = $this->getService('warden.view.registration.email_verification');
        /** @var EmailVerificationView $view */
        $view->display(['fields' => $fieldset]);
        $this->respondPageContent($view);
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

    /**
     * @param string $email
     */
    protected function handleRegisterAttemptForExistingUser($email)
    {
        $this->getPigeonhole()->add(new ExistingUserRegistrationMessage($email));
        //@todo: make login url customisable
        $url = '/login?'.http_build_query(['email' => $email]);
        $this->redirect($url);
    }

    protected function handleRegistration()
    {
        if ($this->request->method() === \Request::POST) {
            $result = $this->getInteractorUserRegistration()
                ->execute(
                    $this->makeInteractorRequest(
                        'user_registration',
                        'fromArray',
                        $this->request->post()
                    )
                );

            if ($result->wasSuccessful()) {
                $user = $result->getUser();
                if ( ! $user->isActive()) {
                    $this->handleInactiveUserRegistration();
                } else {
                    $this->handleActiveUserRegistration($user);
                }
            } else {
                $this->handleRegistrationFailed($result);
            }
        } else {
            $data = [
                'email'                    => $this->request->query('email'),
                'email_confirmation_token' => $this->request->query('token'),
            ];
            $this->displayRegistrationForm(new Fieldset($data, []));
        }
    }

    protected function displayRegistrationForm(Fieldset $fieldset)
    {
        $view = $this->getService('warden.view.registration.registration');
        /** @var RegistrationView $view */
        $view->display(['fields' => $fieldset]);

        $this->respondPageContent($view);
    }

    protected function handleActiveUserRegistration(User $user)
    {
        if ( ! $this->getUserSession()->isAuthenticated()) {
            throw new \UnexpectedValueException('Active user was not authenticated after registration');
        }

        $this->getPigeonhole()->add(new RegistrationSuccessMessage());
        // @todo: Make the registration success url customisable
        $this->redirect('/profile');
    }

    protected function handleInactiveUserRegistration()
    {
        throw new \BadMethodCallException('Registering inactive users is not yet supported');
        // Trigger EmailVerificationInteractor
        // EmailVerificationRequest::forActivation($user);
        // Flash a notification
        // Redirect somewhere sensible
    }

    protected function handleRegistrationFailed(UserRegistrationResponse $result)
    {
        if ($result->isFailureCode(UserRegistrationResponse::ERROR_ALREADY_REGISTERED)) {
            $this->handleRegisterAttemptForExistingUser($result->getEmail());

        } elseif ($result->isFailureCode(UserRegistrationResponse::ERROR_DETAILS_INVALID)) {
            $this->displayRegistrationForm(new Fieldset($this->request->post(), $result->getValidationErrors()));

        } else {
            throw new \UnexpectedValueException('Unexpected registration failure: '.$result->getFailureCode());
        }
    }

}
