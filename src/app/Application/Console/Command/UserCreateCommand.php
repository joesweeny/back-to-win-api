<?php

namespace GamePlatform\Application\Console\Command;

use Chief\CommandBus;
use GamePlatform\Boundary\User\Command\CreateUserCommand;
use GamePlatform\Framework\Exception\UserCreationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserCreateCommand extends Command
{
    /**
     * @var CommandBus
     */
    private $bus;

    public function __construct(CommandBus $bus)
    {
        parent::__construct();
        $this->bus = $bus;
    }

    protected function configure()
    {
        $this->setName('user:create')
            ->setDescription('Create a new User')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('email', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED)
            ->addArgument('currency', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = new SymfonyStyle($input, $output);

        try {
            $command = new CreateUserCommand(
                $input->getArgument('username'),
                $input->getArgument('email'),
                $input->getArgument('password'),
                $input->getArgument('currency')
            );

            $user = $this->bus->execute($command);

            $response->success("User created with ID {$user->id}");
        } catch (UserCreationException $e) {
            $response->error($e->getMessage());
        }
    }
}
