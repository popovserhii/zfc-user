<?php

namespace Popov\ZfcUser\Controller\Plugin\Factory;

use Popov\ZfcUser\Helper\UserHelper;
use Psr\Container\ContainerInterface;
use Popov\Simpler\SimplerHelper;
use Popov\ZfcUser\Service\UserService;
use Popov\ZfcUser\Controller\Plugin\UserPlugin;

class UserPluginFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $acl = $container->get('Acl');
        $userHelper = $container->get(UserHelper::class);
        $simpler = $container->get(SimplerHelper::class);

        $userPlugin = new UserPlugin($userHelper, $acl);
        $userPlugin->setSimpler($simpler);

        return $userPlugin;
    }
}