<?php

namespace BackToWin\Framework\Password;

class PasswordHash
{
    private static $algo = PASSWORD_BCRYPT;
    private static $algoOptions = [];
    private $hash;

    /**
     * PasswordHash constructor.
     * @param $hash
     */
    public function __construct($hash)
    {
        if (empty($hash)) {
            throw new \InvalidArgumentException('Password hash cannot be empty');
        }

        $this->hash = $hash;
    }

    /**
     * @return bool
     */
    public function needsRehash(): bool
    {
        return password_needs_rehash($this->__toString(), static::$algo, static::$algoOptions);
    }

    /**
     * Verify a raw password against this password hash
     *
     * @param string $rawPassword
     * @return bool
     */
    public function verify(string $rawPassword)
    {
        return password_verify($rawPassword, $this->__toString());
    }

    /**
     * PasswordHash constructor.
     * @param string $rawPassword
     * @return static
     */
    public static function createFromRaw(string $rawPassword)
    {
        if (empty($rawPassword)) {
            throw new \InvalidArgumentException('Password cannot be empty');
        }

        return new static(password_hash($rawPassword, static::$algo, static::$algoOptions));
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->hash;
    }
}
