<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;


use Ingenerator\Pigeonhole\Pigeonhole;
use Ingenerator\Warden\Core\Support\InteractorRequestFactory;
use Ingenerator\Warden\UI\Kohana\Message\Register\ExistingUserRegistrationMessage;

abstract class WardenBaseController extends \Controller
{

    /**
     * @var InteractorRequestFactory
     */
    protected $rq_factory;

    public function __construct(InteractorRequestFactory $rq_factory)
    {
        $this->rq_factory = $rq_factory;
    }

    /**
     * @param string $email
     */
    protected function handleRegisterAttemptForExistingUser($email)
    {
        $this->getPigeonhole()->add(new ExistingUserRegistrationMessage($email));
        //@todo: make login url customisable
        $url = '/login?'.http_build_query(['email' => $email]);
        $this->redirect($url);
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


    protected function makeInteractorRequest($type, $factory_method, $argument)
    {
        return $this->rq_factory->make($type, $factory_method, $argument);
    }

}
