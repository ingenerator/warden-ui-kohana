<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Message\Authentication;


use Ingenerator\Pigeonhole\Message;

class PasswordResetSuccessMessage extends Message\KohanaMessage
{
    public function __construct($email)
    {
        parent::__construct(
            'warden_flash_messages',
            'authentication.password_reset_success',
            ['%email%' => $email],
            Message::SUCCESS
        );
    }
}

