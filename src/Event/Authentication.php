<?php
/**
 * File for Event Class
 *
 * @category  User
 * @package   User_Event
 * @author    Marco Neumann <webcoder_at_binware_dot_org>
 * @copyright Copyright (c) 2011, Marco Neumann
 * @license   http://binware.org/license/home/type:new-bsd New BSD License
 */

/**
 * @namespace
 */
namespace Popov\ZfcUser\Event;

/**
 * @uses Zend\Mvc\MvcEvent
 * @uses User\Controller\Plugin\UserAuthentication
 * @uses User\Acl\Acl
 */
use Popov\ZfcUser\Controller\Plugin\UserAuthentication;
use Popov\ZfcUser\Model\User;
use Zend\Mvc\MvcEvent as MvcEvent;
	//Zend\Permissions\Acl\Acl as Acl,
use Zend\Session\Container as SessionContainer;
use Zend\ServiceManager\Exception;

use Popov\ZfcUser\Controller\Plugin\User as UserPlugin;
use Popov\Agere\String\StringUtils as AgereString;
use Popov\ZfcUser\Controller\Plugin\UserAuthentication as AuthPlugin;
use Popov\ZfcUser\Acl\Acl;
use Zend\Stdlib\Request;

/**
 * Authentication Event Handler Class
 *
 * This Event Handles Authentication
 *
 * @category  User
 * @package   User_Event
 * @copyright Copyright (c) 2011, Marco Neumann
 * @license   http://binware.org/license/home/type:new-bsd New BSD License
 */
class Authentication
{
    /** @var User */
    protected $userPlugin;

    /** @var Request */
    protected $request;

    /** @var AuthPlugin */
    protected $_userAuth = null;

    /** @var Acl */
    protected $acl = null;

    protected $roles;

    protected $adapter;

    protected $accessDefault = 6;

    protected $denyDefault = 0;

    public function setUserPlugin($user)
    {
        $this->userPlugin = $user;
    }

