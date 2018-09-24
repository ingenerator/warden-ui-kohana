<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Message\Register;


use Ingenerator\Pigeonhole\Message;

class ExistingUserRegistrationMessage extends Message\KohanaMessage
{
    public function __construct($email)
    {
        parent::__construct(
            'warden_flash_messages',
            'register.existing_user_registration',
            ['%email%' => $email],
            Message::WARNING
        );
    }

}
