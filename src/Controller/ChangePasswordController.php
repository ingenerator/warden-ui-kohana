<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;


use Ingenerator\Warden\Core\Interactor\ChangePasswordInteractor;
use Ingenerator\Warden\Core\Interactor\ChangePasswordResponse;
use Ingenerator\Warden\Core\Interactor\EmailVerificationResponse;
use Ingenerator\Warden\Core\Support\InteractorRequestFactory;
use Ingenerator\Warden\Core\Support\UrlProvider;
use Ingenerator\Warden\Core\UserSession\UserSession;
use Ingenerator\Warden\UI\Kohana\Form\Fieldset;
use Ingenerator\Warden\UI\Kohana\Message\Profile\PasswordChangedMessage;
use Ingenerator\Warden\UI\Kohana\View\ChangePasswordView;
use Psr\Log\LoggerInterface;

class ChangePasswordController extends WardenBaseController
{
    /**
     * @var \Ingenerator\Warden\Core\Interactor\ChangePasswordInteractor
     */
    protected $password_interactor;

    /**
     * @var UserSession
     */
    protected $session;

    /**
     * @var UrlProvider
     */
    protected $urls;

    /**
     * @var \Ingenerator\Warden\UI\Kohana\View\ChangePasswordView
     */
    protected $form_view;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        InteractorRequestFactory $rq_factory,
        ChangePasswordInteractor $password_interactor,
        ChangePasswordView $form_view,
        UrlProvider $urls,
        UserSession $session,
        LoggerInterface $logger
    ) {
        parent::__construct($rq_factory);
        $this->urls                = $urls;
        $this->session             = $session;
        $this->form_view           = $form_view;
        $this->logger              = $logger;
        $this->password_interactor = $password_interactor;
    }

    public function before()
    {
        parent::before();
        if ( ! $this->session->isAuthenticated()) {
            $this->redirect($this->urls->getLoginUrl());
        }
    }

    public function action_get()
    {
        $this->displayChangePasswordForm(new Fieldset([], []));
    }

    protected function displayChangePasswordForm(Fieldset $fieldset)
    {
        $this->form_view->display(
            [
                'fields' => $fieldset,
                'user'   => $this->session->getUser(),
            ]
        );
        $this->respondPageContent($this->form_view);
    }

    public function action_post()
    {
        $values = [
            'user'             => $this->session->getUser(),
            'current_password' => $this->request->post('current_password'),
            'new_password'     => $this->request->post('new_password'),
        ];

        $result = $this->password_interactor->execute(
            $this->makeInteractorRequest('change_password', 'fromArray', $values)
        );

        if ($result->wasSuccessful()) {
            $this->handlePasswordChanged($result);
        } else {
            $this->handlePasswordChangeFailed($result);
        }
    }

    protected function handlePasswordChanged(ChangePasswordResponse $result)
    {
        $this->logger->notice('Password changed by user');
        $this->getPigeonhole()->add(new PasswordChangedMessage);
        $this->redirect($this->urls->getDefaultUserHomeUrl($this->session->getUser()));
    }

    protected function handlePasswordChangeFailed(ChangePasswordResponse $result)
    {
        if ($result->isFailureCode(EmailVerificationResponse::ERROR_DETAILS_INVALID)) {
            $this->displayChangePasswordForm(
                new Fieldset($this->request->post(), $result->getValidationErrors())
            );
        } else {
            throw new \UnexpectedValueException(
                'Unexpected password change failure: '.$result->getFailureCode()
            );
        }
    }

}
