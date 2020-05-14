<?php


namespace Ingenerator\Warden\UI\Kohana\Logging;


use Dependencies;
use Ingenerator\KohanaExtras\DependencyContainer\DependencyContainer;
use Ingenerator\PHPUtils\Logging\LogMetadataProvider;
use Ingenerator\PHPUtils\Object\SingletonNotInitialisedException;
use Ingenerator\Warden\Core\UserSession\UserSession;
use function class_exists;

class WardenUserLogMetadataProvider implements LogMetadataProvider
{

    /**
     * @var string
     */
    protected $dependencies_class;

    /**
     * @var string
     */
    protected $session_dependency_key;

    public function __construct(
        string $dependencies_class = Dependencies::class,
        string $session_dependency_key = 'warden.user_session.session'
    ) {
        $this->dependencies_class     = $dependencies_class;
        $this->session_dependency_key = $session_dependency_key;
    }

    public function getMetadata(): array
    {
        // If the session is not started but headers have been sent (e.g. in the request logger when the request
        // got a 404) then attempting to access the warden user session instance will attempt to start the session. That
        // will then cause a Session_Exception because the session cookie etc cannot be configured once the headers are
        // sent. Therefore we need to fail early and log the inability to access the session. This does mean that
        // authenticated user 404s will not be associated with them in the logs, but the alternative would be to start
        // the session on every request which could increase latency and response time on bots and other obvious 404s.
        if ( ! $this->canAccessSession()) {
            return ['context' => ['user' => '#SESSION-INACCESSIBLE#']];
        }

        if ( ! class_exists($this->dependencies_class, FALSE)) {
            // This is expected if kohana has not yet finished bootstrapping, no need to log this as a separate error
            return ['context' => ['user' => '#ERR# No class '.$this->dependencies_class]];
        }

        try {
            $deps = \call_user_func($this->dependencies_class.'::instance');
        } catch (SingletonNotInitialisedException $e) {
            // This is expected if kohana has not yet finished bootstrapping, no need to log this as a separate error
            return ['context' => ['user' => '#ERR# '.$this->dependencies_class.' '.$e->getMessage()]];
        }

        // If we get as far as here then the app is bootstrapped OK, so if the dependency class is not
        // the expected type, or the user session service does not exist or has the wrong type, that is
        // a system-level error (wrong arguments to this function?) and therefore should be reported.
        $session = $this->getUserSession($deps, $this->session_dependency_key);

        return [
            'context' => [
                'user' => $this->getUserString($session),
            ],
        ];
    }

    protected function getUserSession(DependencyContainer $deps, string $session_dependency_key): UserSession
    {
        return $deps->get($session_dependency_key);
    }

    protected function getUserString(UserSession $session)
    {
        return $session->isAuthenticated() ? $session->getUser()->getEmail() : 'guest';
    }

    protected function canAccessSession(): bool
    {
        switch ($this->sessionStatus()) {
            case PHP_SESSION_ACTIVE:
                return TRUE;

            case PHP_SESSION_DISABLED:
                return FALSE;

            case PHP_SESSION_NONE:
            default:
                return ! $this->headersSent();
        }
    }

    protected function sessionStatus(): int
    {
        return \session_status();
    }

    protected function headersSent(): bool
    {
        return \headers_sent();
    }
}
