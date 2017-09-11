<?php
/**
 * Authentication Factory
 *
 * @category Agere
 * @package Agere_User
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 12.03.2016 1:44
 */
namespace Popov\ZfcUser\Event\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use Popov\ZfcUser\Event\Authentication;

class AuthenticationFactory
{
    public function __invoke(ServiceLocatorInterface $sm)
    {
        $acl = $sm->get('Acl');
        $config = $sm->get('Config');
        $request = $sm->get('Request');
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
        $authService = $sm->get('UserAuthentication')->getAuthService();

        $auth = new Authentication();
        $auth->setAuthService($authService);
        $auth->setRequest($request);
        $auth->setConfig($config);
        $auth->setDbAdapter($dbAdapter);
        //$auth->setUserAuthenticationPlugin($userAuthenticationPlugin);
        $auth->setAcl($acl);
        $auth->setRoles($auth->getDbRoles($dbAdapter));

        return $auth;
    }
}