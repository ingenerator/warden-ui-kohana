<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Message\Register;


use Ingenerator\Pigeonhole\Message\KohanaMessage;
use Ingenerator\Pigeonhole\Message;

class InvalidRegisterLinkMessage extends KohanaMessage
{
    public function __construct()
    {
        parent::__construct(
            'warden_flash_messages',
            'register.invalid_register_link',
            [],
            Message::DANGER
        );
    }

}
