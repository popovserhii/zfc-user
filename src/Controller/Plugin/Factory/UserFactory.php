<?php
namespace Popov\ZfcUser\Controller\Plugin\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\HelperPluginManager;

use Popov\ZfcUser\Controller\Plugin\User;

class UserFactory implements FactoryInterface {

	public function createService(ServiceLocatorInterface $cpm) {
		/** @var HelperPluginManager $sm */
		//$locator = $sm->getServiceLocator();
		//return new User($locator->get('ControllerPluginManager')->get('user'));

		$sm = $cpm->getServiceLocator();

        /** @var \Popov\ZfcUser\Controller\Plugin\UserAuthentication $userAuthentication */
        $userAuthentication = $sm->get('UserAuthentication');
		$authService = $userAuthentication->getAuthService();
		$userService = $sm->get('UserService');
		//$accessService = $sm->get('AccessService');
        /** @var \Popov\ZfcUser\Event\Authentication $authEvent */
        //$authEvent = $sm->get('Popov\Users\Event\Authentication');
        //$acl = $authEvent->getAclClass();
        $acl = $sm->get('Acl');
        $simpler = $cpm->get('simpler');

        //\Zend\Debug\Debug::dump(get_class($acl)); die(__METHOD__);

		$userPlugin = new User($authService, $userService, $acl);
		$userPlugin->setSimpler($simpler);
		
		return $userPlugin;
	}

}