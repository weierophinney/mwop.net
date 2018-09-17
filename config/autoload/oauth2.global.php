<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

use Phly\Expressive\OAuth2ClientAuthentication\ConfigProvider;

return [
    'oauth2clientauthentication' => [],
    'dependencies' => (new ConfigProvider())->getDependencies(),
];
