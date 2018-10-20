<?php

namespace Popov\ZfcUser\Controller\Plugin\Factory;

use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserPluginDelegatorFactory implements DelegatorFactoryInterface
{
    public function createDelegatorWithName(ServiceLocatorInterface $serviceManager, $name, $requestedName, $callback )
    {
        $instance = $callback();
        if (method_exists($instance, 'setUserPlugin')) {
            $userPlugin = $serviceManager->get('ControllerPluginManager')->get('user');
            $instance->setUserPlugin($userPlugin);
        }
        return $instance;
    }
}