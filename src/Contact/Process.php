<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Contact;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Swoole\Http\Server as HttpServer;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Csrf\CsrfGuardInterface;
use Zend\Expressive\Csrf\CsrfMiddleware;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Template\TemplateRendererInterface;

class Process implements RequestHandlerInterface
{
    private $config;
    private $httpServer;
    private $template;
    private $urlHelper;

    public function __construct(
        HttpServer $httpServer,
        TemplateRendererInterface $template,
        UrlHelper $urlHelper,
        array $config
    ) {
        $this->httpServer = $httpServer;
        $this->template   = $template;
        $this->urlHelper  = $urlHelper;
        $this->config     = $config;
    }

    public function handle(Request $request) : Response
    {
        $guard = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);

        $data  = $request->getParsedBody() ?: [];
        $token = $data['csrf'] ?? '';

        if (! $guard->validateToken($token)) {
            // re-display form
            return $this->redisplayForm(
                [
                    'csrf' => [
                        'isset' => ! empty($token),
                        'valid' => false,
                    ],
                    'data' => $data
                ],
                $guard,
                $request
            );
        }

        $filter = new InputFilter($this->config['recaptcha_priv_key']);
        $filter->setData($data);

        if (! $filter->isValid()) {
            // re-display form
            return $this->redisplayForm(
                $filter->getMessages(),
                $guard,
                $request
            );
        }

        $this->sendMessage($filter->getValues());

        $path = ($this->urlHelper)('contact.thank-you');
        return new RedirectResponse($path);
    }

    private function redisplayForm(array $error, CsrfGuardInterface $guard, Request $request) : Response
    {
        $view = array_merge($this->config, [
            'error'  => ['message' => json_encode(
                $error,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_NUMERIC_CHECK
            )],
            'action' => (string) $request->getAttribute('originalRequest', $request)->getUri(),
            'csrf'   => $guard->generateToken(),
        ]);

        return new HtmlResponse(
            $this->template->render('contact::landing', $view)
        );
    }

    private function sendMessage(array $data)
    {
        $message = new ContactMessage(
            $data['from'],
            sprintf('[Contact Form] %s', $data['subject']),
            $data['body']
        );

        $this->httpServer->task($message);
    }
}
