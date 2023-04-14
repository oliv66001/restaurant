<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class SendMailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer) {
        $this->mailer = $mailer;
    }

    public function send(
            string $from,
            string $to,
            string $subject,
            string $template,
            array $context = [],
            bool $addEmailsPrefix = true // Ajoutez ce paramètre ici
        ): void
    {
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate(($addEmailsPrefix ? 'emails/' : '') . $template . '.html.twig') // Modifiez cette ligne
            ->context($context);

        $this->mailer->send($email);
    }
}
