<?php

namespace GamePlatform\Application\Console\Command;

use GamePlatform\Bootstrap\Config;
use GamePlatform\Domain\Bank\Bank;
use GamePlatform\Domain\Bank\Exception\BankingException;
use GamePlatform\Domain\User\UserOrchestrator;
use GamePlatform\Domain\UserPurse\Entity\UserPurseTransaction;
use GamePlatform\Domain\UserPurse\UserPurseOrchestrator;
use GamePlatform\Framework\Calculation\Calculation;
use GamePlatform\Framework\Exception\NotFoundException;
use GamePlatform\Framework\Uuid\Uuid;
use Interop\Container\ContainerInterface;
use Money\Currency;
use Money\Money;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserFundsAddCommand extends Command
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var UserOrchestrator
     */
    private $orchestrator;
    /**
     * @var Bank
     */
    private $bank;
    /**
     * @var UserPurseOrchestrator
     */
    private $purseOrchestrator;

    public function __construct(
        ContainerInterface $container,
        UserOrchestrator $orchestrator,
        Bank $bank,
        UserPurseOrchestrator $purseOrchestrator
    ) {
        parent::__construct();
        $this->container = $container;
        $this->orchestrator = $orchestrator;
        $this->bank = $bank;
        $this->purseOrchestrator = $purseOrchestrator;
    }

    protected function configure()
    {
        $this->setName('user:funds-add')
            ->setDescription('Add fake funds to a User bank account in a non production environment')
            ->addArgument('user_id', InputArgument::REQUIRED)
            ->addArgument('amount', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = new SymfonyStyle($input, $output);

        if ($this->container->get(Config::class)->get('bank.user.driver') !== 'redis') {
            $response->error('Bank configuration is not correct to deposit fake funds');
            return;
        }

        try {
            $user = $this->orchestrator->getUserById(new Uuid($input->getArgument('user_id')));

            $money = new Money($input->getArgument('amount'), new Currency('FAKE'));

            $this->addFunds($user->getId(), $money);

            $this->updateUserPurse($user->getId(), $money, 'Adding funds via console command');

            $response->success("Successfully added funds to User {$user->getId()} bank");
        } catch (\InvalidArgumentException | NotFoundException $e) {
            $response->error($e->getMessage());
        }
    }

    /**
     * Open an account with Money provided, if account exists add Money to already opened account
     *
     * @param Uuid $userId
     * @param Money $money
     */
    private function addFunds(Uuid $userId, Money $money): void
    {
        try {
            $this->bank->openAccount($userId, $money);
        } catch (BankingException $e) {
            $this->bank->deposit($userId, $money);
        }
    }

    /**
     * @param Uuid $userId
     * @param Money $money
     * @param string $message
     * @throws NotFoundException
     * @return void
     */
    private function updateUserPurse(Uuid $userId, Money $money, string $message): void
    {
        $this->purseOrchestrator->createTransaction(
            (new UserPurseTransaction())
                ->setUserId($userId)
                ->setTotal($money)
                ->setCalculation(Calculation::ADD())
                ->setDescription($message)
        );

        $purse = $this->purseOrchestrator->getUserPurse($userId);

        $purse = $purse->addMoney($money);

        $this->purseOrchestrator->updateUserPurse($purse);
    }
}
