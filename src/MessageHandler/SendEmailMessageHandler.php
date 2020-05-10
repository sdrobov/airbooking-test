<?php

namespace App\MessageHandler;

use App\Message\SendEmailMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;

final class SendEmailMessageHandler implements MessageHandlerInterface
{
    /** @var MailerInterface */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(SendEmailMessage $message)
    {
        $emailMessage = new Email();
        $emailMessage->to([$message->getUser()->getEmail()]);
        $emailMessage->subject($message->getSubject());
        $emailMessage->text($message->getMessage());

        $this->mailer->send($emailMessage);
    }
}
