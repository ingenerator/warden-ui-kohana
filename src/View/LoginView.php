<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\View;


use Ingenerator\KohanaView\ViewModel\PageLayoutView;
use Ingenerator\Warden\Core\Support\UrlProvider;

/**
 * @property-read string $login_url
 */
class LoginView extends AbstractFormView
{
    /**
     * @var UrlProvider
     */
    protected $url_provider;

    public function __construct(PageLayoutView $page, UrlProvider $url_provider)
    {
        parent::__construct($page);
        $this->url_provider = $url_provider;
    }

    protected function var_login_url(){
        return $this->url_provider->getLoginUrl();
    }
}
