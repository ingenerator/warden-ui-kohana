<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Message\Authentication;


use Ingenerator\Pigeonhole\Message;
use Ingenerator\Pigeonhole\Message\KohanaMessage;

class LoginEmailVerificationFailedMessage extends KohanaMessage
{
    public function __construct($email)
    {
        parent::__construct(
            'warden_flash_messages',
            'authentication.email_verification_failed',
            ['%email%' => $email],
            Message::DANGER
        );
    }

}
