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

    public function __construct(
        NestedParentView $page,
        protected readonly UrlProvider $url_provider
    )
    {
        parent::__construct($page);
    }
}
