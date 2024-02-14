<?php
/**
 * @author    Craig Gosman <craig@ingenerator.com>
 * @licence   proprietary
 */

namespace integration;

use Ingenerator\KohanaExtras\DependencyFactory\SymfonyValidationFactory;
use Ingenerator\Warden\Core\Interactor\UserRegistrationRequest;
use Ingenerator\Warden\Validator\Symfony\SymfonyValidator;
use PHPUnit\Framework\TestCase;

class SymfonyValidatorAnnotationTest extends TestCase
{
    public function test_symfony_validator_is_configured_to_read_doctrine_annotations(): void
    {
        $warden_validator = new SymfonyValidator(SymfonyValidationFactory::buildValidator());
        $registration_req = UserRegistrationRequest::fromArray(['email' => '', 'password' => '12345678']);
        $this->assertSame(
            ['email' => 'This value should not be blank.'],
            $warden_validator->validate($registration_req)
        );
    }
}
