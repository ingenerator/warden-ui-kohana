<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Routing;


use Ingenerator\KohanaExtras\Routing\HttpMethodRoute;
use Ingenerator\Warden\Core\Entity\User;
use Ingenerator\Warden\Core\Support\UrlProvider;

/**
 * Defines and manages forwards-and-backwards routing between URLs and warden controllers.
 *
 * The default set of actions, urls and handling controllers is set in config/warden.php - override
 * these values as required to send people to different URLs and/or to have those URLs handled
 * by different controllers.
 *
 * [
 *    'url-routing' => [
 *       'after-login' => [
 *         'url'              => '/welcome'           # Define a custom URL to send people to
 *         'route_controller' => FALSE                # Don't map a route for this controller, it's already routed elsewhere
 *         // OR
 *         'route_controller' => MyController::class  # Also define an explicit route for this controller from /welcome
 *       ]
 *    ]
 * ]
 *
 * To actually register the routes, get an instance in routes.php and call ->registerGlobalRoutes();
 *
 * @package Ingenerator\Warden\UI\Kohana\Routing
 */
class WardenConfigBasedRouter implements UrlProvider
{
    /**
     * @var array
     */
    protected $url_config;

    /**
     * @param array $url_config the list of urls and controllers to route
     */
    public function __construct(array $url_config)
    {
        $this->url_config = $url_config;
    }

    public function getAfterLoginUrl(User $user)
    {
        return $this->requireConfigUrl('after-login');
    }

    public function getAfterLogoutUrl()
    {
        return $this->requireConfigUrl('after-logout');
    }

    public function getAfterVerifyEmailSentUrl()
    {
        return $this->requireConfigUrl('after-verify-email');
    }

    /**
     * @return mixed
     */
    public function getChangeEmailUrl()
    {
        return $this->requireConfigUrl('change-email');
    }

    public function getChangePasswordUrl()
    {
        return $this->requireConfigUrl('change-password');
    }

    public function getCompleteActivationUrl(array $params)
    {
        return $this->requireConfigUrl('complete-activation', $params);
    }


    public function getCompleteChangeEmailUrl(array $params)
    {
        return $this->requireConfigUrl('complete-change-email', $params);
    }

    public function getCompletePasswordResetUrl(array $params)
    {
        return $this->requireConfigUrl('complete-password-reset', $params);
    }

    public function getCompleteRegistrationUrl(array $params)
    {
        return $this->requireConfigUrl('complete-registration', $params);
    }

    public function getDefaultUserHomeUrl(User $user)
    {
        return $this->requireConfigUrl('default-user-home');
    }

    public function getLoginUrl($email = NULL)
    {
        return $this->requireConfigUrl('login', \array_filter(['email' => $email]));
    }

    public function getLogoutUrl()
    {
        return $this->requireConfigUrl('logout');
    }

    public function getRegisterVerifyEmailUrl($email = NULL)
    {
        return $this->requireConfigUrl('register-verify-email', \array_filter(['email' => $email]));
    }

    protected function requireConfigUrl($key, array $params = [])
    {
        if ( ! isset($this->url_config[$key]['url'])) {
            // You need to define this key if you're using this URL, or extend this class to send the
            // user somewhere useful if this happens.
            throw new \OutOfBoundsException('Unexpected access to empty URL `'.$key.'`');
        }

        $base_url = \URL::site($this->url_config[$key]['url']);
        if ($params) {
            return $base_url.'?'.\http_build_query($params);
        } else {
            return $base_url;
        }

    }

    /**
     * Defines routes for all the URLs that are defined in config
     */
    public function registerGlobalRoutes()
    {
        foreach ($this->url_config as $key => $config) {
            if ($config['route_controller'] === FALSE) {
                continue;
            }

            if ( ! $config['url']) {
                throw new \InvalidArgumentException(
                    'Cannot define route `'.$key.'` to controller `'.$config['route_controller'].'` with no URL'
                );
            }

            HttpMethodRoute::createExplicit(
                'warden-'.$key,
                \ltrim($config['url'], '/'),
                $config['route_controller']
            );
        }
    }
}
