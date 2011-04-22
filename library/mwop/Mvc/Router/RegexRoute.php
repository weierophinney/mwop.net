<?php
namespace mwop\Mvc\Router;

use mwop\Stdlib\Route,
    mwop\Mvc\Exception\InvalidRequestException,
    mwop\Mvc\Exception\MissingParameterException,
    Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager,
    Fig\Request,
    Fig\Http\HttpRequest;

class RegexRoute implements Route
{
    protected $events;
    protected $matches;
    protected $request;
    protected $regex;
    protected $spec;

    public function __construct($regex, $spec = null)
    {
        $this->regex = $regex;
        $this->spec  = $spec;
    }

    public function events(EventCollection $events = null)
    {
        if (null !== $events) {
            $this->events = $events;
        } elseif (null === $this->events) {
            $this->events = new EventManager(array(__CLASS__, get_called_class(), 'route'));
        }
        return $this->events;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Match a request
     * 
     * @param  Request $request 
     * @return false|array
     */
    public function match(Request $request)
    {
        if (!$request instanceof HttpRequest) {
            throw new InvalidRequestException(sprintf(
                'Expected HttpRequest; received "%s"',
                get_class($request)
            ));
        }

        $events = $this->events();
        $uri    = $request->getPathInfo();
        if (empty($uri)) {
            // Hack for when running under FastCGI
            $uri = parse_url($request->getRequestUri(), PHP_URL_PATH);
        }

        $events()->trigger(__FUNCTION__ . '.pre', $this, array('uri' => $uri, 'regex' => $this->regex));
        if (!preg_match($this->regex, $uri, $matches)) {
            $events()->trigger(__FUNCTION__ . '.post', $this, array('uri' => $uri, 'regex' => $this->regex, 'success' => false));
            return false;
        }
        $this->matches = $matches;
        $events()->trigger(__FUNCTION__ . '.post', $this, array('uri' => $uri, 'success' => true));
        return $matches;
    }

    /**
     * Assemble a URI
     * 
     * @param  array $params 
     * @param  array $options 
     * @return string
     */
    public function assemble(array $params = array(), array $options = array())
    {
        if (null === $this->spec) {
            return '';
        }
        if (null !== $this->matches) {
            $params = array_merge($this->matches, $params);
        }
        preg_match_all('/\{(?P<token>[^}]+)\}/', $this->spec, $matches);
        if (empty($matches['token'])) {
            return $this->spec;
        }
        $uri    = $this->spec;
        $tokens = $matches['token'];
        foreach ($matches['token'] as $token) {
            if (!isset($params[$token])) {
                throw new MissingParameterException(sprintf(
                    'Specification expects "%s"; no matching parameter provided',
                    $token
                ));
            }
            $uri = str_replace('{' . $token . '}', urlencode($params[$token]), $uri);
        }
        return $uri;
    }
}
