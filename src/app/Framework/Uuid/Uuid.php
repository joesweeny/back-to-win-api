<?php

namespace GamePlatform\Framework\Uuid;

use Ramsey\Uuid\Uuid as BaseUuid;
use Ramsey\Uuid\UuidInterface;

class Uuid implements \JsonSerializable
{
    /**
     * @var UuidInterface
     */
    private $inner;
    /**
     * @param string $string
     */
    public function __construct($string)
    {
        $this->inner = BaseUuid::fromString($string);
    }
    /**
     * @return Uuid
     */
    public static function generate(): Uuid
    {
        return new Uuid(BaseUuid::uuid4()->toString());
    }

    /**
     * Get a 16-bit binary representation of this UUID.
     *
     * @return string
     */
    public function toBinary()
    {
        return $this->inner->getBytes();
    }

    /**
     * Get the hexidecimal representation of this UUID. This will be UUID less and hyphens.
     *
     * @return string
     */
    public function toHex(): string
    {
        return $this->inner->getHex();
    }

    /**
     * @param mixed $bits
     * @return Uuid
     */
    public static function createFromBinary($bits): Uuid
    {
        return new Uuid(BaseUuid::fromBytes($bits));
    }
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->inner->toString();
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->__toString();
    }
}
