<?php

namespace Popov\ZfcUser\Helper;

use Psr\Container\ContainerInterface;
use Popov\Simpler\SimplerHelper;

class UserHelperFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $userService = $container->get('UserService');
        $acl = $container->get('Acl');
        $simpler = $container->get(SimplerHelper::class);
        $userPlugin = new UserHelper($userService, $acl, $simpler);

        return $userPlugin;
    }
}