<?php
/**
 * User Plugin
 *
 * @category Agere
 * @package Agere_Users
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 11.02.2016 15:08
 */
namespace Popov\ZfcUser\Controller\Plugin;

use Zend\Stdlib\Exception;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

use Popov\ZfcUser\Acl\Acl;
use Popov\ZfcUser\Service\UserService;
use Popov\ZfcUser\Model\User as UserModel;
use Agere\Simpler\Plugin\SimplerPlugin;

class User extends AbstractPlugin {
	/** @var UserService */
	protected $userService;

	/** @var AuthenticationService  */
	protected $authService;

    /** @var Acl */
	protected $acl;
	
	/** @var SimplerPlugin */
	protected $simpler;

	/**
	 * @param AuthenticationService $authService
	 * @param UserService $userService
	 * @param Acl $acl
	 */
	public function __construct(
        AuthenticationService $authService,
        UserService $userService,
        Acl $acl
    ) {
		$this->authService = $authService;
		$this->userService = $userService;
		$this->acl = $acl;
	}
	
	public function setSimpler($simpler)
	{
		$this->simpler = $simpler;
	}

	/**
	 * @return mixed|null
	 * @deprecated
	 */
	public function getIdentity() {
		return $this->getAuthService()->getIdentity();
	}

	public function hasIdentity() {
		return $this->authService->hasIdentity();
	}

	public function isAdmin() {
        foreach ($this->current()->getRoles() as $role) {
            return $role->getResource() == 'all';
        }

		return false;
	}

    public function hasAccess($resource) {
        if ($this->isAdmin()) {
            return true;
        }
        $user = $this->current();

        return $this->acl->hasAccess($user, $resource);
    }

    public function hasAccessOld($resource) {
        if ($this->isAdmin()) {
            return true;
        }
        $user = $this->current();
        $resource = ltrim($resource, '/');
        foreach ($user->getRoles() as $role) {
            $allowed = ['all' => $this->acl->isAllowed($role->getMnemo(), 'all', Acl::getAccessTotal())];
            if ($this->acl->hasResource($resource)) {
                $allowed['total'] = $this->acl->isAllowed($role->getMnemo(), $resource, Acl::getAccessTotal());
                $allowed['write'] = $this->acl->isAllowed($role->getMnemo(), $resource, Acl::getAccess()['write']);
                $allowed['read'] = $this->acl->isAllowed($role->getMnemo(), $resource, Acl::getAccess()['read']);
            }
            //\Zend\Debug\Debug::dump([$allowed, $resource]); //die(__METHOD__);
            if (in_array(true, $allowed)) {
                return true;
            }
        }

        return false;
    }

	public function getUserService() {
		return $this->userService;
	}

	public function getAuthService() {
		return $this->authService;
	}

	public function getAcl() {
		return $this->acl;
	}

    /**
     * @return UserModel
     */
	public function current() {
		static $user;

		if (!$user) {
            $user = $this->getAuthService()->getIdentity();
            /*$identity = $this->getAuthService()->getIdentity();
            $user = ($identity && ($user = $this->getUserService()->find($identity['id'])))
                ? $user
                : $this->getUserService()->getObjectModel();*/
		}

		return $user;
	}

    /**
     * Find entity by params
     *
     * @param $value
     * @param string $field
     * @return User;
     */
    public function getBy($value, $field = 'id')
    {
        $entity = $this->getUserService()->getRepository()->findOneBy([$field => $value]);

        return $entity;
    }

    public function asString($collectionName, $field = SimplerPlugin::DEFAULT_FIELD) {
		$user = $this->current();
		
		if (!method_exists($user, $method = 'get' . ucfirst($collectionName))) {
			throw new Exception\RuntimeException(
                sprintf('Method for retrieve "%s" collection not exist', $collectionName)
            );
		}
		
		// if thrown exception then inject simpler plugin
		$collection = $user->{$method}();
		return $this->simpler->setContext($collection)->asString($field);
    }

    public function asArray($collectionName, $field = SimplerPlugin::DEFAULT_FIELD) {
        $user = $this->current();
		
		if (!method_exists($user, $method = 'get' . ucfirst($collectionName))) {
			throw new Exception\RuntimeException(
                sprintf('Method for retrieve "%s" collection not exist', $collectionName)
            );
		}
		
		// if thrown exception then inject simpler plugin
		$collection = $user->{$method}();
		return $this->simpler->setContext($collection)->asArray($field);
    }

	/**
	 * @param string $name
	 * @return string
	 * @throws Exception\RuntimeException
	 */
	/*public function run() {
		if (method_exists($this, $method = 'current' . ucfirst($name))) {
			return $this->{$method}();
		}

		throw new Exception\RuntimeException(sprintf(
			'Option with name %s is not supported. Allowed values: module, controller, action, router, route, request, view',
			$name
		));
	}*/
    /**
     * @return mixed
     * @deprecated
     */
	protected function getSm() {
        die(__METHOD__);
		return $this->getController()->getServiceLocator();
	}

	/*public function __invoke(...$args) {
		if (!$args) {
			return $this;
		}

		$name = isset($args[0]) ? $args[0] : false;
		!isset($args[1]) || $this->setContext($args[1]);

		return $this->run($name);
	}*/

}