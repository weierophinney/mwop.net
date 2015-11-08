<?php
namespace Mwop;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Expressive\TemplatedErrorHandler;
use Zend\Stratigility\Http\Response as StratigilityResponse;

class ErrorHandler
{
    private $displayErrors;
    private $originalResponse;
    private $renderer;
    private $router;
    private $template404;
    private $templateError;

    public function __construct(
        TemplateRendererInterface $renderer,
        RouterInterface $router,
        $displayErrors = false,
        $template404 = 'error::404',
        $templateError = 'error::500',
        ResponseInterface $originalResponse = null
    ) {
        $this->renderer      = $renderer;
        $this->router        = $router;
        $this->displayErrors = $displayErrors;
        $this->template404   = $template404;
        $this->templateError = $templateError;
        if ($originalResponse) {
            $this->setOriginalResponse($originalResponse);
        }
    }

    public function setOriginalResponse(ResponseInterface $response)
    {
        $this->originalResponse = $response;
    }

    public function __invoke($request, $response, $err = null)
    {
        if (! $err) {
            return $this->marshalNonErrorResponse($request, $response);
        }

        return $this->handleErrorResponse($err, $request, $response);
    }

    private function marshalNonErrorResponse($request, $response)
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

    private function marshalReceivedResponse($request, $response)
    {
        if ($response->getStatusCode() === 200
            && $response->getBody()->getSize() === 0
        ) {
            return $this->create404($request, $response);
        }

        return $response;
    }

    private function create404($request, $response)
    {
        $viewModel = new PageView();
        $viewModel->setRouter($this->router);
        return new HtmlResponse(
            $this->renderer->render($this->template404, $viewModel),
            404
        );
    }

    private function handleErrorResponse($error, $request, $response)
    {
        $error = $this->displayErrors
            ? $this->prepareError($error)
            : [];
        $viewModel = new PageView(['error' => $error]);
        $viewModel->setRouter($this->router);
        return new HtmlResponse(
            $this->renderer->render($this->templateError, $viewModel),
            500
        );
    }

    private function prepareError($error)
    {
        if (is_scalar($error)) {
            return $error;
        }

        if ($error instanceof \Exception) {
            return $this->prepareException($error);
        }

        return json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    private function prepareException(\Exception $e)
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
