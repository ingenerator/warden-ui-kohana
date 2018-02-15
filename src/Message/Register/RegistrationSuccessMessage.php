<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Message\Register;


use Ingenerator\Pigeonhole\Message;

class RegistrationSuccessMessage extends Message
{
    public function __construct()
    {
        parent::__construct(
            'Registration Successful',
            'You have successfully created an account.',
            Message::SUCCESS
        );
    }

}
