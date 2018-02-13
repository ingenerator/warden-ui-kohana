<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;


use Ingenerator\Pigeonhole\Message;
use Ingenerator\Warden\Core\Interactor\LoginResponse;
use Ingenerator\Warden\Core\Interactor\PasswordResetResponse;
use Ingenerator\Warden\UI\Kohana\Form\Fieldset;
use Ingenerator\Warden\UI\Kohana\Message\Authentication\IncorrectPasswordMessage;
use Ingenerator\Warden\UI\Kohana\Message\Authentication\InvalidPasswordResetLinkMessage;
use Ingenerator\Warden\UI\Kohana\Message\Authentication\LogoutSuccessMessage;
use Ingenerator\Warden\UI\Kohana\Message\Authentication\PasswordResetSuccessMessage;
use Ingenerator\Warden\UI\Kohana\Message\Authentication\UnregisteredUserMessage;
use Ingenerator\Warden\UI\Kohana\View\LoginView;
use Ingenerator\Warden\UI\Kohana\View\PasswordResetView;

class Authentication extends WardenBaseController
{

    public function action_login()
    {
        if ($this->getUserSession()->isAuthenticated()) {
            $this->redirect('/profile');
        }

        if ($this->request->query('action') === 'reset-password') {
            $this->handlePasswordReset();
        } elseif ($this->request->method() === \Request::POST) {
            $this->handleLoginRequest();
        } else {
            $this->displayLoginForm(new Fieldset(['email' => $this->request->query('email')], []));
        }
    }

    protected function handleLoginRequest()
    {
        $result = $this->getInteractorLogin()->execute(
            $this->makeInteractorRequest('login', 'fromArray', $this->request->post())
        );

        if ($result->wasSuccessful()) {
            // @todo: Make the login success url customisable
            $this->redirect('/profile');
        } else {
            $this->handleLoginFailure($result);
        }
    }

    protected function handleLoginFailure(LoginResponse $result)
    {
        switch ($result->getFailureCode()) {
            case LoginResponse::ERROR_NOT_REGISTERED:
                $this->handleLoginNotRegistered($result);
                break;

            case LoginResponse::ERROR_PASSWORD_INCORRECT:
                $this->handleLoginInvalidPassword($result);
                break;

            case LoginResponse::ERROR_DETAILS_INVALID:
                $this->displayLoginForm(new Fieldset($this->request->post(), $result->getValidationErrors()));
                break;

            case LoginResponse::ERROR_NOT_ACTIVE:
            default:
                throw new \UnexpectedValueException('Unexpected login failure: '.$result->getFailureCode());
        }
    }

    protected function handleLoginInvalidPassword(LoginResponse $result)
    {
        $this->getPigeonhole()->add(new IncorrectPasswordMessage($result->getEmail()));
        $this->displayLoginForm(new Fieldset(['email' => $result->getEmail()], ['password' => 'Incorrect password']));
    }

    protected function handleLoginNotRegistered(LoginResponse $result)
    {
        $this->getPigeonhole()->add(new UnregisteredUserMessage($result->getEmail()));
        $registration_url = $this->getUrlProvider()->getRegistrationUrl();
        $this->redirect(
            $registration_url.'?'.http_build_query(['email' => $result->getEmail()])
        );
    }

    protected function displayLoginForm(Fieldset $fields)
    {
        /** @var LoginView $view */
        $view = $this->getService('warden.view.login.login');
        $view->display(['fields' => $fields]);

        $this->respondPageContent($view);

    }

    public function action_logout()
    {
        if ($this->getUserSession()->isAuthenticated()) {
            $this->getUserSession()->logout();
            $this->getPigeonhole()->add(new LogoutSuccessMessage);
        }
        //@todo: Return url after logout should be customisable
        $this->redirect('/login');
    }

    protected function handlePasswordReset()
    {
        if ($this->request->method() === \Request::POST) {
            $this->handlePasswordResetSubmission();
        } else {
            $this->displayPasswordResetForm(new Fieldset(['email' => $this->request->query('email')], []));
        }
    }

    protected function handlePasswordResetSubmission()
    {
        $values = [
            'email'        => $this->request->query('email'),
            'token'        => $this->request->query('token'),
            'new_password' => $this->request->post('new_password'),
        ];

        $result = $this->getInteractorPasswordReset()->execute(
            $this->makeInteractorRequest('password_reset', 'fromArray', $values)
        );

        if ($result->wasSuccessful()) {
            $this->handlePasswordResetSuccess($result);
        } else {
            $this->handlePasswordResetFailure($result);
        }
    }

    protected function displayPasswordResetForm(Fieldset $fieldset)
    {
        $view = $this->getService('warden.view.login.password_reset');
        /** @var PasswordResetView $view */
        $view->display(['fields' => $fieldset]);
        $this->respondPageContent($view);
    }

    protected function handlePasswordResetSuccess(PasswordResetResponse $result)
    {
        $this->getPigeonhole()->add(new PasswordResetSuccessMessage($result->getEmail()));
        // @todo: Make the login success url customisable
        $this->redirect('/profile');
    }

    protected function handlePasswordResetFailure(PasswordResetResponse $result)
    {
        if ($result->isFailureCode(PasswordResetResponse::ERROR_TOKEN_INVALID)) {
            $this->getPigeonhole()->add(new InvalidPasswordResetLinkMessage);
            $this->redirect(
                $this->getUrlProvider()->getLoginUrl().'?'.http_build_query(['email' => $result->getEmail()])
            );
        } elseif ($result->isFailureCode(PasswordResetResponse::ERROR_DETAILS_INVALID)) {
            $this->displayPasswordResetForm(new Fieldset($this->request->post(), $result->getValidationErrors()));

        } else {
            throw new \UnexpectedValueException('Unexpected registration failure: '.$result->getFailureCode());
        }
    }

}
