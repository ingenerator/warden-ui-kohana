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
use Ingenerator\Warden\UI\Kohana\Form\Fieldset;
use Ingenerator\Warden\UI\Kohana\Message\Authentication\AccountNotActiveMessage;
use Ingenerator\Warden\UI\Kohana\Message\Authentication\IncorrectPasswordMessage;
use Ingenerator\Warden\UI\Kohana\Message\Authentication\UnregisteredUserMessage;
use Ingenerator\Warden\UI\Kohana\View\LoginView;
use Psr\Log\LoggerInterface;

class LoginController extends WardenBaseController
{
    /**
     * @var LoginInteractor
     */
    protected $interactor;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

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
        UserSession $user_session,
        LoggerInterface $logger
    ) {
        parent::__construct($rq_factory);
        $this->interactor = $interactor;
        $this->logger = $logger;
        $this->login_view = $login_view;
        $this->urls = $urls;
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
        $this->logger->notice(
            'Failed login for `'.$result->getEmail().'`: '.$result->getFailureCode()
        );
        switch ($result->getFailureCode()) {
            case LoginResponse::ERROR_NOT_REGISTERED:
                $this->handleLoginNotRegistered($result);
                break;

            /** @noinspection PhpMissingBreakStatementInspection */
            case LoginResponse::ERROR_PASSWORD_INCORRECT_RESET_THROTTLED:
                $this->logThrottledEmailVerification($result, 'reset');
            // Continue to the next step - no particular need for the user to know
            case LoginResponse::ERROR_PASSWORD_INCORRECT:
                $this->handleLoginInvalidPassword($result);
                break;

            case LoginResponse::ERROR_DETAILS_INVALID:
                $this->displayLoginForm(
                    new Fieldset($this->request->post(), $result->getValidationErrors())
                );
                break;

            /** @noinspection PhpMissingBreakStatementInspection */
            case LoginResponse::ERROR_NOT_ACTIVE_ACTIVATION_THROTTLED:
                $this->logThrottledEmailVerification($result, 'activation');
            // Continue to the next step - no particular need for the user to know
            case LoginResponse::ERROR_NOT_ACTIVE:
                $this->handleLoginInactiveAccount($result);
                break;

            default:
                throw new \UnexpectedValueException(
                    'Unexpected login failure: '.$result->getFailureCode()
                );
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
        $this->displayLoginForm(
            new Fieldset(['email' => $result->getEmail()], ['password' => 'Incorrect password'])
        );
    }

    protected function logThrottledEmailVerification(LoginResponse $result, $type)
    {
        $this->logger->debug(
            sprintf(
                'Skipped sending %s to %s (rate limit will clear %s)',
                $type,
                $result->getEmail(),
                $result->canRetryAfter()->format(\DateTime::ATOM)
            )
        );
    }

    protected function handleLoginInactiveAccount(LoginResponse $result)
    {
        $this->getPigeonhole()->add(new AccountNotActiveMessage($result->getEmail()));
        $this->displayLoginForm(new Fieldset(['email' => $result->getEmail()], []));
    }


}
