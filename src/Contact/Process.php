<?php
namespace Mwop\Contact;

use Aura\Session\Session;
use Mwop\Template\TemplateInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;

class Process
{
    private $config;
    private $session;
    private $template;
    private $transport;

    public function __construct(
        Session $session,
        TransportInterface $transport,
        TemplateInterface $template,
        array $config
    ) {
        $this->session   = $session;
        $this->transport = $transport;
        $this->template  = $template;
        $this->config    = $config;
    }

    public function __invoke($request, $response, $next)
    {
        $this->session->start();

        $data  = $request->getParsedBody() ?: [];
        $token = $this->session->getCsrfToken();

        if (! isset($data['csrf'])
            || ! $token->isValid($data['csrf'])
        ) {
            // re-display form
            return $this->redisplayForm(
                ['csrf' => 'true', 'data' => $data],
                $token,
                $request
            );
        }

        $filter = new InputFilter();
        $filter->setData($data);

        if (! $filter->isValid()) {
            // re-display form
            return $this->redisplayForm(
                $filter->getMessages(),
                $token,
                $request
            );
        }

        $this->sendEmail($filter->getValues());

        $parent = $request->getOriginalRequest();
        $path   = str_replace('/process', '', (string) $parent->getUri()) . '/thank-you';
        return $response
            ->withStatus(302)
            ->withHeader('Location', $path);
    }

    private function redisplayForm($error, $csrfToken, $request)
    {
        $csrfToken->regenerateValue();

        $view = array_merge($this->config, [
            'error'  => ['message' => json_encode($error)],
            'action' => (string) $request->getOriginalRequest()->getUri(),
            'csrf'   => $csrfToken->getValue(),
        ]);

        return new HtmlResponse(
            $this->template->render('contact.landing', $view)
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

    private function createMessage()
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
