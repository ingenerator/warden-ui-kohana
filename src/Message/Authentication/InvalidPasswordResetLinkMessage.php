<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Message\Authentication;


use Ingenerator\Pigeonhole\Message;

class InvalidPasswordResetLinkMessage extends Message\KohanaMessage
{
    public function __construct()
    {
        parent::__construct(
            'warden_flash_messages',
            'authentication.invalid_password_reset_link',
            [],
            Message::DANGER
        );
    }
}
