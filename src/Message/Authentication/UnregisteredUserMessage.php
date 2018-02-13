<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Message\Authentication;


use Ingenerator\Pigeonhole\Message;

class UnregisteredUserMessage extends Message
{
    public function __construct($email)
    {
        parent::__construct(
            'Please register to continue',
            'The email address '.$email.' is not registered.',
            Message::WARNING
        );
    }
    
}
