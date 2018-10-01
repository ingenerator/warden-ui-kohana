<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Message\ChangeEmail;


use Ingenerator\Pigeonhole\Message;
use Ingenerator\Pigeonhole\Message\KohanaMessage;

class EmailChangedMessage extends KohanaMessage
{
    public function __construct($email)
    {
        parent::__construct(
            'warden_flash_messages',
            'change_email.email_changed',
            ['%email%' => $email],
            Message::SUCCESS
        );
    }

}
