<?php
namespace Application\Controller;

use Zend\Http\Header\SetCookie as SetCookieHeader,
    Zend\Mvc\Controller\ActionController;

class MobileController extends ActionController
{
    public function indexAction()
    {
        $request = $this->getRequest();
        $query   = $request->query();
        $disable = $query->get('disable', false);
        $disable = $disable ? 0 : 1;
        $server  = $request->server();
        $referer = $server->get('HTTP_REFERER');
        if (!$referer) {
            $referer = '/';
        }

        $response = $this->getResponse();
        $headers  = $response->headers();
        $cookie   = $response->cookie();
        if (!$cookie) {
            $cookie = new SetCookieHeader();
            $headers->addHeader($cookie);
        }
        $cookie->setName('mwop_mobile');
        $cookie->setValue($disable);
        $cookie->setExpires(null);
        $cookie->setDomain($server->get('SERVER_NAME'));
        $cookie->setPath('/');
        $cookie->setHttpOnly(false);
        $headers->addHeaderLine('Location', $referer);
        return $response;
    }
}
