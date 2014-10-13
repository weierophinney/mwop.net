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
                // Nothing to do; $_POST is injected by default into the 
                // request body parameters
                break;
            case 'json':
                $request->rawBody = $request->getBody()->getContents();
                $reqest->setBodyParams(json_decode($request->rawBody, true));
                break;
            default:
                break;
        }

        $next();
    }
}
