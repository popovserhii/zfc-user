<?php
namespace Popov\ZfcUser\Controller\Plugin\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\HelperPluginManager;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Popov\ZfcUser\Controller\Plugin\User;
//use Popov\ZfcUser\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Popov\ZfcUser\Controller\Plugin\UserAuthentication;

class UserAuthenticationFactory
{
	public function __invoke(ContainerInterface $container) {
		/** @var HelperPluginManager $sm */
		//$locator = $sm->getServiceLocator();
		//return new User($locator->get('ControllerPluginManager')->get('user'));

		//$sm = $container->getServiceLocator();

        //$authAdapter = $container->get(CredentialTreatmentAdapter::class);

        $zendDb = $container->get('Zend\Db\Adapter\Adapter');
        //$zendDb = $container->get('Zend\Db\Adapter\AdapterInterface');
        $tableName = 'user';
        $identityColumn = 'email';
        $credentialColumn = 'password';
        $credentialTreatment = '?';
        /*$adapter = new CredentialTreatmentAdapter(
            $zendDb,
            $tableName,
            $identityColumn,
            $credentialColumn,
            $credentialTreatment
        );*/

        $authAdapter = new CredentialTreatmentAdapter(
            $zendDb,
            $tableName,
            $identityColumn,
            $credentialColumn,
            $credentialTreatment
        );

        $userAuthentication = new UserAuthentication();
        $userAuthentication->setAuthAdapter($authAdapter);

        return $userAuthentication;
	}

}