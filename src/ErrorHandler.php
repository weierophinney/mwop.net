<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use DomainException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class ErrorHandler
{
    const TEMPLATE_ERROR = 'error::500';

    private $displayErrors;

    private $errorFormatter;

    private $renderer;

    public function __construct(
        TemplateRendererInterface $renderer,
        bool $displayErrors = false
    ) {
        $this->renderer      = $renderer;
        $this->displayErrors = $displayErrors;
    }

    public function setErrorFormatter(callable $formatter)
    {
        $this->errorFormatter = $formatter;
    }

    public function getErrorFormatter() : callable
    {
        if (! $this->errorFormatter) {
            return $this->getDefaultErrorFormatter();
        }

        return $this->errorFormatter;
    }

    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        try {
            $result = $next($request, $response);
            if ($result instanceof Response) {
                return $result;
            }

            throw new DomainException('No middleware returned a response.');
        } catch (Throwable $e) {
            return $this->createErrorResponse($e, $request);
        }
    }

    public function createErrorResponse(Throwable $e, Request $request) : Response
    {
        $error = $this->displayErrors
            ? $this->prepareError($e, $request)
            : $this->renderer->render(self::TEMPLATE_ERROR);

        return new HtmlResponse($error, 500);
    }

    private function getDefaultErrorFormatter() : callable
    {
        return function (Throwable $e) : string {
            $message = '';
            do {
                $message .= sprintf(
                    "Exception: %s (%d)\nTrace:\n%s\n",
                    $e->getMessage(),
                    $e->getCode(),
                    $e->getTraceAsString()
                );
            } while ($e = $e->getPrevious());

            return $this->renderer->render(self::TEMPLATE_ERROR, ['error' => $message]);
        };
    }

    private function prepareError(Throwable $error, Request $request) : string
    {
        $formatter = $this->getErrorFormatter();
        return $formatter($error, $request);
    }
}
