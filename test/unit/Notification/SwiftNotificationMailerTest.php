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
use Ingenerator\Warden\UI\Kohana\Notification\SwiftNotificationMailer;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Swift_Message;
use Swift_Mime_Message;

class SwiftNotificationMailerTest extends TestCase
{
    protected $config = [
        'email_sender'      => 'foo@warden.net',
        'email_sender_name' => 'Warden Emailer',
    ];

    /**
     * @var SpyingSwiftMailer
     */
    protected $mailer;

    /**
     * @var JsonKohanaMessaageProviderStub
     */
    protected $messages;

    public function test_it_is_initialisable()
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf('Ingenerator\Warden\UI\Kohana\Notification\SwiftNotificationMailer', $subject);
        $this->assertInstanceOf('Ingenerator\Warden\Core\Notification\UserNotificationMailer', $subject);
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
        $this->assertSame(['foo@bar.com' => NULL], $mail->getTo());
    }

    public function test_it_sends_email_from_configured_sender()
    {
        $this->config = [
            'email_sender'      => 'warden-sendWardenNotification@mail.net',
            'email_sender_name' => 'Mail Warden',
        ];
        $this->sendConfirmationRequiredWith(['recipient' => 'foo@bar.com']);
        $mail = $this->mailer->assertSentOne();
        $this->assertSame(['warden-sendWardenNotification@mail.net' => 'Mail Warden'], $mail->getFrom());
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
        $message = $this->assertHasMessagePart($mail, 'text/plain');

        $this->messages->assertIsMessage(
            'user_notification_mail',
            'confirm_required.register.message',
            [
                '%continuation_url%' => \URL::site('/reset?foo=blah', TRUE),
            ],
            \strtok($message, \PHP_EOL)
        );
    }


    /**
     * @param $mail
     * @param $content_type
     *
     * @return mixed
     */
    protected function assertHasMessagePart(\Swift_Message $mail, $content_type)
    {
        $this->assertNotEmpty($mail->getChildren(), 'Mail has no body parts');
        $found_parts = [];
        foreach ($mail->getChildren() as $part) {
            if ($part->getContentType() === $content_type) {
                return $part->getBody();
            }
            $found_parts[] = "'".$part->getContentType()."'";
        }

        $this->fail("No '$content_type' part in ".\implode($found_parts));
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
        $this->mailer   = new SpyingSwiftMailer;
        $this->messages = new JsonKohanaMessaageProviderStub;

    }

    protected function newSubject()
    {
        return new SwiftNotificationMailer($this->mailer, $this->messages, $this->config);
    }

}

class SpyingSwiftMailer extends \Swift_Mailer
{
    /**
     * @var \Swift_Mime_Message[]
     */
    protected $mails = [];

    public function __construct() { }

    public function send(Swift_Mime_Message $message, &$failedRecipients = NULL)
    {
        $this->mails[] = $message;
    }

    /**
     * @return Swift_Message
     */
    public function assertSentOne()
    {
        \PHPUnit\Framework\Assert::assertCount(1, $this->mails);
        $message = $this->mails[0];
        \PHPUnit\Framework\Assert::assertInstanceOf(Swift_Message::class, $message);

        return $message;
    }

}

class JsonKohanaMessaageProviderStub extends KohanaMessageProvider
{
    public function __construct() { }

    public function message($file, $path, array $params = [], $default = NULL)
    {
        return \json_encode(
            [
                'file'   => $file,
                'path'   => $path,
                'params' => $params,
            ]
        );
    }

    public function assertIsMessage($file, $path, array $params, $string)
    {
        $values = \json_decode($string, TRUE);
        \PHPUnit\Framework\Assert::assertEquals(
            [
                'file'   => $file,
                'path'   => $path,
                'params' => $params,
            ],
            $values
        );
    }
}
