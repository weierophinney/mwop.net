<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Expressive\TemplatedErrorHandler;
use Zend\Stratigility\Http\Response as StratigilityResponse;

class ErrorHandler
{
    private $displayErrors;
    private $originalResponse;
    private $renderer;
    private $template404;
    private $templateError;

    public function __construct(
        TemplateRendererInterface $renderer,
        bool $displayErrors = false,
        string $template404 = 'error::404',
        string $templateError = 'error::500',
        Response $originalResponse = null
    ) {
        $this->renderer      = $renderer;
        $this->displayErrors = $displayErrors;
        $this->template404   = $template404;
        $this->templateError = $templateError;
        if ($originalResponse) {
            $this->setOriginalResponse($originalResponse);
        }
    }

    public function setOriginalResponse(Response $response)
    {
        $this->originalResponse = $response;
    }

    public function __invoke(Request $request, Response $response, $err = null) : Response
    {
        if (! $err) {
            return $this->marshalNonErrorResponse($request, $response);
        }

        return $this->handleErrorResponse($err, $request, $response);
    }

    private function marshalNonErrorResponse(Request $request, Response $response) : Response
    {
        if (! $this->originalResponse) {
            return $this->marshalReceivedResponse($request, $response);
        }

        $originalResponse  = $this->originalResponse;
        $decoratedResponse = $response instanceof StratigilityResponse
            ? $response->getOriginalResponse()
            : $response;

        if ($originalResponse !== $response
            && $originalResponse !== $decoratedResponse
        ) {
            // Response does not match either the original response or the
            // decorated response; return it verbatim.
            return $response;
        }

        if (($originalResponse === $response || $decoratedResponse === $response)
            && $this->bodySize !== $response->getBody()->getSize()
        ) {
            // Response matches either the original response or the
            // decorated response; but the body size has changed; return it
            // verbatim.
            return $response;
        }

        return $this->create404($request, $response);
    }

    private function marshalReceivedResponse(Request $request, Response $response) : Response
    {
        if ($response->getStatusCode() === 200
            && $response->getBody()->getSize() === 0
        ) {
            return $this->create404($request, $response);
        }

        return $response;
    }

    private function create404(Request $request, Response $response) : Response
    {
        return new HtmlResponse(
            $this->renderer->render($this->template404, []),
            404
        );
    }

    private function handleErrorResponse($error, Request $request, Response $response) : Response
    {
        $error = $this->displayErrors
            ? $this->prepareError($error)
            : [];
        return new HtmlResponse(
            $this->renderer->render($this->templateError, ['error' => $error]),
            500
        );
    }

    private function prepareError($error) : string
    {
        if (is_scalar($error)) {
            return (string) $error;
        }

        if ($error instanceof Throwable) {
            return $this->prepareException($error);
        }

        return json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    private function prepareException(Throwable $e) : string
    {
        $message = '';
        do {
            $message .= sprintf(
                "Exception: %s (%d)\nTrace:\n%s\n",
                $e->getMessage(),
                $e->getCode(),
                $e->getTraceAsString()
            );
        } while ($e = $e->getPrevious());
        return $message;
    }
}
