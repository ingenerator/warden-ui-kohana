<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */
namespace Ingenerator\Warden\UI\Kohana\Dummy;

use Ingenerator\Warden\Core\Support\UrlProvider;

class ReverseRouteURLProvider implements UrlProvider
{
    public function getLoginUrl()
    {
        return \URL::site(\Route::url('warden-login'));
    }

    public function getRegistrationUrl()
    {
        return \URL::site(\Route::url('warden-register'));
    }

}
