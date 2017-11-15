<?php

namespace Mwop;

use Middlewares\Csp;
use Phly\Expressive\OAuth2ClientAuthentication\OAuth2CallbackMiddleware;
use Zend\Expressive\Helper;
use Zend\Expressive\Middleware\NotFoundHandler;
use Zend\Stratigility\Middleware\ErrorHandler;
use Zend\Stratigility\Middleware\OriginalMessages;

$app->pipe(OriginalMessages::class);
$app->pipe(XClacksOverhead::class);
$app->pipe(XPoweredBy::class);
$app->pipe(Csp::class);
$app->pipe(ErrorHandler::class);
$app->pipe(Redirects::class);
$app->pipe('/auth', OAuth2CallbackMiddleware::class);
$app->pipeRoutingMiddleware();
$app->pipe(Helper\UrlHelperMiddleware::class);
$app->pipeDispatchMiddleware();
$app->pipe(NotFoundHandler::class);
