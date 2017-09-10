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

use Popov\ZfcUser\Service\UserService;
use Zend\ServiceManager\ServiceLocatorInterface;
use Popov\ZfcUser\Event\Authentication;
use Popov\ZfcUser\Controller\Plugin\UserAuthentication;
use Popov\ZfcUser\Acl\Acl;

class AuthenticationFactory {

	protected $roles;

	public function __invoke(ServiceLocatorInterface $sm) {
        //$cpm = $sm->get('ControllerPluginManager');
        $cpm = $sm->get('ControllerPluginManager');

        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
		$userAuthenticationPlugin = $sm->get(UserAuthentication::class);
        $userPlugin = $cpm->get('user');

        /** @var UserService $userService */
        //$userService = $sm->get('UserService');
        //$user = $userService->getCurrent();
		$acl = $sm->get('Acl');
        $request = $sm->get('Request');

		$auth = new Authentication();
		$auth->setUserPlugin($userPlugin);
		$auth->setRequest($request);
		$auth->setDbAdapter($dbAdapter);
		$auth->setUserAuthenticationPlugin($userAuthenticationPlugin);
		$auth->setAcl($acl);
		$auth->setRoles($auth->getDbRoles($dbAdapter));

		//$tableRealName = func_get_args()[2];

		return $auth;
	}




}