<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Message\Authentication;


use Ingenerator\Pigeonhole\Message;

class InvalidPasswordResetLinkMessage extends Message
{
    public function __construct()
    {
        parent::__construct(
            'Password reset failed',
            'The link you clicked was invalid or expired. Please try to log in again and we\'ll send a new email if required',
            Message::DANGER
        );
    }
}
