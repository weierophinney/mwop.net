<?php
namespace Mwop;

class BodyParams
{
    private $nonBodyRequests = [
        'GET',
        'HEAD',
        'OPTIONS',
    ];

    public function __invoke($request, $response, $next)
    {
        if (in_array($request->getMethod(), $this->nonBodyRequests)) {
            return $next();
        }

        $request->body = [];

        $header     = $request->getHeader('Content-Type');
        $priorities = [
            'form'     => 'application/x-www-form-urlencoded',
            'json'     => '[/+]json',
        ];

        $matched = false;
        foreach ($priorities as $type => $pattern) {
            $pattern = sprintf('#%s#', $pattern);
            if (! preg_match($pattern, $header)) {
                continue;
            }
            $matched = $type;
            break;
        }

        switch ($matched) {
            case 'form':
                $request->body = $_POST;
                break;
            case 'json':
                $request->rawBody = $request->getBody()->getContents();
                $request->body = json_decode($request->rawBody, true);
                break;
            default:
                break;
        }

        $next();
    }
}
