<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Warden\UI\Kohana\Notification;


use Ingenerator\KohanaExtras\Message\KohanaMessageProvider;
use Ingenerator\Warden\Core\Interactor\EmailVerificationRequest;
use Ingenerator\Warden\Core\Notification\ConfirmationRequiredNotification;
use Ingenerator\Warden\Core\Notification\UserNotification;
use Ingenerator\Warden\Core\Notification\UserNotificationMailer;
use Ingenerator\Warden\UI\Kohana\Notification\SymfonyMailerNotificationMailer;
use InvalidArgumentException;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;
use function json_decode;
use function json_encode;

class SymfonyMailerNotificationMailerTest extends TestCase
{
    protected array $config = [
        'email_sender'      => 'foo@warden.net',
        'email_sender_name' => 'Warden Emailer',
    ];

    protected SpyingSymfonyMailer $mailer;

    /**
     * @var JsonKohanaMessaageProviderStub
     */
    protected $messages;

    public function test_it_is_initialisable()
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf(SymfonyMailerNotificationMailer::class, $subject);
        $this->assertInstanceOf(UserNotificationMailer::class, $subject);
    }

    public function test_it_throws_on_unsupported_notification_type()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->newSubject()->sendWardenNotification(
            $this->getMockBuilder(UserNotification::class)->disableOriginalConstructor()->getMock()
        );
    }

    public function test_it_sends_email_to_notification_recipient()
    {
        $this->sendConfirmationRequiredWith(['recipient' => 'foo@bar.com']);
        $mail = $this->mailer->assertSentOne();
        $this->assertEquals([new Address('foo@bar.com', '')], $mail->getTo());
    }

    public function test_it_sends_email_from_configured_sender()
    {
        $this->config = [
            'email_sender'      => 'warden-sendWardenNotification@mail.net',
            'email_sender_name' => 'Mail Warden',
        ];
        $this->sendConfirmationRequiredWith(['recipient' => 'foo@bar.com']);
        $mail = $this->mailer->assertSentOne();
        $this->assertEquals([new Address('warden-sendWardenNotification@mail.net', 'Mail Warden')], $mail->getFrom());
    }

    public function test_it_sets_subject_from_message_provider_for_notification()
    {
        $this->sendConfirmationRequiredWith(['recipient' => 'foo@bar.com']);
        $mail = $this->mailer->assertSentOne();
        $this->messages->assertIsMessage(
            'user_notification_mail',
            'confirm_required.register.subject',
            [],
            $mail->getSubject()
        );
    }

    public function test_it_provides_notification_details_and_messages_to_view_model()
    {
        $this->markTestIncomplete('Support branded user notification emails');
    }

    public function test_it_renders_message_as_first_part_of_text_content()
    {
        $this->sendConfirmationRequiredWith(['continuation_url' => '/reset?foo=blah']);
        $mail    = $this->mailer->assertSentOne();
        $message = $this->assertHasMessageText($mail);

        $this->messages->assertIsMessage(
            'user_notification_mail',
            'confirm_required.register.message',
            [
                '%continuation_url%' => \URL::site('/reset?foo=blah', TRUE),
            ],
            \strtok($message, \PHP_EOL)
        );
    }

    protected function assertHasMessageText(Email $mail): string
    {
        $this->assertNotEmpty($mail->getTextBody(), 'Mail has no text body');

        return $mail->getTextBody();
    }

    /**
     * @param $values
     */
    protected function sendConfirmationRequiredWith($values)
    {
        $values       = \array_merge(
            [
                'recipient'         => 'foo@bar.com',
                'action'            => EmailVerificationRequest::REGISTER,
                'continuation_url' => '/reset?fo=blash',
            ],
            $values
        );
        $notification = new ConfirmationRequiredNotification(
            $values['recipient'],
            $values['action'],
            $values['continuation_url']
        );
        $this->newSubject()->sendWardenNotification($notification);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->mailer   = new SpyingSymfonyMailer;
        $this->messages = new JsonKohanaMessaageProviderStub;

    }

    protected function newSubject(): SymfonyMailerNotificationMailer
    {
        return new SymfonyMailerNotificationMailer($this->mailer, $this->messages, $this->config);
    }

}

class SpyingSymfonyMailer implements MailerInterface
{
    /**
     * @var Email[]
     */
    protected $mails = [];

    public function __construct() { }

    public function send(RawMessage $message, Envelope $envelope = null): void
    {
        $this->mails[] = $message;
    }

    public function assertSentOne(): Email
    {
        Assert::assertCount(1, $this->mails);
        $message = $this->mails[0];
        Assert::assertInstanceOf(Email::class, $message);

        return $message;
    }

}

class JsonKohanaMessaageProviderStub extends KohanaMessageProvider
{
    public function __construct() { }

    public function message($file, $path, array $params = [], $default = NULL)
    {
        return json_encode(
            [
                'file'   => $file,
                'path'   => $path,
                'params' => $params,
            ]
        );
    }

    public function assertIsMessage($file, $path, array $params, $string)
    {
        $values = json_decode($string, TRUE);
        Assert::assertEquals(
            [
                'file'   => $file,
                'path'   => $path,
                'params' => $params,
            ],
            $values
        );
    }
}
