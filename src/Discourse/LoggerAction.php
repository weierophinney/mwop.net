<?php

namespace Mwop\Discourse;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Zend\Diactoros\Response\EmptyResponse;

class LoggerAction implements MiddlewareInterface
{
    /**
     * @var LoggerInterface
     */
    private $log;

    public function __construct(LoggerInterface $log)
    {
        $this->log = $log;
    }

    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
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
