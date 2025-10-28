<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\View;


use Ingenerator\KohanaView\ViewModel\NestedParentView;
use Ingenerator\Warden\Core\Support\UrlProvider;

class LoginView extends AbstractFormView
{
    public string $login_url {
        get => $this->url_provider->getLoginUrl();
    }
    /**
     * @var UrlProvider
     */
    protected $url_provider;

    public function __construct(NestedParentView $page, UrlProvider $url_provider)
    {
        parent::__construct($page);
        $this->url_provider = $url_provider;
    }
}
