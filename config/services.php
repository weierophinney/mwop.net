<?php
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;

return new ServiceManager(new Config(require 'config/dependencies.php'));
