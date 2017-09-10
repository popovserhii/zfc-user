<?php
namespace Popov\ZfcUser\View\Helper\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\HelperPluginManager;

use Popov\ZfcUser\View\Helper\UserHelper;

class UserFactory implements FactoryInterface {

	public function createService(ServiceLocatorInterface $hpm) {
		die(__METHOD__);
		/** @var HelperPluginManager $sm */
		$sm = $hpm->getServiceLocator();
		$userHelper = new UserHelper();
		$userHelper->setUserPlugin($sm->get('ControllerPluginManager')->get('user'));


		//$sm = $cpm->getServiceLocator();
		//$authService = $sm->get('UserAuthentication')->getAuthService();
		//$userService = $sm->get('UsersService');

		//$userPlugin = new User($authService, $userService);

		return $userHelper;
	}

}