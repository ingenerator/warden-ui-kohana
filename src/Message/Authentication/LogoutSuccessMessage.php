<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Message\Authentication;


use Ingenerator\Pigeonhole\Message;

class LogoutSuccessMessage extends Message\KohanaMessage
{
    public function __construct()
    {
        parent::__construct(
            'warden_flash_messages',
            'authentication.logout_success',
            [],
            Message::SUCCESS
        );
    }


}
