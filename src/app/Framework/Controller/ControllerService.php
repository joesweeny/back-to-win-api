<?php

namespace BackToWin\Framework\Controller;

use Chief\CommandBus;
use Psr\Log\LoggerInterface;

trait ControllerService
{
    /**
     * @var CommandBus
     */
    private $bus;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ControllerService constructor.
     * @param CommandBus $bus
     * @param LoggerInterface $logger
     */
    public function __construct(CommandBus $bus, LoggerInterface $logger)
    {
        $this->bus = $bus;
        $this->logger = $logger;
    }
}
