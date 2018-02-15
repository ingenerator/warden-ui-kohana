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
            "$email is already registered - please login below.",
            Message::WARNING
        );
    }

}
