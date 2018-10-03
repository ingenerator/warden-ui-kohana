<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;


use Ingenerator\Warden\Core\Interactor\PasswordResetInteractor;
use Ingenerator\Warden\Core\Interactor\PasswordResetRequest;
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
        $this->redirectHomeIfLoggedIn($this->session, $this->urls);
    }

    public function action_get()
    {
        $token_state = $this->reset_interactor->validateToken($this->makePasswordResetRequest());
        if ( ! $token_state->isValid()) {
            $this->handleInvalidLinkFailure();
        }

        $this->displayPasswordResetForm(
            new Fieldset(['email' => $token_state->getUserEmail()], [])
        );
    }

    protected function displayPasswordResetForm(Fieldset $fieldset)
    {
        $this->reset_view->display(['fields' => $fieldset]);
        $this->respondPageContent($this->reset_view);
    }

    public function action_post()
    {
        $result = $this->reset_interactor->execute($this->makePasswordResetRequest());

        if ($result->wasSuccessful()) {
            $this->handlePasswordResetSuccess($result);
        } else {
            $this->handlePasswordResetFailure($result);
        }
    }

    /**
     * @return PasswordResetRequest
     */
    protected function makePasswordResetRequest()
    {
        $values = [
            'user_id'      => $this->request->query('user_id'),
            'token'        => $this->request->query('token'),
            'new_password' => $this->request->post('new_password'),
        ];
        return $this->makeInteractorRequest('password_reset', 'fromArray', $values);
    }

    protected function handlePasswordResetSuccess(PasswordResetResponse $result)
    {
        $this->getPigeonhole()->add(new PasswordResetSuccessMessage($result->getEmail()));
        $this->redirect($this->urls->getAfterLoginUrl($this->session->getUser()));
    }

    protected function handlePasswordResetFailure(PasswordResetResponse $result)
    {
        if ($result->isFailureCode(PasswordResetResponse::ERROR_TOKEN_INVALID)) {
            $this->handleInvalidLinkFailure();

        } elseif ($result->isFailureCode(PasswordResetResponse::ERROR_DETAILS_INVALID)) {
            $this->displayPasswordResetForm(
                new Fieldset($this->request->post(), $result->getValidationErrors())
            );

        } else {
            throw new \UnexpectedValueException(
                'Unexpected registration failure: '.$result->getFailureCode()
            );
        }
    }

    protected function handleInvalidLinkFailure()
    {
        $this->getPigeonhole()->add(new InvalidPasswordResetLinkMessage);
        $this->redirect($this->urls->getLoginUrl());
    }

}
