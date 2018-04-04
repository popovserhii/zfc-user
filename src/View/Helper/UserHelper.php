<?php

namespace Popov\ZfcUser\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Popov\Simpler\SimplerHelper;
use Popov\ZfcUser\Helper\UserHelper as BaseUserHelper;

class UserHelper extends AbstractHelper
{
    /**
     * @var BaseUserHelper
     */
    protected $userHelper;

    public function __construct(BaseUserHelper $userHelper)
    {
        $this->userHelper = $userHelper;
    }

    /**
     * @return BaseUserHelper
     */
    public function getUserHelper()
    {
        if (null === $this->userHelper) {
            $sm = $this->getView()->getHelperPluginManager()->getServiceLocator();
            $cpm = $sm->get('ControllerPluginManager');
            $this->userHelper = $cpm->get('user');
        }

        return $this->userHelper;
    }

    public function current()
    {
        return $this->getUserHelper()->current();
    }

    public function getBy($value, $field = SimplerHelper::DEFAULT_FIELD)
    {
        return $this->getUserHelper()->getBy($value, $field);
    }

    public function asString($collectionName, $field = SimplerHelper::DEFAULT_FIELD)
    {
        return $this->getUserHelper()->asString($collectionName, $field);
    }

    public function asArray($collectionName, $field = SimplerHelper::DEFAULT_FIELD)
    {
        return $this->getUserHelper()->asArray($collectionName, $field);
    }

    public function isAdmin()
    {
        return $this->getUserHelper()->isAdmin();
    }

    /**
     * @param string $resource
     * @return bool
     * @throws \Exception
     */
    public function hasAccess($resource)
    {
        return $this->getUserHelper()->hasAccess($resource);
    }
}