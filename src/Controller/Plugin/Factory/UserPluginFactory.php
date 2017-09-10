<?php
namespace Popov\ZfcUser\Controller\Plugin\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Popov\ZfcUser\Controller\Plugin\UserPlugin;

class UserPluginFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $cpm)
    {
        $sm = $cpm->getServiceLocator();
        $userService = $sm->get('UserService');
        $acl = $sm->get('Acl');
        $simpler = $cpm->get('simpler');

        $userPlugin = new UserPlugin($userService, $acl);
        $userPlugin->setSimpler($simpler);

        return $userPlugin;
    }
}