    public function getUserPlugin()
    {
        return $this->userPlugin;
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Sets Authentication Plugin
     *
     * @param AuthPlugin $userAuthenticationPlugin
     * @return $this
     */
    public function setUserAuthenticationPlugin(AuthPlugin $userAuthenticationPlugin)
    {
        $this->_userAuth = $userAuthenticationPlugin;

        return $this;
    }

    /**
     * Gets Authentication Plugin
     *
     * @return UserAuthentication
     */
    public function getUserAuthenticationPlugin()
    {
        return $this->_userAuth;
    }

    /**
     * Sets ACL Class
     *
     * @param Acl $acl
     * @return $this
     */
    public function setAcl(Acl $acl)
    {
        $this->acl = $acl;

        return $this;
    }

    /**
     * Gets ACL Class
     *
     * @return Acl
     */
    public function getAcl()
    {
        if ($this->acl === null) {
            $this->acl = new Acl([]);
        }

        return $this->acl;
    }

    /**
     * @return Acl
     * @deprecated
     */
    public function getAclClass()
    {
        return $this->getAcl();
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setDbAdapter($adapter)
    {
        $this->adapter = $adapter;
    }

    public function getDbAdapter()
    {
        return $this->adapter;
    }

    /**
	 * Initialization ACL resources for current user
	 */
	public function init() {
		$this->initAcl();
		//$this->basicAuthentication();
	}

	public function initCron()
    {
        $this->initAcl();
        // Всі інші дії авторизіції; взяти їх з \Popov\Users\Controller\UsersController::loginAction
    }


	public function basicAuthentication($login, $password) {
		//\Zend\Debug\Debug::dump([$login, $password]);die(__METHOD__);
		if ($login && $password) {
			//$query = "SELECT authentication_id FROM authentication WHERE username = ? AND password = ?";
			//add your own auth code here. I have it check against a database table and return a value if found.

			//$sm = $this->getUserAuthenticationPlugin()->getConroller()->getServiceLocator();
			//$request = $sm->get('Request');

			$uAuth = $this->getUserAuthenticationPlugin(); //@FIXME improve realisation
			$authService = $uAuth->getAuthService();

			/** @var \Popov\Agere\Authentication\Adapter\DbTable\CredentialTreatmentAdapter $authAdapter */
			$authAdapter = $uAuth->getAuthAdapter();
			/** @var \Popov\ZfcUser\Service\UsersService $userService */
			//$userService = $sm->get('UsersService');


			$email = $login;
			$passwordHash = \Popov\ZfcUser\Service\UsersService::getHashPassword($password);
			$authAdapter->setIdentity($email);
			$authAdapter->setCredential($passwordHash);
			$authAdapter->setWhere(['remove' => [0]]);

			/** @var \Zend\Authentication\Result $result */
			$result = $authService->authenticate($authAdapter);

			//\Zend\Debug\Debug::dump($result->getMessages()); die(__METHOD__);

			if ($result->isValid()) {
				return true;
			} else {
				throw new \SOAPFault("Incorrect username or password.", 401);

			}

		} else {
			throw new \SOAPFault("Invalid username and password format. Values may not be empty and are case-sensitive.", 401);

		}
	}

	/**
	 * @throws Exception\RuntimeException Warning! This method must be called only once
	 */
	private function initAcl(/*MvcEvent $e*/) {
		/*if ($this->roles) {
			throw new Exception\RuntimeException('ACL initialization must be only once');
		}*/

		foreach ($this->roles as $role => $resources) {
			$role = new \Zend\Permissions\Acl\Role\GenericRole($role);
			$this->acl->addRole($role);
			//adding resources
			foreach ($resources as $resource) {
                //\Zend\Debug\Debug::dump([$resource['target'], /*$this->roles, __METHOD__*/]);
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
	}


	public function mvcPreDispatch($event) {
        //\Zend\Debug\Debug::dump([$this->roles, spl_object_hash($this), __METHOD__]);
		$params = $event->getRouteMatch()->getParams();

		// Access to page
		$result = $this->preDispatch($event);

        $this->getDbPage($event, $params); // here set $permissionDenied

		return $result;
	}

	/**
	 * preDispatch Event Handler
	 *
	 * @param \Zend\Mvc\MvcEvent $event
	 * @throws \Exception
	 * @todo Зробити повний рефакторинг прав доступу. Невідомо чому тут додається $defaultResource...
	 */
    public function preDispatch(MvcEvent $event) {
        static $defaultResource;


        //@todo - Should we really use here and Controller Plugin?
        $userAuth = $this->getUserAuthenticationPlugin();
        $viewModel = $event->getViewModel();
        $viewModel->permissionDenied = true;
        //$this->_Acl = $viewModel->acl;

        $roleMnemos = [Acl::DEFAULT_ROLE];

        $access = Acl::getAccess();
        $accessTotal = Acl::getAccessTotal();

        $userPlugin = $this->getUserPlugin();
        /** @var UserPlugin $userPlugin */
        if (($user = $userPlugin->current()) && $user->getId()) {
            $roleMnemos = $userPlugin->asArray('roles', 'mnemo');

            if (!$userPlugin->isAdmin()) {
                // Update expire login
                $sessionAuth = new SessionContainer('Zend_Auth');
                $sessionAuth->setExpirationSeconds(3600); // 60 minutes
            }

            if (!$defaultResource) {
                // Set default resource
                $defaultResource = ['files/get'];
                foreach ($defaultResource as $target) {
                    $this->acl->addResource(new \Zend\Permissions\Acl\Resource\GenericResource($target));
                    $this->acl->allow($roleMnemos, $target, Acl::getAccessTotal());
                }
            }
        }

        $allowed = [$this->acl->isAllowed($roleMnemos, 'all', $accessTotal)];

        // Allowed session
        if (isset($_SESSION['location'])) {
            $target = $_SESSION['location']['controller'] . '/' . $_SESSION['location']['action'];
            // Allowed
            if ($this->acl->hasResource($target)) {
                $allowed[] = $this->acl->isAllowed($roleMnemos, $target, $accessTotal);
                $allowed[] = $this->acl->isAllowed($roleMnemos, $target, $access['write']);
                $allowed[] = $this->acl->isAllowed($roleMnemos, $target, $access['read']);
            }
            if (in_array(true, $allowed)) {
                $route = 'default';
                $dataUrl = [
                    'controller' => $_SESSION['location']['controller'],
                    'action'     => $_SESSION['location']['action'],
                ];
                if (isset($_SESSION['location']['id'])) {
                    $route = 'default/id';
                    $dataUrl['id'] = $_SESSION['location']['id'];
                }
                $url = $event->getRouter()->assemble($dataUrl, ['name' => $route]);
                $response = $event->getResponse();
                $response->getHeaders()->addHeaderLine('Location', $url);
                $response->setStatusCode(302);
                $response->sendHeaders();
                unset($_SESSION['location']);
                exit;
            }
        }

        // Resource
        $routeMatch = $event->getRouteMatch();
        $controller = $routeMatch->getParam('controller');
        $action = $routeMatch->getParam('action');
        $target = $controller . '/' . $action;

        //$sm = $event->getApplication()->getServiceManager();
        #$cpm = $sm->get('ControllerPluginManager');
        /** @var \Zend\Mvc\Controller\Plugin\Url $urlPlugin */
        #$urlPlugin = $cpm->get('url');
        #$fullTarget = $urlPlugin->fromRoute($routeMatch->getMatchedRouteName(), $routeMatch->getParams());

        #$request = $sm->get('Request');
        #$targetFull = $request->getUri()->getPath();

        $targetFull = $event->getRouter()->assemble($routeMatch->getParams(), [
            'name' => $routeMatch->getMatchedRouteName()
        ]);

        // Allowed
        if ($this->acl->hasAccessByRoles($roleMnemos, $target)
            || $this->acl->hasAccessByRoles($roleMnemos, $targetFull)
        ) {
            return true;
        }

        if ($userAuth->hasIdentity()) {
            $event->stopPropagation(true); // very important string
            $viewModel->permissionDenied = false;

            return false;
        } else {
            $_SESSION['location'] = $routeMatch->getParams();
            $url = $event->getRouter()->assemble([
                'controller' => 'user',
                'action' => 'login',
            ], ['name' => 'default']);
        }

        if ($url) {
            $response = $event->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);
            $response->sendHeaders();
            exit('Probably you enable showing DEPRECATED error messages
                and header already has been send through ServiceLocatorAwareInterface trigger_error');
        }
    }

	/**
	 * @todo: Implement more perfect structure
	 * @param $dbAdapter
	 * @return mixed
	 */
	public function getDbRoles($dbAdapter) {
        // making the roles array
        // @todo Перемістити в БД і налаштовувати через адмінку
        // @todo Реалізувати можливість швидкого додавання через конфіги
        $this->roles['guest'][] = ['target' => 'inquiry/form', 'access' => Acl::ACCESS_TOTAL];
        $this->roles['guest'][] = ['target' => 'inquiry/thanks', 'access' => Acl::ACCESS_TOTAL];

        $this->roles['guest'][] = ['target' => 'soap-api/index', 'access' => Acl::ACCESS_TOTAL];
        $this->roles['guest'][] = ['target' => 'soap-api/get', 'access' => Acl::ACCESS_TOTAL];
        //$this->roles['guest'][] = ['target' => 'soap-api/post', 'access' => Acl::ACCESS_TOTAL];

        $this->roles['guest'][] = ['target' => 'user/login', 'access' => Acl::ACCESS_TOTAL];
        $this->roles['guest'][] = ['target' => 'user/logout', 'access' => Acl::ACCESS_TOTAL];
        $this->roles['guest'][] = ['target' => 'user/forgot-password', 'access' => Acl::ACCESS_TOTAL];
        // Table roles to array

        $resultRolesArray = $this->getResultRolesArray($dbAdapter);

        // is permission module enabled
        if (!class_exists(\Popov\ZfcPermission\Module::class)) {
            return $this->roles;
        }

		$sql = <<<SQL
SELECT p.`target`, pa.`roleId`, pa.`access`
FROM `permission_access` pa
LEFT JOIN `permission` p ON pa.`permissionId` = p.`id`
WHERE p.`entityId` = 0 AND p.`parent` = 0
SQL;
		$results = $dbAdapter->query($sql, $dbAdapter::QUERY_MODE_EXECUTE);

		//\Zend\Debug\Debug::dump($resultRolesArray); die(__METHOD__);

		foreach ($results as $result) {
			// Parse roleId
			$assocDigit = AgereString::parseStringAssocDigit($result['roleId']);
			//\Zend\Debug\Debug::dump($assocDigit);

			if ($assocDigit['field'] == 'role' && isset($resultRolesArray[$assocDigit['id']])) {
				$this->roles[$resultRolesArray[$assocDigit['id']]['mnemo']][] = [
					'target' => $result['target'],
					'access' => $result['access'],
				];
			}
		}
		//die(__METHOD__);

		return $this->roles;
	}

	private function getResultRolesArray($dbAdapter) {
		static $resultRolesArray;
		static $accessDefault = 6;

		if (!$resultRolesArray) {
			// Table roles
			$resultRoles = $dbAdapter->query(
				'SELECT r.`id`, r.`mnemo`, r.`resource`FROM `role` r',
				$dbAdapter::QUERY_MODE_EXECUTE
			);

			foreach ($resultRoles as $result) {
				if ($result['resource'] == 'all') {
					$this->roles[$result['mnemo']][] = [
						'target' => $result['resource'],
						'access' => $accessDefault,
					];
				} else {
					$resultRolesArray[$result['id']] = $result;
				}
			}
		}

		return $resultRolesArray;
	}

	public function getDbPage(MvcEvent $e, $params) {
		static $accessDefault = 6;

		$sm = $e->getApplication()->getServiceManager();
		$dbAdapter = $this->getDbAdapter();
		$where = '';

		//ini_set('display_errors', 'on');
		//error_reporting(-1);
		
		$userHelper = $sm->get('ViewHelperManager')->get('user');
		$simpler = $sm->get('ControllerPluginManager')->get('simpler');
		$user = $userHelper->getUser();
		//$user = $userHelper->current();
		$viewModel = $e->getViewModel();


		// Acl class
		//$Acl = $viewModel->acl;
		$acl = $this->getAcl();

        //if (!$acl->hasResource('all')) { //@todo видалити і реалізувати нормальну роботу без Permission
        //    return;
        //}


        $role = $user ? $user->getRoles()->first() : false;
		if ($role && $role->getMnemo() && !$acl->isAllowed($role->getMnemo(), 'all', $accessDefault)) {
			// Where
			if (isset($params['id']) && $params['id'] > 0) {
				$where = "(p.`target` = '{$params['controller']}/{$params['action']}/{$params['id']}'
						AND p.`entityId` = {$params['id']} AND p.`type` = 'action')";
			}
			if (isset($params['parent']) && $params['parent'] > 0) {
				if ($where != '') {
					$where .= ' OR ';
				}
				$where .= "(p.`target` = '{$params['controller']}' AND p.`type` = 'controller' AND p.`parent` = {$params['parent']})";
			}

			if ($where != '') {
				// Table permission
				$permissionId = 0;
				$resultPermission = $dbAdapter->query(
					"SELECT p.`id` FROM `permission` p WHERE {$where}",
					$dbAdapter::QUERY_MODE_EXECUTE
				);

				foreach ($resultPermission as $result) {
					$permissionId = $result['id'];
				}
				if ($permissionId > 0) {
					//$roleId = AgereString::getStringAssocDigit($user['roleId'], 'role');
					//$roleId = implode(', ', $roleId);
					//$userId = AgereString::getStringAssocDigit($user['id'], 'user');
					$currentUser = $userHelper->current();
					$roleId = AgereString::getStringAssocDigit($simpler($currentUser->getRoles())->asArray('id'), 'role');
					$roleId = implode(', ', $roleId);
					$userId = AgereString::getStringAssocDigit($currentUser->getId(), 'user');
					
					//$roleId = $simpler($user->getRoles())->asArray('id');
					//$roleId = implode(', ', $roleId);
					//$userId = AgereString::getStringAssocDigit($user['id'], 'user');

					$sql = <<<SQL
SELECT pa.`roleId`, pa.`access`
FROM `permission_access` pa
WHERE pa.`permissionId` = {$permissionId}
AND (pa.`roleId` IN ({$roleId})
OR pa.`roleId` = '{$userId}')
SQL;
					//\Zend\Debug\Debug::dump($sql); die(__METHOD__);
					// Table permission_access
					$resultAccess = $dbAdapter->query($sql, $dbAdapter::QUERY_MODE_EXECUTE);
					// Access to page

					if (!$resultAccess->count()) {
						$viewModel->permissionDenied = false;
					}
				}
			}
		}
	}
}
