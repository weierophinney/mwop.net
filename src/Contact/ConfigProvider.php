<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Contact;

use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;

class ConfigProvider
{
    public function __invoke() : array
    {
        return [
            'contact'      => $this->getConfig(),
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getConfig() : array
    {
        return [
            'recaptcha_pub_key'  => null,
            'recaptcha_priv_key' => null,
            'message' => [
                'to'   => null,
                'from' => null,
                'sender' => [
                    'address' => null,
                    'name'    => null,
                ],
            ],
        ];
    }

    public function getDependencies() : array
    {
        return [
            'factories' => [
                LandingPage::class         => LandingPageFactory::class,
                Process::class             => ProcessFactory::class,
                SendMessageListener::class => SendMessageListenerFactory::class,
                ThankYouPage::class        => ThankYouPageFactory::class,
            ],
            'delegators' => [
                AttachableListenerProvider::class => [
                    SendMessageListenerDelegator::class,
                ],
            ],
        ];
    }
}
