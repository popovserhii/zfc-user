<?php
/**
 * User Plugin
 *
 * @category Popov
 * @package Popov_User
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 11.02.2016 15:08
 */
namespace Popov\ZfcUser\Controller\Plugin;

use Zend\Stdlib\Exception;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Popov\ZfcUser\Acl\Acl;
use Popov\ZfcUser\Service\UserService;
use Popov\ZfcUser\Model\User;
use Agere\Simpler\Plugin\SimplerPlugin;

class UserPlugin extends AbstractPlugin
{
    /** @var UserService */
    protected $userService;

    /** @var Acl */
    protected $acl;

    /** @var SimplerPlugin */
    protected $simpler;

    /**
     * @param UserService $userService
     * @param Acl $acl
     */
    public function __construct(
        UserService $userService,
        Acl $acl
    ) {
        $this->userService = $userService;
        $this->acl = $acl;
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
     * @return UserPlugin;
     */
    public function getBy($value, $field = 'id')
    {
        $entity = $this->getUserService()->getRepository()->findOneBy([$field => $value]);

        return $entity;
    }

    public function asString($collectionName, $field = SimplerPlugin::DEFAULT_FIELD)
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

    public function asArray($collectionName, $field = SimplerPlugin::DEFAULT_FIELD)
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