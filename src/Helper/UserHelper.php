<?php
/**
 * User Helper
 *
 * @category Popov
 * @package Popov_ZfcUser
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 11.02.2016 15:08
 */
namespace Popov\ZfcUser\Helper;

use Zend\Stdlib\Exception;
use Popov\ZfcPermission\Acl\Acl;
use Popov\ZfcUser\Model\User;
use Popov\ZfcUser\Service\UserService;
use Popov\Simpler\SimplerHelper;

class UserHelper
{
    /** @var UserService */
    protected $userService;

    /** @var Acl */
    protected $acl;

    /** @var SimplerHelper */
    protected $simpler;

    /**
     * UserHelper constructor.
     *
     * @param UserService $userService
     * @param Acl $acl
     * @param SimplerHelper $simplerHelper
     */
    public function __construct(
        UserService $userService,
        Acl $acl,
        SimplerHelper $simplerHelper
    ) {
        $this->userService = $userService;
        $this->acl = $acl;
        $this->simpler = $simplerHelper;
    }

    public function setSimpler($simpler)
    {
        $this->simpler = $simpler;
    }

    public function isAdmin()
    {
        foreach ($this->current()->getRoles() as $role) {
            if ($role->getResource() == 'all') {
                return true;
            }
        }

        return false;
    }

    public function hasAccess($resource)
    {
        if ($this->isAdmin()) {
            return true;
        }
        $user = $this->current();

        return $this->acl->hasAccess($user, $resource);
    }

    public function getUserService()
    {
        return $this->userService;
    }

    public function getAcl()
    {
        return $this->acl;
    }

    /**
     * @return User
     */
    public function current()
    {
        static $user;
        if (!$user) {
            $user = $this->getUserService()->getCurrent();
        }

        return $user;
    }

    /**
     * Find entity by params
     *
     * @param $value
     * @param string $field
     * @return User
     */
    public function getBy($value, $field = 'id')
    {
        $entity = $this->getUserService()->getRepository()->findOneBy([$field => $value]);

        return $entity;
    }

    public function asString($collectionName, $field = SimplerHelper::DEFAULT_FIELD)
    {
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

    public function asArray($collectionName, $field = SimplerHelper::DEFAULT_FIELD)
    {
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
}