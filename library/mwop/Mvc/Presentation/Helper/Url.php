<?php
namespace mwop\Mvc\Presentation\Helper;

use mwop\Stdlib\Route as Router,
    Fig\Request,
    Fig\Http\HttpRequest;

class Url
{
    protected $router;
    protected $baseUrl = '';

    public function __construct(Request $request, Router $router)
    {
        if ($request instanceof HttpRequest) {
            $this->baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . $request->getBaseUrl();
        }
        $this->router = $router;
    }

    public function generate($spec = null, array $options= array())
    {
        if (null === $spec) {
            // No specification; assemple default URL
            return $this->baseUrl . $this->router->assemble();
        } elseif (is_string($spec)) {
            // String specification; prepend with base URL
            return $this->baseUrl . '/' . ltrim($spec, '/');
        } elseif (is_array($spec)) {
            // Array specification; generate with router
            return $this->baseUrl . $this->router->assemble($spec, $options);
        }

        throw new \DomainException('Invalid URL specification provided; must be null, a string, or an array');
    }
}
