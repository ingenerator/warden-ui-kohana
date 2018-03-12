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

class RegisterController extends WardenBaseController
{
    public function before()
    {
        parent::before();
        if ($this->getUserSession()->isAuthenticated()) {
            $this->redirect('/profile');
        }

        // if configured to require email confirm before registration and there is no token param
        if ( ! $this->request->query('token')) {
            // @todo: configure the verify email route
            $this->redirect('/register/verify-email?'.http_build_query(['email' => $this->request->query('email')]));
        }
    }

    public function action_get()
    {
        $data = [
            'email'                    => $this->request->query('email'),
            'email_confirmation_token' => $this->request->query('token'),
        ];
        $this->displayRegistrationForm(new Fieldset($data, []));
    }

    protected function displayRegistrationForm(Fieldset $fieldset)
    {
        $view = $this->getService('warden.view.registration.registration');
        /** @var RegistrationView $view */
        $view->display(['fields' => $fieldset]);

        $this->respondPageContent($view);
    }

    public function action_post()
    {
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
    }

    protected function handleInactiveUserRegistration()
    {
        throw new \BadMethodCallException('Registering inactive users is not yet supported');
        // Trigger EmailVerificationInteractor
        // EmailVerificationRequest::forActivation($user);
        // Flash a notification
        // Redirect somewhere sensible
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
