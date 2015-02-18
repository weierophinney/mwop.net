<?php
namespace Mwop\Contact;

use Aura\Session\Session;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;

class Process
{
    private $config;
    private $page;
    private $session;
    private $transport;

    public function __construct(
        Session $session,
        TransportInterface $transport,
        $page,
        array $config
    ) {
        $this->session   = $session;
        $this->transport = $transport;
        $this->page      = $page;
        $this->config    = $config;
    }

    public function __invoke($request, $response, $next)
    {
        error_log(sprintf("In %s\n", __METHOD__));
        if ($request->getMethod() !== 'POST') {
            error_log(sprintf("DID NOT RECEIVE POST! Returning 405\n"));
            return $next($request, $response->withStatus(405), 'POST');
        }

        $this->session->start();

        $data  = $request->getParsedBody() ?: [];
        $token = $this->session->getCsrfToken();

        if (! isset($data['csrf'])
            || ! $token->isValid($data['csrf'])
        ) {
            error_log(sprintf("Invalid CSRF token; redisplaying form\n"));
            // re-display form
            return $next($this->redisplayForm(
                ['csrf' => 'true', 'data' => $data],
                $token,
                $request,
                $response
            ), $response);
        }

        $filter = new InputFilter();
        $filter->setData($data);

        if (! $filter->isValid()) {
            error_log(sprintf("Invalid form data; redisplaying form with messages\n"));
            // re-display form
            return $next($this->redisplayForm(
                $filter->getMessages(),
                $token,
                $request,
                $response
            ), $response);
        }

        error_log(sprintf("Sending email\n"));
        $this->sendEmail($filter->getValues());

        $parent = $request->getOriginalRequest();
        $path = str_replace('/process', '', (string) $parent->getUri()) . '/thank-you';
        error_log(sprintf("Returning response with 302 status and location %s\n", $path));
        return $response
            ->withStatus(302)
            ->withHeader('Location', $path)
            ->end();
    }

    private function redisplayForm($error, $csrfToken, $request, $response)
    {
        $csrfToken->regenerateValue();

        $view = array_merge($this->config, [
            'error'  => ['message' => json_encode($error)],
            'action' => $request->originalUrl,
            'csrf'   => $csrfToken->getValue(),
        ]);

        return $request->withAttribute('view', (object) [
            'template' => $this->page,
            'model'    => $view,
        ]);
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
