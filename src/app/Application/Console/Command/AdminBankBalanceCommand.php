<?php

namespace GamePlatform\Application\Console\Command;

use GamePlatform\Bootstrap\Config;
use GamePlatform\Domain\Admin\Bank\Bank;
use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AdminBankBalanceCommand extends Command
{
    /**
     * @var Bank
     */
    private $bank;
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container, Bank $bank)
    {
        parent::__construct();
        $this->bank = $bank;
        $this->container = $container;
    }

    protected function configure()
    {
        $this->setName('admin:bank-balance');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = new SymfonyStyle($input, $output);

        if ($this->container->get(Config::class)->get('bank.admin.driver') !== 'redis') {
            $response->error('Bank configuration is not correct to view Admin bank balance');
            return;
        }

        $balance = $this->bank->getBalance();

        $response->success($balance->getCurrency()->getCode() . ' ' . (float) $balance->getAmount() / 100);
    }
}
