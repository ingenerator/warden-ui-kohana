<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Message\Authentication;


use Ingenerator\Pigeonhole\Message\KohanaMessage;
use Ingenerator\Pigeonhole\Message;

class IncorrectPasswordMessage extends KohanaMessage
{
    public function __construct($email)
    {
        parent::__construct(
            'warden_flash_messages',
            'authentication.incorrect_password',
            ['%email%' => $email],
            Message::DANGER
        );
    }

}
