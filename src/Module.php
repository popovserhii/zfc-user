<?php
namespace Popov\ZfcUser;

use Zend\EventManager\StaticEventManager;
use Zend\Session\Container as SessionContainer;
use	Zend\Mvc\MvcEvent;
use	Zend\ModuleManager\ModuleManager;
use	Zend\EventManager\Event;
use	Zend\Http\Request as HttpRequest;
//use	Popov\Agere\String\StringUtils as AgereString;
use	Popov\ZfcUser\Acl\Acl;
use	Popov\ZfcUser\Controller\Plugin\UserAuthentication;
//use Popov\Agere\View\Helper\AbstractHelper;
use Zend\Console\Request as ConsoleRequest;

class Module {

	protected $dbAdapter;
	protected $resultRolesArray;
	protected $accessDefault = 6;
	protected $denyDefault = 0;
	protected $roles;
	protected $acl;


	public function onBootstrap(MvcEvent $e) {
		$eventManager = $e->getApplication()->getEventManager();
		$sm = $e->getApplication()->getServiceManager();

		// set params in the controller
		$sharedEvents = $eventManager->getSharedManager();


		$this->attachEvents($e);


		//$route = $sm->get('Application')->getMvcEvent()->getRouteMatch();

		//\Zend\Debug\Debug::dump([strpos($e->getRequest()->getUri()->getPath(), 'soap')]); die(__METHOD__);


		/*if ($e->getRequest()->getMethod() == 'POST') {
			throw new \SOAPFault("Incorrect username and or password.", 401);
		}*/

        /** @var \Popov\ZfcUser\Event\Authentication $auth */
        $this->auth = $auth = $sm->get('Popov\Users\Event\Authentication');
		//if (false) {
			if ($e->getRequest() instanceof HttpRequest
				&& (false === strpos($e->getRequest()->getUri()->getPath(), 'soap')) // @todo Придумати як видалити цю перевірку
			) {
				//$this->dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				//$this->acl = new Acl(); //\Zend\Permissions\Acl\Acl();
				//$this->getDbRoles($e);
				$auth->init();


				//if ($e->getRequest()->getMethod() == 'POST') {
				/*if ($e->getRequest()->getMethod() == 'POST') {
					$server = $e->getRequest()->getServer();
					//\Zend\Debug\Debug::dump([$server->get('PHP_AUTH_USER'), $server->get('PHP_AUTH_PW')]); die(__METHOD__);
					if ($server->get('PHP_AUTH_USER') && $server->get('PHP_AUTH_PW')) {
						//die(__METHOD__);
						//die(__METHOD__);
						$auth->basicAuthentication($server->get('PHP_AUTH_USER'), $server->get('PHP_AUTH_PW'));
					}
				}*/


				// Access to page
				//$auth->preDispatch($event);
				// Attach Event to EventManager
				//$events = StaticEventManager::getInstance();
				//$events = $e->getApplication()->getEventManager()->getSharedManager();
				//$sharedEvents->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', array($this, 'mvcPreDispatch'), 100); //@todo - Go directly to User\Event\Authentication
				$sharedEvents->attach(\Zend\Mvc\Controller\AbstractActionController::class, 'dispatch', array(
					$auth,
					'mvcPreDispatch'
				), 1000); //@todo - Go directly to User\Event\Authentication
			} elseif ($e->getRequest() instanceof ConsoleRequest) {
                $auth->initCron();
			}


		//}
	}

	/**
	 * @deprecated
	 */
	private function setInitAcl(MvcEvent $e)
	{
        die(__METHOD__);
		if ($this->roles) {
			foreach ($this->roles as $role => $resources) {
				$role = new \Zend\Permissions\Acl\Role\GenericRole($role);
				$this->acl->addRole($role);
				//adding resources
				foreach ($resources as $resource) {
					if (!$this->acl->hasResource($resource['target'])) {
						$this->acl->addResource(new \Zend\Permissions\Acl\Resource\GenericResource($resource['target']));
					}
					if ($resource['access'] == $this->denyDefault) {
						$this->acl->deny($role, $resource['target'], $resource['access']);
					} else {
						$this->acl->allow($role, $resource['target'], $resource['access']);
					}
				}
			}
			//unset($this->roles);
			//setting to view
			$e->getViewModel()->acl = $this->acl;
		}
	}

