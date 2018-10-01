<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Message\ChangeEmail;


use Ingenerator\Pigeonhole\Message;
use Ingenerator\Pigeonhole\Message\KohanaMessage;

class InvalidVerifyEmailLinkMessage extends KohanaMessage
{
    public function __construct()
    {
        parent::__construct(
            'warden_flash_messages',
            'change_email.invalid_verify_link',
            [],
            Message::DANGER
        );
    }

}
