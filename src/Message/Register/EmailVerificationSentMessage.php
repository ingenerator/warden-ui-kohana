<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Message\Register;


use Ingenerator\Pigeonhole\Message;

class EmailVerificationSentMessage extends Message
{
    public function __construct($email)
    {
        parent::__construct(
            'Verification Email Sent',
            "Please check your inbox at $email and click the link to continue",
            static::SUCCESS
        );
    }
}
