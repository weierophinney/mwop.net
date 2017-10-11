<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Contact;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Mwop\PageView;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Csrf\CsrfGuardInterface;
use Zend\Expressive\Csrf\CsrfMiddleware;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;

class Process implements MiddlewareInterface
{
    private $config;
    private $template;
    private $transport;
    private $urlHelper;

    public function __construct(
        TransportInterface $transport,
        TemplateRendererInterface $template,
        UrlHelper $urlHelper,
        array $config
    ) {
        $this->transport = $transport;
        $this->template  = $template;
        $this->urlHelper = $urlHelper;
        $this->config    = $config;
    }

    /**
     * @return Response
     */
    public function process(Request $request, DelegateInterface $delegate)
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

        $this->sendEmail($filter->getValues());

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

    private function sendEmail(array $data)
    {
        $from    = $data['from'];
        $subject = '[Contact Form] ' . $data['subject'];
        $body    = $data['body'];

        $message = $this->createMessage();
        $message->addFrom($from)
            ->addReplyTo($from)
            ->setSubject($subject)
            ->setBody($body);
        $this->transport->send($message);
    }

    private function createMessage() : Message
    {
        $message = new Message();
        $config  = $this->config['message'];
        $message->addTo($config['to']);
        if ($config['from']) {
            $message->addFrom($config['from']);
        }
        $message->setSender($config['sender']['address'], $config['sender']['name']);
        return $message;
    }
}
