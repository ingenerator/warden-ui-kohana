<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;


use Ingenerator\Pigeonhole\Message;
use Ingenerator\Pigeonhole\Pigeonhole;
use Ingenerator\Warden\Core\Interactor\EmailVerificationInteractor;
use Ingenerator\Warden\Core\Interactor\LoginInteractor;
use Ingenerator\Warden\Core\Interactor\PasswordResetInteractor;
use Ingenerator\Warden\Core\Interactor\UserRegistrationInteractor;
use Ingenerator\Warden\Core\Support\InteractorRequestFactory;
use Ingenerator\Warden\Core\Support\UrlProvider;
use Ingenerator\Warden\Core\UserSession\UserSession;

abstract class WardenBaseController extends \Controller
{

    /**
     * @return EmailVerificationInteractor
     */
    protected function getInteractorEmailVerification()
    {
        return $this->getService('warden.interactor.email_verification');
    }

    /**
     * @return LoginInteractor
     */
    protected function getInteractorLogin()
    {
        return $this->getService('warden.interactor.login');
    }

    /**
     * @return PasswordResetInteractor
     */
    protected function getInteractorPasswordReset()
    {
        return $this->getService('warden.interactor.password_reset');
    }

    protected function makeInteractorRequest($type, $factory_method, $argument)
    {
        $factory = $this->getService('warden.support.interactor_request_factory');

        /** @var InteractorRequestFactory $factory */
        return $factory->make($type, $factory_method, $argument);
    }

    /**
     * @return UserRegistrationInteractor
     */
    protected function getInteractorUserRegistration()
    {
        return $this->getService('warden.interactor.user_registration');
    }

    /**
     * @return Pigeonhole
     */
    protected function getPigeonhole()
    {
        return $this->getService('pigeonhole');
    }

    /**
     * @param string $service
     *
     * @return object
     */
    protected function getService($service)
    {
        return $this->dependencies->get($service);
    }

    /**
     * @return UrlProvider
     */
    protected function getUrlProvider()
    {
        return $this->getService('warden.support.url_provider');
    }
    
    /**
     * @return UserSession
     */
    protected function getUserSession()
    {
        return $this->getService('warden.user_session.session');
    }

}
