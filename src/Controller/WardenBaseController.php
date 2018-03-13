<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Controller;


use Ingenerator\Pigeonhole\Pigeonhole;
use Ingenerator\Warden\Core\Support\InteractorRequestFactory;
use Ingenerator\Warden\Core\Support\UrlProvider;
use Ingenerator\Warden\UI\Kohana\Message\Register\ExistingUserRegistrationMessage;

abstract class WardenBaseController extends \Controller
{

    /**
     * @var InteractorRequestFactory
     */
    protected $rq_factory;

    public function __construct(InteractorRequestFactory $rq_factory)
    {
        parent::__construct();
        $this->rq_factory = $rq_factory;
    }

    /**
     * @param string $email
     */
    protected function handleRegisterAttemptForExistingUser(UrlProvider $urls, $email)
    {
        $this->getPigeonhole()->add(new ExistingUserRegistrationMessage($email));
        $this->redirect($urls->getLoginUrl($email));
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
