<?php

namespace App\Command;

use App\Service\SendMailService;
use App\Repository\CalendarRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:send-reminder-emails')]
class SendReminderEmailCommand extends Command
{

    private $calendarRepository;
    private $mailService;

    public function __construct(CalendarRepository $calendarRepository, SendMailService $mailService)
    {
        $this->calendarRepository = $calendarRepository;
        $this->mailService = $mailService;
    
        parent::__construct();
    }
    

    protected function configure(): void
    {
        $this->setDescription('Send reminder emails to users who have a reservation in 24 hours.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
       
$reservations = $this->calendarRepository->findReservationsStartingInNext24Hours();
error_log("Réservations trouvées : " . count($reservations));

foreach ($reservations as $reservation) {
    $user = $reservation->getName();
    error_log("Envoi d'un rappel à l'utilisateur : " . $user->getEmail());
    
            $this->mailService->send(
                'quai-antique@crocobingo.fr',
                $user->getEmail(),
                'Rappel de réservation',
                'emails/reminder_reservation',
                [
                    'reservation' => $reservation,
                    'user' => $user,
                ],
                false 
            );
        }

        $io->success('Les rappels de réservation ont été envoyés.');

        $output->writeln(sprintf('Sent reminder emails to %d users.', count($reservations)));

        return Command::SUCCESS;
    }
}