	/**
	 * @deprecated
	 */
	private function getResultRolesArray()
	{
        die(__METHOD__);
		if (is_null($this->resultRolesArray))
		{
			$dbAdapter = $this->dbAdapter;

			// Table roles
			$resultRoles = $this->dbAdapter->query('SELECT r.`id`, r.`mnemo`, r.`resource`
											FROM `roles` r', $dbAdapter::QUERY_MODE_EXECUTE);

			foreach ($resultRoles as $result)
			{
				if ($result['resource'] == 'all')
				{
					$this->roles[$result['mnemo']][] = [
						'target' => $result['resource'],
						'access' => $this->accessDefault,
					];
				}
				else
				{
					$this->resultRolesArray[$result['id']] = $result;
				}
			}
		}

		return $this->resultRolesArray;
	}

	/**
	 * @deprecated
	 */
	public function getDbRoles(MvcEvent $e){
        die(__METHOD__);
		$dbAdapter = $this->dbAdapter;

		$results = $this->dbAdapter->query('SELECT p.`target`, pa.`roleId`, pa.`access`
										FROM `permission_access` pa
										LEFT JOIN `permission` p ON pa.`permissionId` = p.`id`
										WHERE p.`entityId` = 0 AND p.`parent` = 0', $dbAdapter::QUERY_MODE_EXECUTE);

		// making the roles array
		$this->roles['guest'][] = ['target' => 'users/login', 'access' => $this->accessDefault];
		$this->roles['guest'][] = ['target' => 'users/forgot-password', 'access' => $this->accessDefault];

		// Table roles to array
		$resultRolesArray = $this->getResultRolesArray();

		foreach($results as $result)
		{
			// Parse roleId
			$assocDigit = AgereString::parseStringAssocDigit($result['roleId']);

			if ($assocDigit['field'] == 'role' && isset($resultRolesArray[$assocDigit['id']]))
			{
				$this->roles[$resultRolesArray[$assocDigit['id']]['mnemo']][] = [
					'target' => $result['target'],
					'access' => $result['access'],
				];
			}
		}

		return $this->roles;
	}

	/**
	 * @deprecated
	 */
	public function getDbPage(MvcEvent $e, $params)
	{
        die(__METHOD__);
		$sm = $e->getApplication()->getServiceManager();
		$dbAdapter = $this->dbAdapter;
		$where = '';

		// User
		//$userAuth = new UserAuthentication();
		//$authService = $userAuth->getAuthService();
		//$userHelper = new \Popov\Users\View\Helper\User($authService);
		//$user = $userHelper->getUser();

		$userHelper = $sm->get('ViewHelperManager')->get('user');
		$user = $userHelper->getUser();

		$viewModel = $e->getViewModel();

		// Acl class
		$aclClass = $viewModel->acl;

		if (isset($user['mnemo']) && ! $aclClass->isAllowed($user['mnemo'], 'all', $this->accessDefault))
		{
			// Where
			if (isset($params['id']) && $params['id'] > 0)
			{
				$where = "(p.`target` = '{$params['controller']}/{$params['action']}/{$params['id']}'
						AND p.`entityId` = {$params['id']} AND p.`type` = 'action')";
			}

			if (isset($params['parent']) && $params['parent'] > 0)
			{
				if ($where != '')
				{
					$where .= ' OR ';
				}

				$where .= "(p.`target` = '{$params['controller']}' AND p.`type` = 'controller'
						AND p.`parent` = {$params['parent']})";
			}

			if ($where != '')
			{
				// Table permission
				$permissionId = 0;
				$resultPermission = $this->dbAdapter->query("SELECT p.`id`
											FROM `permission` p
											WHERE {$where}", $dbAdapter::QUERY_MODE_EXECUTE);

				foreach ($resultPermission as $result)
				{
					$permissionId = $result['id'];
				}

				if ($permissionId > 0)
				{
					$roleId = AgereString::getStringAssocDigit($user['roleId'], 'role');
					$roleId = implode(', ', $roleId);
					$userId = AgereString::getStringAssocDigit($user['id'], 'user');

					// Table permission_access
					$resultAccess = $this->dbAdapter->query("SELECT pa.`roleId`, pa.`access`
											FROM `permission_access` pa
											WHERE pa.`permissionId` = {$permissionId}
											AND (pa.`roleId` IN ({$roleId})
											OR pa.`roleId` = '{$userId}')", $dbAdapter::QUERY_MODE_EXECUTE);

					// Access to page
					if (! $resultAccess->count())
					{
						$viewModel->permissionDenied = false;
					}
				}
			}
		}
	}

	/**
	 * MVC preDispatch Event
	 *
	 * @deprecated
	 */
	public function mvcPreDispatch($event) {
	    die(__METHOD__);
		//\Zend\Debug\Debug::dump([$this->roles, spl_object_hash($this), __METHOD__]);
		$params = $event->getRouteMatch()->getParams();


		$app = $event->getParam('application');
		$sm = $app->getServiceManager();
		/** @var \Popov\ZfcUser\Event\Authentication $auth */
		$auth = $sm->get('Popov\Users\Event\Authentication');

		// Access to page
		$this->setInitAcl($event);
		$auth->preDispatch($event);
		$this->getDbPage($event, $params); // here set $permissionDenied
	}

	//public function init(ModuleManager $mm)
	public function attachEvents($e) {
		$mm = $e->getApplication();
		$sm = $mm->getServiceManager();

		/*$userAuth = new UserAuthentication();
		$authService = $userAuth->getAuthService();
		$userHelper = new \Popov\Users\View\Helper\User($authService);
		$user = $userHelper->getUser();*/

        #$uAuth = $sm->get('UserAuthentication'); //@FIXME improve realisation
        #$uAuth->unAuthenticate();


		//try {
            $vhm = $sm->get('ViewHelperManager');
        /** @var \Popov\ZfcUser\View\Helper\User $userHelper */
            $userHelper = $vhm->get('user');
			$user = $userHelper->getUser();
			//$user = $sm->get('ViewHelperManager')->get('user');
		//} catch(\Exception $e) {
		//	\Zend\Debug\Debug::dump($e->getMessage());
		//	\Zend\Debug\Debug::dump($e->getTraceAsString());
		//	die(__METHOD__);
		//}



		$mm->getEventManager()->getSharedManager()
			->attach('Popov\AuthorizedPersons\Controller\AuthorizedPersonsController', ['authorized-persons.delete'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);


		$mm->getEventManager()->getSharedManager()
			->attach('Popov\Autocarts\Controller\AutocartsController', ['autocarts.delete'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\Brand\Controller\BrandController', ['brand.deleteAction'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		//$mm->getEventManager()->getSharedManager()
		//	->attach('Popov\Buyers\Controller\BuyersController', ['buyers.delete'],
		//		function(Event $evt) use($user)
		//		{
		//			return $this->_accessPage($evt, $user);
		//		}
		//	);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\CallCenter\Controller\CallCenterController', ['callCenter.delete'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\CarAssembly\Controller\CarAssemblyController', ['carAssembly.delete'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\CarAction\Controller\CarActionController', ['carAction.delete'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\CarEquipment\Controller\CarEquipmentController', ['carEquipment.delete'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\CarModel\Controller\CarModelController', ['carModel.delete'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\CarSubgroup\Controller\CarSubgroupController', ['carSubgroup.delete'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

        $mm->getEventManager()->getSharedManager()
            ->attach('Popov\Cart\Controller\CartController', ['cart.addToCart', 'cart.removeItem'],
                function(Event $evt) use($user)
                {
                    return $this->_accessPage($evt, $user);
                }
            );

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\Category\Controller\CategoryController', ['category.delete'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\City\Controller\CityController', ['city.delete'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\Clients\Controller\ClientsController', ['clients.delete'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\CustomerResponse\Controller\CustomerResponseController', ['customerResponse.delete'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\Dealerships\Controller\DealershipsController', ['dealerships.delete'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\Department\Controller\DepartmentController', ['department.delete'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\Documents\Controller\DocumentsController', ['documents.deleteFileAction', 'documents.deleteAction'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\Files\Controller\FilesController', ['files.deleteFileAction'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\FileUpload\Controller\FileUploadController', ['fileUpload.index'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\JobTitles\Controller\JobTitlesController', ['job-titles.delete'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\Logistics\Controller\LogisticsController', ['logistics.deleteAction'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\OptionalEquipment\Controller\OptionalEquipmentController', ['optionalEquipment.delete'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\OrderSale\Controller\OrderSaleController', ['orderSale.deleteOrderSaleStore',
					'orderSale.deleteOrderSalePaid', 'orderSale.issueOrderSale'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

        $mm->getEventManager()->getSharedManager()
            ->attach('Popov\PromotionalProducts\Controller\PromotionalProductsController', ['promotionalProducts.delete'],
                function(Event $evt) use($user)
                {
                    return $this->_accessPage($evt, $user);
                }
            );

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\Regions\Controller\RegionsController', ['regions.delete'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\Roles\Controller\RolesController', ['roles.delete'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

        $mm->getEventManager()->getSharedManager()
            ->attach('Popov\Shop\Controller\AbstractShopController', ['shopSpares.deleteOrder', 'shopPromotionalProducts.deleteOrder'],
                function(Event $evt) use($user)
                {
                    return $this->_accessPage($evt, $user);
                }
            );

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\Status\Controller\StatusController', ['status.delete'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\StatusBuyer\Controller\StatusBuyerController', ['statusBuyer.delete'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\Store\Controller\StoreController', ['store.delete', 'store.changeStatusAction',
					'store.addToCarOptionalEquipment', 'store.deleteOptionalEquipment', 'store.deleteOurAccessories',
					'store.addToTotalPriceList'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\Supplier\Controller\SupplierController', ['supplier.delete'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\Towns\Controller\TownsController', ['towns.delete'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\TypeSale\Controller\TypeSaleController', ['typeSale.delete'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt->getParam('controller').'.delete', $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\Users\Controller\UsersController', ['users.delete', 'users.deletePhoto'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\Users\Controller\StaffController', ['staff.delete', 'staff.deletePhoto'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach('Popov\Warranty\Controller\WarrantyController', ['warranty.deleteAction'],
				function(Event $evt) use($user)
				{
					return $this->_accessPage($evt, $user);
				}
			);

	}

	protected function _accessPage($event, $user)
	{
		//$sm = $event->getTarget()->getServiceLocator();
		/** @var \Popov\ZfcUser\Event\Authentication $auth */
		//$auth = $sm->get('Popov\Users\Event\Authentication');

        /** @var \Popov\ZfcUser\Acl\Acl $acl */
		$acl = $this->auth->getAcl();

		//\Zend\Debug\Debug::dump(get_class($acl)); die(__METHOD__);

		$eventName = $event->getName();

		// Target
		$target = preg_replace('/([a-z]+)+([A-Z])/', '$1-$2', $eventName);
		$target = str_replace(['.', '-action'], ['/', ''], strtolower($target));

		// Access
		$access = Acl::getAccess();
		$accessTotal = Acl::getAccessTotal();

		// Allowed
		$allowed = [$acl->isAllowed($user['mnemo'], 'all', $accessTotal)];

		if ($acl->hasResource($target)) {
			$allowed[] = $acl->isAllowed($user['mnemo'], $target, $accessTotal);
			$allowed[] = $acl->isAllowed($user['mnemo'], $target, $access['write']);
		}

		$message = (in_array(true, $allowed)) ? '' : 'Доступ запрещен';

		return ['message' => $message];
	}


	public function getConfig()
	{
		return include __DIR__ . '/../config/module.config.php';
	}

}