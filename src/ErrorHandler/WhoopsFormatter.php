<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\ErrorHandler;

use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run as Whoops;
use Zend\Stratigility\Http\Request as StratigilityRequest;

class WhoopsFormatter
{
    private $whoops;
    private $whoopsHandler;

    public function __construct(Whoops $whoops, PrettyPageHandler $whoopsHandler)
    {
        $this->whoops = $whoops;
        $this->whoopsHandler = $whoopsHandler;
    }

    public function __invoke(Throwable $e, Request $request)
    {
        $this->prepareWhoopsHandler($request);
        $this->whoops->pushHandler($this->whoopsHandler);
        return $this->whoops->handleException($e);
    }

    /**
     * Prepare the Whoops page handler with a table displaying request information.
     *
     * @return void
     */
    private function prepareWhoopsHandler(Request $request)
    {
        if ($request instanceof StratigilityRequest) {
            $request = $request->getAttribute('originalRequest', $request);
        }

        $uri = $request->getUri();
        $this->whoopsHandler->addDataTable('Expressive Application Request', [
            'HTTP Method'            => $request->getMethod(),
            'URI'                    => (string) $uri,
            'Script'                 => $request->getServerParams()['SCRIPT_NAME'],
            'Headers'                => $request->getHeaders(),
            'Cookies'                => $request->getCookieParams(),
            'Attributes'             => $request->getAttributes(),
            'Query String Arguments' => $request->getQueryParams(),
            'Body Params'            => $request->getParsedBody(),
        ]);
    }
}
