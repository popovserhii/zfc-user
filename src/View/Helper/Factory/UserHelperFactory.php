<?php

namespace Popov\ZfcUser\View\Helper\Factory;

use Psr\Container\ContainerInterface;
use Popov\ZfcUser\View\Helper\UserHelper;
use Popov\ZfcUser\Helper\UserHelper as BaseUserHelper;

class UserHelperFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $baseHelper = $container->get(BaseUserHelper::class);
        $userHelper = new UserHelper($baseHelper);

        return $userHelper;
    }
}