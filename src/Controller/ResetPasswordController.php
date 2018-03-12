<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;


use Ingenerator\Warden\Core\Interactor\PasswordResetResponse;
use Ingenerator\Warden\UI\Kohana\Form\Fieldset;
use Ingenerator\Warden\UI\Kohana\Message\Authentication\InvalidPasswordResetLinkMessage;
use Ingenerator\Warden\UI\Kohana\Message\Authentication\PasswordResetSuccessMessage;
use Ingenerator\Warden\UI\Kohana\View\PasswordResetView;

class ResetPasswordController extends WardenBaseController
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
        $this->displayPasswordResetForm(new Fieldset(['email' => $this->request->query('email')], []));
    }

    protected function displayPasswordResetForm(Fieldset $fieldset)
    {
        $view = $this->getService('warden.view.login.password_reset');
        /** @var PasswordResetView $view */
        $view->display(['fields' => $fieldset]);
        $this->respondPageContent($view);
    }

    public function action_post()
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
