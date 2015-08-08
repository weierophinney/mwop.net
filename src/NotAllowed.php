<?php
namespace Mwop;

class NotAllowed
{
    public function __invoke($err, $req, $res, $next)
    {
        if ($res->getStatusCode() !== 405) {
            return $next($req, $res, $err);
        }

        if (is_array($err) || is_string($err)) {
            $res = $res->withHeader('Allow', $err);
        }

        return $res;
    }
}
