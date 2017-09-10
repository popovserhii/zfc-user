<?php
/**
 * @category Agere
 * @package Agere_User
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 10.08.2016 13:32
 */
namespace Popov\ZfcUser\Service\Factory;

use Zend\Authentication\AuthenticationService;
use Interop\Container\ContainerInterface;
use Popov\ZfcUser\Service\UserService;
use Popov\ZfcUser\Model\User as User;

class UserServiceFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var AuthenticationService $authService */
        $authService = $container->get('UserAuthentication')->getAuthService();
        $userService = new UserService();

        if ($authService->hasIdentity()) {
            /** @var \Doctrine\ORM\EntityManager $om */
            //$om = $container->get('Doctrine\ORM\EntityManager');
            $user = $authService->getIdentity();
            //$user = $om->find(User::class, $dataUser['id']);
            $userService->setCurrent($user);
        }

        return $userService;
    }
}