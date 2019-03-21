<?php
/**
 * @category Popov
 * @package Popov_ZfcUser
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 10.08.2016 13:32
 */
namespace Popov\ZfcUser\Service\Factory;

use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManager;
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
            /** @var EntityManager $om */
            $om = $container->get(EntityManager::class);
            /** @var User $user */
            //$userId = $auth->getIdentity();
            $user = $om->find(User::class, $auth->getIdentity());

            //$storage = $auth->getStorage();
            //$storage->write($user);

            $userService->setCurrent($user);
        }

        return $userService;
    }
}