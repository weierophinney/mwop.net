<?php
namespace Local;

class Module
{
    public function getConfig($env = null)
    {
        $configFile = __DIR__ . '/config/module.config.php';
        if (!file_exists($configFile)) {
            $configFile .= '.dist';
        }

        $config = include $configFile;

        if (null === $env) {
            return $config;
        }

        if (!isset($config[$env])) {
            throw new InvalidArgumentException(sprintf(
                'Unrecognized environment "%s" provided to %s',
                $env,
                __METHOD__
            ));
        }

        return $config[$env];
    }
}
