<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */
namespace Ingenerator\Warden\UI\Kohana\Dummy;

use Ingenerator\Tokenista;
use Ingenerator\Warden\Core\Support\EmailConfirmationTokenService;

class TokenistaTokenService implements EmailConfirmationTokenService
{
    /**
     * @var Tokenista
     */
    protected $tokenista;

    public function __construct(Tokenista $tokenista)
    {
        $this->tokenista = $tokenista;
    }

    public function createToken($params)
    {
        return $this->tokenista->generate(\Date::DAY * 5, $params);
    }

    public function isValid($token, $params)
    {
        return $this->tokenista->isValid($token, $params);
    }

}
