<?php

namespace BackToWin\Framework\Session;

use Psr\Http\Message\ServerRequestInterface;
use PSR7Session\Http\SessionMiddleware;
use PSR7Session\Session\SessionInterface;

class SessionManager
{
    /**
     * @param ServerRequestInterface $request
     * @param string $key
     * @param $value
     */
    public function set(ServerRequestInterface $request, string $key, $value)
    {
        self::session($request)->set($key, $value);
    }

    /**
     * @param ServerRequestInterface $request
     * @param string $key
     * @return array|bool|float|int|string
     */
    public function get(ServerRequestInterface $request, string $key)
    {
        return self::session($request)->get($key);
    }

    /**
     * @param ServerRequestInterface $request
     * @return SessionInterface
     */
    private static function session(ServerRequestInterface $request): SessionInterface
    {
        return $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
    }

    /**
     * @param ServerRequestInterface $request
     * @return void
     */
    public function destroy(ServerRequestInterface $request)
    {
        self::session($request)->clear();
    }
}
