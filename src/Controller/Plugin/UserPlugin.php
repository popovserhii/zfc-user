<?php
/**
 * User Plugin
 *
 * @category Popov
 * @package Popov_ZfcUser
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 11.02.2016 15:08
 */

namespace Popov\ZfcUser\Controller\Plugin;

use Popov\ZfcUser\Helper\UserHelper;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Popov\ZfcPermission\Acl\Acl;
use Popov\ZfcUser\Service\UserService;
use Popov\ZfcUser\Model\User;
use Popov\Simpler\SimplerHelper;

class UserPlugin extends AbstractPlugin
{
    /** @var UserHelper */
    protected $userHelper;

    /** @var Acl */
    protected $acl;

    /** @var SimplerHelper */
    protected $simpler;

    /**
     * @param UserHelper $userHelper
     * @param Acl $acl
     */
    public function __construct(
        UserHelper $userHelper,
        Acl $acl
    ) {
        $this->userHelper = $userHelper;
        $this->acl = $acl;
    }

    public function setSimpler($simpler)
    {
        $this->simpler = $simpler;
    }

    public function isAdmin()
    {
        return $this->userHelper->isAdmin();
    }

    public function hasAccess($resource)
    {
        return $this->userHelper->hasAccess($resource);
    }

    /**
     * @return UserService
     */
    public function getUserService()
    {
        return $this->userHelper->getUserService();
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
        return $this->userHelper->current();
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
        return $this->userHelper->getBy($value, $field);
    }

    public function asString($collectionName, $field = SimplerHelper::DEFAULT_FIELD)
    {
        return $this->userHelper->asString($collectionName, $field);
    }

    public function asArray($collectionName, $field = SimplerHelper::DEFAULT_FIELD)
    {
        return $this->userHelper->asArray($collectionName, $field);
    }
}