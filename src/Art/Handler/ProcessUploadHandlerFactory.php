<?php

declare(strict_types=1);

namespace Mwop\Art\Handler;

use Mezzio\Helper\UrlHelper;
use Mezzio\Template\TemplateRendererInterface;
use Mwop\Art\UploadPhoto;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class ProcessUploadHandlerFactory
{
    public function __invoke(ContainerInterface $container): ProcessUploadHandler
    {
        return new ProcessUploadHandler(
            responseFactory: $container->get(ResponseFactoryInterface::class),
            renderer: $container->get(TemplateRendererInterface::class),
            uploader: $container->get(UploadPhoto::class),
            url: $container->get(UrlHelper::class),
        );
    }
}
