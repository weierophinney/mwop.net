<?php
namespace Mwop\Contact;

use Aura\Session\Session;
use Phly\Mustache\Mustache;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;

class Process
{
    private $config;
    private $page;
    private $renderer;
    private $session;
    private $transport;

    public function __construct(
        Mustache $renderer,
        Session $session,
        TransportInterface $transport,
        $page,
        array $config
    ) {
        $this->renderer  = $renderer;
        $this->session   = $session;
        $this->transport = $transport;
        $this->page      = $page;
        $this->config    = $config;
    }

    public function __invoke($request, $response, $next)
    {
        if ($request->getMethod() !== 'POST') {
            $response->setStatusCode(405);
            return $next('POST');
        }

        $this->session->start();

        $data  = $request->body;
        $token = $this->session->getCsrfToken();

        if (! isset($data['csrf'])
            || ! $token->isValid($data['csrf'])
        ) {
            // re-display form
            return $this->redisplayForm(['csrf' => 'true', 'data' => $data], $token, $request, $response);
        }

        $filter = new InputFilter();
        $filter->setData($data);

        if (! $filter->isValid()) {
            // re-display form
            return $this->redisplayForm($filter->getMessages(), $token, $request, $response);
        }

        $this->sendEmail($filter->getValues());

        $path = str_replace('/process', '', $request->originalUrl) . '/thank-you';
        $response->setStatusCode(302);
        $response->addHeader('Location', $path);
        $response->end();
    }

    private function redisplayForm($error, $csrfToken, $request, $response)
    {
        $csrfToken->regenerateValue();

        $view = array_merge($this->config, [
            'error'  => ['message' => json_encode($error)],
            'action' => $request->originalUrl,
            'csrf'   => $csrfToken->getValue(),
        ]);

        $response->end($this->renderer->render($this->page, $view));
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
