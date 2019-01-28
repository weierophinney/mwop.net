<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Contact\Handler;

use Mwop\Contact\ContactMessage;
use Mwop\Contact\Validation\InputFilter;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Csrf\CsrfGuardInterface;
use Zend\Expressive\Csrf\CsrfMiddleware;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Template\TemplateRendererInterface;

class ProcessContactFormHandler implements RequestHandlerInterface
{
    private $config;
    private $dispatcher;
    private $template;
    private $urlHelper;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        TemplateRendererInterface $template,
        UrlHelper $urlHelper,
        array $config
    ) {
        $this->dispatcher = $dispatcher;
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

        $path = $this->urlHelper->generate('contact.thank-you');
        return new RedirectResponse($path);
    }

    private function redisplayForm(array $error, CsrfGuardInterface $guard, Request $request) : Response
    {
        $view = array_merge($this->config, [
            'error'  => ['message' => json_encode(
                $error,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_NUMERIC_CHECK
            )],
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

        $this->dispatcher->dispatch($message);
    }
}
