<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Message\Register;


use Ingenerator\Pigeonhole\Message\KohanaMessage;
use Ingenerator\Pigeonhole\Message;

class InvalidActivationLinkMessage extends KohanaMessage
{
    public function __construct()
    {
        parent::__construct(
            'warden_flash_messages',
            'register.invalid_activation_link',
            [],
            Message::DANGER
        );
    }

}
