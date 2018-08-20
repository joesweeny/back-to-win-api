<?php

namespace GamePlatform\Framework\Identity;

use GamePlatform\Framework\Uuid\Uuid;

trait IdentifiedByUuidTrait
{
    /**
     * IdentifiedByUuid constructor.
     *
     * @param string|Uuid $id
     */
    public function __construct($id = null)
    {
        $this->setId($id ?: Uuid::generate());
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->get('id');
    }

    /**
     * @param string|Uuid $id
     * @return $this
     */
    private function setId($id)
    {
        return $this->set('id', $id instanceof Uuid ? $id : new Uuid($id));
    }

    /**
     * Get a shortened version of the ID.
     *
     * Returns a 10-character hash of the ID
     *
     * This should be avoided as there are higher changes of collisions
     * between IDs. We offer no guarantees that the shortened ID is
     * 100% unique. It can be useful however if you need a short reference
     * for an entity where you have other criteria available to
     * validate you have the correct item
     */
    public function getShortId(): string
    {
        return substr(md5($this->getId()), 0, 10);
    }
}
