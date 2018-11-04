<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Contact;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Swift_Mailer as Mailer;
use Swift_Message as Message;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Csrf\CsrfGuardInterface;
use Zend\Expressive\Csrf\CsrfMiddleware;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Template\TemplateRendererInterface;

class Process implements RequestHandlerInterface
{
    private $config;
    private $template;
    private $mailer;
    private $urlHelper;

    public function __construct(
        Mailer $mailer,
        TemplateRendererInterface $template,
        UrlHelper $urlHelper,
        array $config
    ) {
        $this->mailer    = $mailer;
        $this->template  = $template;
        $this->urlHelper = $urlHelper;
        $this->config    = $config;
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
        $replyTo = $data['from'];
        $subject = '[Contact Form] ' . $data['subject'];
        $body    = $data['body'];

        $message = $this->createMessage();
        $message
            ->setReplyTo($replyTo)
            ->setSubject($subject)
            ->setBody($body);
        $this->mailer->send($message);
    }

    private function createMessage() : Message
    {
        $message = new Message();
        $config  = $this->config['message'];
        $message->setTo($config['to']);
        $message->setFrom($config['sender']['address']);
        $message->setSender($config['sender']['address']);
        return $message;
    }
}
