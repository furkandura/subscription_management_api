<?php

namespace App\Command;

use App\Service\QueService;
use App\Service\SubscriptionService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'subscription:validate',
    description: 'Verifies and updates expired but not canceled subscriptions.',
)]
class SubscriptionValidateCommand extends Command
{
    public function __construct(
        private QueService $queService,
        private SubscriptionService $subscriptionService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Process Started | ' . date("d.m.Y H:i:s"));

        $subscriptions = $this->subscriptionService->getExpiredSubscriptions();

        foreach ($subscriptions as $subscription) {
            try {
                $this->queService->dispatchSubscriptionValidate(['id' => $subscription->getId()]);
                $io->writeln('Subscription('.$subscription->getId().') added to que.');
            } catch (\Exception $e) {
                $io->writeln('Subscription('.$subscription->getId().') failed to add to que.');
            }
        }

        $io->success("Process Complete | ".date("d.m.Y H:i:s"));

        return Command::SUCCESS;
    }
}
