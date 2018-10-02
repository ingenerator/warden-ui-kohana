<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 */

namespace Ingenerator\Warden\UI\Kohana\Entity;

/**
 * Use this interface to mark a user entity that wants to track when / how often it logged in
 *
 * This will be updated by KohanaUserSession on each login including by password reset, activation
 * etc.
 *
 * @package Ingenerator\Warden\UI\Kohana\Entity
 */
interface LastLoginTrackingUser
{
    /**
     * @param \DateTimeImmutable $now
     *
     * @see \Ingenerator\Warden\UI\Kohana\UserSession\KohanaUserSession::login()
     *
     * @return void
     */
    public function markLoggedInAt(\DateTimeImmutable $now);
}
