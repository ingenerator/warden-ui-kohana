<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Warden\UI\Kohana\Notification;


use Ingenerator\KohanaWrapper\KohanaMessageProvider;
use Ingenerator\Warden\Core\Notification\ConfirmationRequiredNotification;
use Ingenerator\Warden\Core\Notification\UserNotification;
use Ingenerator\Warden\Core\Notification\UserNotificationMailer;

class SwiftNotificationMailer implements UserNotificationMailer
{
    const MESSAGE_FILE = 'user_notification_mail';
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var array
     */
    protected $email_config;

    /**
     * @var KohanaMessageProvider
     */
    protected $messages;

    public function __construct(\Swift_Mailer $mailer, KohanaMessageProvider $messages, array $email_config)
    {
        $this->mailer       = $mailer;
        $this->email_config = $email_config;
        $this->messages     = $messages;
    }

    public function send(UserNotification $notification)
    {
        if ($notification instanceof ConfirmationRequiredNotification) {
            $this->sendConfirmationRequired($notification);
        } else {
            throw new \InvalidArgumentException('Unsupported notification type '.get_class($notification));
        }
    }

    protected function sendConfirmationRequired(ConfirmationRequiredNotification $notification)
    {
        $mail = $this->newEmailToRecipient($notification);

        $base_path = 'confirm_required.'.$notification->getAction();
        $mail->setSubject($this->messages->message(self::MESSAGE_FILE, $base_path.'.subject'));
        $mail->addPart(
            $this->messages->message(
                self::MESSAGE_FILE,
                $base_path.'.message',
                ['%continuation_url%' => \URL::site($notification->getContinuationUrl(), TRUE)]
            ),
            'text/plain'
        );

        $this->mailer->send($mail);
    }

    /**
     * @return \Swift_Message
     */
    protected function newEmailToRecipient(UserNotification $notification)
    {
        $mail = \Swift_Message::newInstance();
        $mail->setFrom($this->email_config['email_sender'], $this->email_config['email_sender_name']);
        $mail->setTo($notification->getRecipientEmail());

        return $mail;
    }


}
