<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Message\Authentication;


use Ingenerator\Pigeonhole\Message;

class LogoutSuccessMessage extends Message
{
    public function __construct()
    {
        parent::__construct(
            'You have logged out',
            'Come back soon!',
            Message::SUCCESS
        );
    }


}
