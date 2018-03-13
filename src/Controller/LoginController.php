<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;


use Ingenerator\Warden\Core\Interactor\LoginInteractor;
use Ingenerator\Warden\Core\Interactor\LoginResponse;
use Ingenerator\Warden\Core\Support\InteractorRequestFactory;
use Ingenerator\Warden\Core\Support\UrlProvider;
use Ingenerator\Warden\Core\UserSession\UserSession;
use Ingenerator\Warden\UI\Kohana\Dummy\ReverseRouteURLProvider;
use Ingenerator\Warden\UI\Kohana\Form\Fieldset;
use Ingenerator\Warden\UI\Kohana\Message\Authentication\IncorrectPasswordMessage;
use Ingenerator\Warden\UI\Kohana\Message\Authentication\UnregisteredUserMessage;
use Ingenerator\Warden\UI\Kohana\View\LoginView;

class LoginController extends WardenBaseController
{
    /**
     * @var LoginInteractor
     */
    protected $interactor;

    /**
     * @var LoginView
     */
    protected $login_view;

    /**
     * @var UrlProvider
     */
    protected $urls;

    /**
     * @var UserSession
     */
    protected $user_session;

    public function __construct(
        InteractorRequestFactory $rq_factory,
        LoginInteractor $interactor,
        LoginView $login_view,
        UrlProvider $urls,
        UserSession $user_session
    ) {
        parent::__construct($rq_factory);
        $this->interactor   = $interactor;
        $this->login_view   = $login_view;
        $this->urls         = $urls;
        $this->user_session = $user_session;
    }

    public function before()
    {
        parent::before();
        $this->redirectHomeIfLoggedIn($this->user_session, $this->urls);
    }

    public function action_get()
    {
        $this->displayLoginForm(new Fieldset(['email' => $this->request->query('email')], []));
    }

    protected function displayLoginForm(Fieldset $fields)
    {
        $this->login_view->display(['fields' => $fields]);
        $this->respondPageContent($this->login_view);
    }

    public function action_post()
    {
        $result = $this->interactor->execute(
            $this->makeInteractorRequest('login', 'fromArray', $this->request->post())
        );

        if ($result->wasSuccessful()) {
            $this->redirect($this->urls->getAfterLoginUrl($result->getUser()));

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

    protected function handleLoginNotRegistered(LoginResponse $result)
    {
        $this->getPigeonhole()->add(new UnregisteredUserMessage($result->getEmail()));
        $this->redirect($this->urls->getRegisterVerifyEmailUrl($result->getEmail()));
    }

    protected function handleLoginInvalidPassword(LoginResponse $result)
    {
        $this->getPigeonhole()->add(new IncorrectPasswordMessage($result->getEmail()));
        $this->displayLoginForm(new Fieldset(['email' => $result->getEmail()], ['password' => 'Incorrect password']));
    }


}
