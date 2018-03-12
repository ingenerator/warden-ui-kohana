<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;


use Ingenerator\Warden\Core\Interactor\LoginResponse;
use Ingenerator\Warden\UI\Kohana\Form\Fieldset;
use Ingenerator\Warden\UI\Kohana\Message\Authentication\IncorrectPasswordMessage;
use Ingenerator\Warden\UI\Kohana\Message\Authentication\UnregisteredUserMessage;
use Ingenerator\Warden\UI\Kohana\View\LoginView;

class LoginController extends WardenBaseController
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
        $this->displayLoginForm(new Fieldset(['email' => $this->request->query('email')], []));
    }

    public function action_post()
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

}
