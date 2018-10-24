<?php

declare(strict_types=1);

namespace Mwop\OAuth2;

use Psr\Http\Message\ResponseFactoryInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

trait ValidateProviderTrait
{
    /**
     * @var string[]
     */
    private $allowedProviders = [
        'github',
        'google',
    ];
    
    private function validateProvider(?string $provider) : bool
    {
        $allowedProviders = $this->allowedProviders;
        if ($this->isDebug) {
            $allowedProviders[] = 'debug';
        }

        return in_array($provider, $allowedProviders, true);
    }
}
