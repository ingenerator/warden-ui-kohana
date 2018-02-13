<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Message\Register;


use Ingenerator\Pigeonhole\Message;

class ExistingUserRegistrationMessage extends Message
{
    public function __construct($email)
    {
        parent::__construct(
            'You\'re already registered',
            "You already have an account as $email - please login below.",
            Message::WARNING
        );
    }

}
