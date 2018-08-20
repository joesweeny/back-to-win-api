<?php

namespace GamePlatform\Framework\Entity;

use Cake\Chronos\Chronos;
use GamePlatform\Framework\Exception\UndefinedException;
use DateTimeImmutable;

/**
 * Classes using this trait MUST also use PrivateAttributesTrait, or implement its get() and set() methods.
 */
trait TimestampedTrait
{
    /**
     * @return Chronos
     */
    public function getCreatedDate(): Chronos
    {
        return Chronos::instance($this->get('created', function () {
            throw new UndefinedException('This entity does not have a created date set yet');
        }));
    }

    /**
     * @param DateTimeImmutable|string $datetime
     * @return $this
     */
    public function setCreatedDate($datetime)
    {
        if (is_string($datetime)) {
            $datetime = new DateTimeImmutable($datetime);
        }

        if (!$datetime instanceof DateTimeImmutable) {
            throw new \InvalidArgumentException('Date must be instance of DateTimeImmutable or a string');
        }

        try {
            $this->getCreatedDate();
            $overriding = true;
        } catch (UndefinedException $e) {
            $overriding = false;
        }

        if ($overriding === true) {
            throw new \RuntimeException('This entity already has a created date, and it cannot be changed');
        }

        return $this->set('created', $datetime);
    }

    /**
     * @return Chronos
     */
    public function getLastModifiedDate(): Chronos
    {
        return Chronos::instance($this->get('modified', function () {
            throw new UndefinedException('This entity does not have a modified date set yet');
        }));
    }

    /**
     * @param DateTimeImmutable|string $datetime
     * @return $this
     */
    public function setLastModifiedDate($datetime)
    {
        if (!$datetime instanceof DateTimeImmutable) {
            $datetime = new DateTimeImmutable($datetime);
        }

        return $this->set('modified', $datetime);
    }
}
