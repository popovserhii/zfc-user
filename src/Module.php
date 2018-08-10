<?php
namespace Popov\ZfcUser;

use Zend\EventManager\StaticEventManager;
use Zend\Session\Container as SessionContainer;
use	Zend\Mvc\MvcEvent;
use	Zend\ModuleManager\ModuleManager;
use	Zend\EventManager\Event;
use	Zend\Http\Request as HttpRequest;
use Zend\Console\Request as ConsoleRequest;
use	Popov\ZfcPermission\Acl\Acl;

class Module {

	protected $dbAdapter;
	protected $resultRolesArray;
	protected $accessDefault = Acl::ACCESS_TOTAL;
	protected $denyDefault = 0;
	protected $roles;
	protected $acl;

    public function getConfig()
    {
        $config = include __DIR__ . '/../config/module.config.php';
        $config['service_manager'] = $config['dependencies'];
        unset($config['dependencies']);

        return $config;
    }

	public function onBootstrap(MvcEvent $e) {
		$eventManager = $e->getApplication()->getEventManager();
		$sm = $e->getApplication()->getServiceManager();
		$sharedEvents = $eventManager->getSharedManager();



        if ($e->getRequest() instanceof HttpRequest
            && (false === strpos($e->getRequest()->getUri()->getPath(), 'soap')) // @todo Придумати як видалити цю перевірку
        ) {

            /*$vhm = $sm->get('ViewHelperManager');

            #$this->attachEvents($e);

            $this->auth = $auth = $sm->get(Authentication::class);
            $this->userHelper = $vhm->get('user');

            $auth->init();

            $sharedEvents->attach(\Zend\Mvc\Controller\AbstractActionController::class, 'dispatch', [
                $auth,
                'mvcPreDispatch',
            ], 1000);*/
        }
    }

    public function attachEvents($e) {
		$mm = $e->getApplication();

		$mm->getEventManager()->getSharedManager()
			->attach(\Popov\ZfcRole\Controller\RoleController::class, ['roles.delete'],
				function(Event $evt)
				{
					return $this->_accessPage($evt);
				}
			);


		$mm->getEventManager()->getSharedManager()
			->attach(\Popov\ZfcUser\Controller\UserController::class, ['users.delete', 'users.deletePhoto'],
				function(Event $evt)
				{
					return $this->_accessPage($evt);
				}
			);

		$mm->getEventManager()->getSharedManager()
			->attach(\Popov\ZfcUser\Controller\StaffController::class, ['staff.delete', 'staff.deletePhoto'],
				function(Event $evt)
				{
					return $this->_accessPage($evt);
				}
			);
	}

	protected function _accessPage($event)
	{
	    $user = $this->userHelper->getUser();
		//$sm = $event->getTarget()->getServiceLocator();
		/** @var \Popov\ZfcUser\Event\Authentication $auth */
		//$auth = $sm->get('Popov\Users\Event\Authentication');

        /** @var \Popov\ZfcPermission\Acl\Acl $acl */
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
        $allowed = [false];
        foreach ($user->getRoles() as $role) {
            if ($bool = $acl->isAllowed($role->getMnemo(), 'all', $accessTotal)) {
                $allowed[] = [true];
            }
        }
        //$allowed = [$acl->isAllowed($user['mnemo'], 'all', $accessTotal)];
        //$allowed = [in_array(true, $allows, true)];

		if ($acl->hasResource($target)) {
			$allowed[] = $acl->isAllowed($user['mnemo'], $target, $accessTotal);
			$allowed[] = $acl->isAllowed($user['mnemo'], $target, $access['write']);
		}

		$message = (in_array(true, $allowed)) ? '' : 'Доступ запрещен';

		return ['message' => $message];
	}
}