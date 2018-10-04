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
use Ingenerator\Warden\Core\Support\UrlProvider;
use Ingenerator\Warden\Core\UserSession\UserSession;
use Ingenerator\Warden\UI\Kohana\Form\Fieldset;
use Ingenerator\Warden\UI\Kohana\Message\Register\InvalidRegisterLinkMessage;
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
    /**
     * @var UrlProvider
     */
    private $urls;

    public function __construct(
        InteractorRequestFactory $rq_factory,
        UrlProvider $urls,
        UserRegistrationInteractor $register_interactor,
        RegistrationView $register_view,
        UserSession $session
    ) {
        parent::__construct($rq_factory);
        $this->register_interactor = $register_interactor;
        $this->register_view       = $register_view;
        $this->session             = $session;
        $this->urls                = $urls;
    }

    public function before()
    {
        parent::before();
        $this->redirectHomeIfLoggedIn($this->session, $this->urls);
    }

    public function action_get()
    {
        $request     = $this->makeRegistrationRequest();
        $token_state = $this->register_interactor->validateToken($request);
        if ( ! $token_state->isValid()) {
            $this->handleInvalidLinkFailure($request->getEmail());
        }

        $this->displayRegistrationForm(
            new Fieldset(
                [
                    'email'                    => $request->getEmail(),
                    'email_confirmation_token' => $request->getToken()
                ], []
            )
        );
    }

    /**
     * @return \Ingenerator\Warden\Core\Interactor\UserRegistrationRequest
     */
    protected function makeRegistrationRequest()
    {
        if ($this->request->method() === \Request::POST) {
            $values = $this->request->post();
        } else {
            $values = [
                'email'                    => $this->request->query('email'),
                'email_confirmation_token' => $this->request->query('token'),
            ];
        }

        return $this->makeInteractorRequest(
            'user_registration',
            'fromArray',
            $values
        );
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
            throw new \UnexpectedValueException(
                'Active user was not authenticated after registration'
            );
        }

        $this->getPigeonhole()->add(new RegistrationSuccessMessage());
        $this->redirect($this->urls->getAfterLoginUrl($user));
    }

    protected function handleRegistrationFailed(UserRegistrationResponse $result)
    {
        if ($result->isFailureCode(UserRegistrationResponse::ERROR_ALREADY_REGISTERED)) {
            $this->handleRegisterAttemptForExistingUser($this->urls, $result->getEmail());

        } elseif ($result->isFailureCode(
            UserRegistrationResponse::ERROR_EMAIL_CONFIRMATION_INVALID
        )) {
            $this->handleInvalidLinkFailure($result->getEmail());

        } elseif ($result->isFailureCode(UserRegistrationResponse::ERROR_DETAILS_INVALID)) {
            $this->displayRegistrationForm(
                new Fieldset($this->request->post(), $result->getValidationErrors())
            );

        } else {
            throw new \UnexpectedValueException(
                'Unexpected registration failure: '.$result->getFailureCode()
            );
        }
    }

    protected function handleInvalidLinkFailure($email)
    {
        $this->getPigeonhole()->add(new InvalidRegisterLinkMessage);
        $this->redirect($this->urls->getRegisterVerifyEmailUrl($email));
    }

}
