<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @author    Craig Gosman <craig@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Notification;


use Ingenerator\KohanaExtras\Message\KohanaMessageProvider;
use Ingenerator\Warden\Core\Notification\ConfirmationRequiredNotification;
use Ingenerator\Warden\Core\Notification\UserNotification;
use Ingenerator\Warden\Core\Notification\UserNotificationMailer;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class SymfonyMailerNotificationMailer implements UserNotificationMailer
{
    const MESSAGE_FILE = 'user_notification_mail';

    public function __construct(
        protected MailerInterface       $mailer,
        protected KohanaMessageProvider $messages,
        protected array                 $email_config
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendWardenNotification(UserNotification $notification): void
    {
        if ($notification instanceof ConfirmationRequiredNotification) {
            $this->sendConfirmationRequired($notification);
        } else {
            throw new \InvalidArgumentException('Unsupported notification type '.\get_class($notification));
        }
    }

    /**
     * @throws TransportExceptionInterface
     */
    protected function sendConfirmationRequired(ConfirmationRequiredNotification $notification): void
    {
        $mail = $this->newEmailToRecipient($notification);

        $base_path = 'confirm_required.' . $notification->getAction();
        $mail->subject($this->messages->message(self::MESSAGE_FILE, $base_path . '.subject'));
        $mail->text(
            $this->messages->message(
                self::MESSAGE_FILE,
                $base_path . '.message',
                ['%continuation_url%' => \URL::site($notification->getContinuationUrl(), TRUE)]
            )
        );

        $this->mailer->send($mail);
    }

    protected function newEmailToRecipient(UserNotification $notification): Email
    {
        $mail = new Email();
        $mail->from(new Address($this->email_config['email_sender'], $this->email_config['email_sender_name']));
        $mail->to(new Address($notification->getRecipientEmail()));

        return $mail;
    }


}
