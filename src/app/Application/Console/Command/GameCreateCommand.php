<?php

namespace BackToWin\Application\Console\Command;

use BackToWin\Boundary\Game\Command\CreateGameCommand;
use Chief\CommandBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GameCreateCommand extends Command
{
    /** @var CommandBus */
    private $bus;

    /**
     * IngredientCreate constructor.
     * @param CommandBus $bus
     */
    public function __construct(CommandBus $bus)
    {
        parent::__construct();
        $this->bus = $bus;
    }

    protected function configure()
    {
        $this->setName('game:create')
            ->setDescription('Create a new Game')
            ->addArgument('type', InputArgument::REQUIRED, 'Must match a value of the GameType enum')
            ->addArgument('currency', InputArgument::REQUIRED)
            ->addArgument('buy_in', InputArgument::REQUIRED)
            ->addArgument('max', InputArgument::REQUIRED)
            ->addArgument('min', InputArgument::REQUIRED)
            ->addArgument('start', InputArgument::REQUIRED, 'In format 2018-07-23T00:00+00:00')
            ->addArgument('players', InputArgument::REQUIRED, 'Number of players for game');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = new SymfonyStyle($input, $output);

        try {
            $game = $this->bus->execute(new CreateGameCommand(
                $input->getArgument('type'),
                $input->getArgument('currency'),
                $input->getArgument('buy_in'),
                $input->getArgument('max'),
                $input->getArgument('min'),
                $input->getArgument('start'),
                $input->getArgument('players')
            ));

            $response->success("Game created with ID {$game->id}");
        } catch (\UnexpectedValueException | \InvalidArgumentException $e) {
            $response->error($e->getMessage());
        }
    }
}
