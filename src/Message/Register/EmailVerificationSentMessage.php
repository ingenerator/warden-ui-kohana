<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Message\Register;


use Ingenerator\Pigeonhole\Message\KohanaMessage;
use Ingenerator\Pigeonhole\Message;

class EmailVerificationSentMessage extends KohanaMessage
{
    public function __construct($email)
    {
        parent::__construct(
            'warden_flash_messages',
            'register.email_verification_sent',
            ['%email%' => $email],
            Message::SUCCESS
        );
    }
}
