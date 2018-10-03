<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Message\Authentication;


use Ingenerator\Pigeonhole\Message;
use Ingenerator\Pigeonhole\Message\KohanaMessage;

class LoginRateLimitedMessage extends KohanaMessage
{
    public function __construct()
    {
        parent::__construct(
            'warden_flash_messages',
            'authentication.login_rate_limited',
            [],
            Message::DANGER
        );
    }

}
