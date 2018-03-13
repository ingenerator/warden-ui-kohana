<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Dummy;

use Ingenerator\Warden\Core\Entity\User;
use Ingenerator\Warden\Core\Support\UrlProvider;

class ReverseRouteURLProvider implements UrlProvider
{
    public function getAfterLoginUrl(User $user)
    {
        return $this->getDefaultUserHomeUrl($user);
    }

    public function getDefaultUserHomeUrl(User $user)
    {
        return '/profile';
    }

    public function getAfterLogoutUrl()
    {
        return $this->getLoginUrl();
    }

    public function getLogoutUrl()
    {
        return \Route::url('warden-logout');
    }

    public function getLoginUrl($email = NULL)
    {
        $url = \URL::site(\Route::url('warden-login'));
        if ($email) {
            return $url.'?'.http_build_query(['email' => $email]);
        } else {
            return $url;
        }
    }

    public function getAfterVerifyEmailSentUrl()
    {
        return '/';
    }

    public function getCompletePasswordResetUrl(array $params)
    {
        return \URL::site(\Route::url('warden-reset')).'?'.http_build_query($params);
    }

    public function getCompleteRegistrationUrl(array $params)
    {
        return \URL::site(\Route::url('warden-register')).'?'.http_build_query($params);
    }

    public function getRegisterVerifyEmailUrl($email = NULL)
    {
        $url = \URL::site(\Route::url('warden-verify-email'));
        if ($email) {
            return $url.'?'.http_build_query(['email' => $email]);
        } else {
            return $url;
        }
    }

}
