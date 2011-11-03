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
        $server  = $request->server();

        // Disable the layout?
        $disable = $query->get('disable', false);
        $disable = $disable ? 0 : 1;

        // Set cookies
        $response = $this->getResponse();
        $headers  = $response->headers();

        // En/Dis-able mobile layout
        $mobile   = new SetCookieHeader();
        $mobile->setName('mwop_mobile');
        $mobile->setValue($disable);
        $mobile->setExpires(null);
        $mobile->setDomain($server->get('SERVER_NAME'));
        $mobile->setPath('/');
        $mobile->setHttpOnly(false);
        $headers->addHeader($mobile);

        // Set theme
        $theme    = $query->get('theme', 'iphone');
        $themeSel = new SetCookieHeader();
        $themeSel->setName('mwop_theme');
        $themeSel->setValue($theme);
        $themeSel->setExpires(null);
        $themeSel->setDomain($server->get('SERVER_NAME'));
        $themeSel->setPath('/');
        $themeSel->setHttpOnly(false);
        $headers->addHeader($themeSel);

        // Redirect to referer
        $referer  = $server->get('HTTP_REFERER');
        if (!$referer) {
            $referer = '/';
        }
        $headers->addHeaderLine('Location', $referer);

        // Done
        return $response;
    }
}
