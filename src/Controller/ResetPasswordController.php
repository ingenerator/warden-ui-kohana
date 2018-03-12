<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;


use Ingenerator\Warden\Core\Interactor\PasswordResetInteractor;
use Ingenerator\Warden\Core\Interactor\PasswordResetResponse;
use Ingenerator\Warden\Core\Support\InteractorRequestFactory;
use Ingenerator\Warden\Core\Support\UrlProvider;
use Ingenerator\Warden\Core\UserSession\UserSession;
use Ingenerator\Warden\UI\Kohana\Form\Fieldset;
use Ingenerator\Warden\UI\Kohana\Message\Authentication\InvalidPasswordResetLinkMessage;
use Ingenerator\Warden\UI\Kohana\Message\Authentication\PasswordResetSuccessMessage;
use Ingenerator\Warden\UI\Kohana\View\PasswordResetView;

class ResetPasswordController extends WardenBaseController
{
    /**
     * @var PasswordResetInteractor
     */
    protected $reset_interactor;

    /**
     * @var PasswordResetView
     */
    protected $reset_view;

    /**
     * @var UserSession
     */
    protected $session;

    /**
     * @var UrlProvider
     */
    protected $urls;

    public function __construct(
        InteractorRequestFactory $rq_factory,
        PasswordResetInteractor $reset_interactor,
        PasswordResetView $reset_view,
        UrlProvider $urls,
        UserSession $session
    ) {
        parent::__construct($rq_factory);
        $this->reset_interactor = $reset_interactor;
        $this->reset_view       = $reset_view;
        $this->urls             = $urls;
        $this->session          = $session;
    }

    public function before()
    {
        parent::before();
        if ($this->session->isAuthenticated()) {
            $this->redirect('/profile');
        }
    }

    public function action_get()
    {
        $this->displayPasswordResetForm(new Fieldset(['email' => $this->request->query('email')], []));
    }

    protected function displayPasswordResetForm(Fieldset $fieldset)
    {
        $this->reset_view->display(['fields' => $fieldset]);
        $this->respondPageContent($this->reset_view);
    }

    public function action_post()
    {
        $values = [
            'email'        => $this->request->query('email'),
            'token'        => $this->request->query('token'),
            'new_password' => $this->request->post('new_password'),
        ];

        $result = $this->reset_interactor->execute(
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
                $this->urls->getLoginUrl().'?'.http_build_query(['email' => $result->getEmail()])
            );
        } elseif ($result->isFailureCode(PasswordResetResponse::ERROR_DETAILS_INVALID)) {
            $this->displayPasswordResetForm(new Fieldset($this->request->post(), $result->getValidationErrors()));

        } else {
            throw new \UnexpectedValueException('Unexpected registration failure: '.$result->getFailureCode());
        }
    }

}
