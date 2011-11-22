<?php

namespace Contact\Controller;

use Contact\Form\ContactForm,
    Zend\Mail\AbstractTransport as Transport,
    Zend\Mail\Mail as Mailer,
    Zend\Mvc\Controller\ActionController;

class ContactController extends ActionController
{
    protected $form;
    protected $mailer;
    protected $transport;

    public function setMailer(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function setMailTransport(Transport $transport)
    {
        $this->transport = $transport;
    }

    public function indexAction()
    {
        return array('form' => $this->form);
    }

    public function processAction()
    {
        if (!$this->request->isPost()) {
            $this->response->setStatusCode(302);
            $this->response->headers()->addHeaderLine('Location', '/contact');
        }
        $post = $this->request->post()->toArray();
        $form = $this->form;
        if (!$form->isValid($post)) {
            $this->getEvent()->getRouteMatch()->setParam('action', 'index');
            return array(
                'error' => true,
                'form'  => $form
            );
        }

        // send email...
        $values  = $form->getValues();
        $from    = $values['from'];
        $subject = '[Contact Form] ' . $values['subject'];
        $body    = $values['body'];

        $this->mailer->setFrom($from)
                     ->setReplyTo($from)
                     ->setSubject($subject)
                     ->setBodyText($body);
        $this->mailer->send($this->transport);

        return $this->redirect()->toRoute('contact-thank-you');
    }

    public function thankYouAction()
    {
        // do nothing...
    }

    public function setContactForm(ContactForm $form)
    {
        $this->form = $form;
    }
}
