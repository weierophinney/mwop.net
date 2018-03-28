<?php

namespace Mwop\Github\PuSH;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Zend\Diactoros\Response\EmptyResponse;

class LoggerAction implements RequestHandlerInterface
{
    /**
     * @var LoggerInterface
     */
    private $log;

    public function __construct(LoggerInterface $log)
    {
        $this->log = $log;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $path = $request->getUri()->getPath();
        $contentType = $request->getHeaderLine('Content-Type') ?: 'application/x-www-form-urlencoded';
        $signature = $request->getHeaderLine('X-Hub-Signature') ?: '(none)';
        $body = (string) $request->getBody();

        $message = '{path} (Signed: {signature}) ({content_type}): {payload}';

        $this->log->info($message, [
            'content_type' => $contentType,
            'path'         => $path,
            'payload'      => $body,
            'signature'    => $signature,
        ]);

        return new EmptyResponse(202);
    }
}
