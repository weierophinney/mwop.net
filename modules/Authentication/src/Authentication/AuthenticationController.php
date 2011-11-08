<?php

namespace Authentication;

use Zend\Mvc\Controller\ActionController,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\Router\RouteMatch;

class AuthenticationController extends ActionController
{
    protected $auth;

    public function setAuthentication(AuthenticationService $auth)
    {
        $this->auth = $auth;
    }

    public function loginAction()
    {
        $form = new AuthenticationForm();
        if (!$this->request->isPost()) {
            return array('form' => $form);
        }

        $post = $this->request->post()->toArray();
        if (!$form->isValid($post)) {
            return array(
                'error'   => true,
                'message' => 'One or more values were invalid; please correct, and try again.',
                'form'    => $form,
            );
        }

        $values = $form->getValues();
        $result = $this->auth->login($values['username'], $values['password']);
        if (!$result->isValid()) {
            return array(
                'error'   => true,
                'message' => 'Invalid credentials provided; please try again.',
                'form'    => $form,
            );
        }

        return array('success' => true);
    }

    public function logoutAction()
    {
        $this->auth->logout();
        return array('success' => true);
    }
}
