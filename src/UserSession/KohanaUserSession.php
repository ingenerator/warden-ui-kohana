<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\UserSession;

use Ingenerator\Warden\Core\Entity\User;
use Ingenerator\Warden\Core\Repository\UnknownUserException;
use Ingenerator\Warden\Core\Repository\UserRepository;
use Ingenerator\Warden\Core\UserSession\SimplePropertyUserSession;

class KohanaUserSession extends SimplePropertyUserSession
{
    /**
     * @var \Session
     */
    protected $session_driver;

    /**
     * @var UserRepository
     */
    protected $user_repository;

    public function __construct(\Session $session_driver, UserRepository $user_repository)
    {
        $this->session_driver  = $session_driver;
        $this->user_repository = $user_repository;
    }

    /**
     * {@inheritdoc}
     */
    public function login(User $user)
    {
        parent::login($user);
        $this->session_driver->regenerate();
        $this->session_driver->set('user_id', $user->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function logout()
    {
        parent::logout();
        $this->session_driver->delete('user_id');
        $this->session_driver->regenerate();
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthenticated()
    {
        return (bool) $this->getUserId();
    }

    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        if ($user = parent::getUser()) {
            return $user;
        }

        $user_id = $this->getUserId();
        try {
            return $this->user_repository->load($user_id);
        } catch (UnknownUserException $e) {
            $this->session_driver->delete('user_id');
            throw $e;
        }
    }

    /**
     * @return mixed
     */
    protected function getUserId()
    {
        return $this->session_driver->get('user_id');
    }

}
