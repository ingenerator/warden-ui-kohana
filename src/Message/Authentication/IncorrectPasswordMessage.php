<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Message\Authentication;


use Ingenerator\Pigeonhole\Message;

class IncorrectPasswordMessage extends Message
{
    public function __construct($email)
    {
        parent::__construct(
            'That was not the correct password',
            'We\'ve emailed a link to '.$email.' for you to reset it.',
            Message::DANGER
        );
    }

}
