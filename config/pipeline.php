<?php

namespace Mwop;

use Zend\Expressive\Helper;
use Zend\Stratigility\Middleware\OriginalMessages;

$app->pipe(OriginalMessages::class);
$app->pipe(ErrorHandler::class);
$app->pipe(ContentSecurityPolicy::class);
$app->pipe(XClacksOverhead::class);
$app->pipe(XPoweredBy::class);
$app->pipe(Redirects::class);
$app->pipe('/auth', Auth\Middleware::class);
$app->pipeRoutingMiddleware();
$app->pipe(Helper\UrlHelperMiddleware::class);
$app->pipeDispatchMiddleware();
$app->pipe(NotFound::class);
