<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;

use Ingenerator\Warden\Core\Entity\User;
use Ingenerator\Warden\Core\Interactor\UserRegistrationInteractor;
use Ingenerator\Warden\Core\Interactor\UserRegistrationResponse;
use Ingenerator\Warden\Core\Support\InteractorRequestFactory;
use Ingenerator\Warden\Core\UserSession\UserSession;
use Ingenerator\Warden\UI\Kohana\Form\Fieldset;
use Ingenerator\Warden\UI\Kohana\Message\Register\RegistrationSuccessMessage;
use Ingenerator\Warden\UI\Kohana\View\RegistrationView;

class RegisterController extends WardenBaseController
{
    /**
     * @var UserRegistrationInteractor
     */
    protected $register_interactor;

    /**
     * @var RegistrationView
     */
    protected $register_view;

    /**
     * @var UserSession
     */
    protected $session;

    public function __construct(
        InteractorRequestFactory $rq_factory,
        UserRegistrationInteractor $register_interactor,
        RegistrationView $register_view,
        UserSession $session
    ) {
        parent::__construct($rq_factory);
        $this->register_interactor = $register_interactor;
        $this->register_view       = $register_view;
        $this->session             = $session;
    }

    public function before()
    {
        parent::before();
        if ($this->session->isAuthenticated()) {
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
        $this->register_view->display(['fields' => $fieldset]);

        $this->respondPageContent($this->register_view);
    }

    public function action_post()
    {
        $result = $this->register_interactor->execute(
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
        if ( ! $this->session->isAuthenticated()) {
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
