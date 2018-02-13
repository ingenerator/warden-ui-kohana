<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Message\Authentication;


use Ingenerator\Pigeonhole\Message;

class PasswordResetSuccessMessage extends Message
{
    public function __construct($email)
    {
        parent::__construct(
            'Password reset successful',
            'The password for '.$email.' was successfully changed, you are now logged in',
            Message::SUCCESS
        );
    }
}

