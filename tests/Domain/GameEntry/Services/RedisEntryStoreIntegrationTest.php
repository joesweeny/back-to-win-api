<?php

namespace BackToWin\Domain\GameEntry\Services;

use BackToWin\Bootstrap\Config;
use BackToWin\Domain\GameEntry\Entity\GameEntry;
use BackToWin\Domain\GameEntry\Exception\EntryFeeStoreException;
use BackToWin\Framework\Uuid\Uuid;
use BackToWin\Testing\Traits\UsesContainer;
use BackToWin\Testing\Traits\UsesTestRedisDatabase;
use Interop\Container\ContainerInterface;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Predis\Client;

class RedisEntryStoreIntegrationTest extends TestCase
{
    use UsesContainer,
        UsesTestRedisDatabase;

    /** @var  ContainerInterface */
    private $container;
    /** @var  EntryFeeStore */
    private $store;
    /** @var  Client */
    private $client;

    public function setUp()
    {
        $this->container  = $this->setRedisDatabase($this->createContainer());
        $this->container->get(Config::class)->set('bank.entry-fee.store-driver', 'redis');
        $this->client = $this->container->get(Client::class);
        $this->store = $this->container->get(EntryFeeStore::class);
    }

    public function test_interface_is_bound()
    {
        $this->assertInstanceOf(EntryFeeStore::class, $this->store);
    }

    public function test_enter_create_and_stores_a_new_record_if_one_does_not_exist()
    {
        $entry = new GameEntry(
            new Uuid('a4a7128b-6fc6-4480-845e-cc86a0a69890'),
            new Uuid('6b2b9664-c38d-4d8b-9aa7-bdda507eaa6c')
        );

        $this->store->enter($entry, new Money(100, new Currency('GBP')));

        $record = $this->client->get((string) $entry->getGameId());

        $this->assertEquals('6b2b9664-c38d-4d8b-9aa7-bdda507eaa6c.{"amount":"100","currency":"GBP"}', $record);
    }

    public function test_enter_updates_existing_record_if_it_exists_for_game()
    {
        $entry = new GameEntry(
            new Uuid('a4a7128b-6fc6-4480-845e-cc86a0a69890'),
            new Uuid('6b2b9664-c38d-4d8b-9aa7-bdda507eaa6c')
        );

        $this->store->enter($entry, new Money(100, new Currency('GBP')));

        $record = $this->client->get((string) $entry->getGameId());

        $this->assertEquals('6b2b9664-c38d-4d8b-9aa7-bdda507eaa6c.{"amount":"100","currency":"GBP"}', $record);

        $newEntry = new GameEntry(
            new Uuid('a4a7128b-6fc6-4480-845e-cc86a0a69890'),
            new Uuid('8063c8e5-0051-460b-b286-72ca3aa3e0ed')
        );

        $this->store->enter($newEntry, new Money(100, new Currency('GBP')));

        $updatedRecord = $this->client->get((string) $entry->getGameId());

        $this->assertEquals(
            $record . '/8063c8e5-0051-460b-b286-72ca3aa3e0ed.{"amount":"100","currency":"GBP"}',
            $updatedRecord
        );
    }

    public function test_get_fee_total_returns_a_money_object_containing_total_of_all_entry_fees_for_game()
    {
        $gameId = new Uuid('a4a7128b-6fc6-4480-845e-cc86a0a69890');

        for ($i = 0; $i < 4; $i++) {
            $this->store->enter(new GameEntry($gameId, Uuid::generate()), new Money(1000, new Currency('GBP')));
        }

        $total = $this->store->getFeeTotal($gameId);

        $this->assertEquals(new Money(4000, new Currency('GBP')), $total);
    }

    public function test_exception_thrown_if_no_record_exists_for_game()
    {
        $this->expectException(EntryFeeStoreException::class);
        $this->expectExceptionMessage('Game a4a7128b-6fc6-4480-845e-cc86a0a69890 record does not exist');
        $this->store->getFeeTotal(new Uuid('a4a7128b-6fc6-4480-845e-cc86a0a69890'));
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->flushRedisDatabase($this->container);
    }
}
