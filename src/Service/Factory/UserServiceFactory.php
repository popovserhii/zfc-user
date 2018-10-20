<?php
/**
 * @category Popov
 * @package Popov_ZfcUser
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 10.08.2016 13:32
 */
namespace Popov\ZfcUser\Service\Factory;

use Interop\Container\ContainerInterface;
use Popov\ZfcUser\Auth\Auth;
use Popov\ZfcUser\Service\UserService;
use Popov\ZfcUser\Model\User;

class UserServiceFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $userService = new UserService();

        /** @var Auth $auth */
        $auth = $container->get(Auth::class)->getAuthService();

        if ($auth->hasIdentity()) {
            /** @var \Doctrine\ORM\EntityManager $om */
            $om = $container->get('Doctrine\ORM\EntityManager');
            /** @var User $user */
            $user = $auth->getIdentity();
            $user = $om->merge($user);
            $userService->setCurrent($user);
        }

        return $userService;
    }
}