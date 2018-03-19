<?php

namespace Popov\ZfcUser\View\Helper;

use Popov\ZfcUser\Model\Repository\UserRepository;
use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;
use Zend\Session\Container as SessionContainer;
use Popov\ZfcUser\Controller\Plugin\UserPlugin as UserPlugin;
use Popov\Simpler\Plugin\SimplerPlugin;

class UserHelper extends AbstractHelper
{
    /** @var UserPlugin */
    protected $userPlugin;

    /**
     * @param UserPlugin $userPlugin
     * @return $this
     */
    public function setUserPlugin(UserPlugin $userPlugin)
    {
        $this->userPlugin = $userPlugin;

        return $this;
    }

    /** @return UserPlugin */
    public function getUserPlugin()
    {
        if (null === $this->userPlugin) {
            if (!$this->getView()) {
                // Як вирішити? Це значить що десь йде виклик хелперу не через ServiceManager,
                // а на пряму, тобто
                // $user = new \Popov\Users\View\Helper\User($authService);
                // Що робити? Подивитись по Exception де це викликається і замінити на
                // $userHelper = $vhm->get('user');
                // $currentUser = $userHelper->getUser();
                // @see Popov\Fields\View\Helper\Factory\FieldFactory
                throw new \Exception('trp ;p ;');
            }
            $sm = $this->getView()->getHelperPluginManager()->getServiceLocator();
            //$sm = $this->getServiceLocator();
            $cpm = $sm->get('ControllerPluginManager');
            $this->userPlugin = $cpm->get('user');
        }

        return $this->userPlugin;
    }

    public function current()
    {
        return $this->getUserPlugin()->current();
    }

    public function getBy($value, $field = SimplerPlugin::DEFAULT_FIELD)
    {
        return $this->getUserPlugin()->getBy($value, $field);
    }

    public function asString($collectionName, $field = SimplerPlugin::DEFAULT_FIELD)
    {
        return $this->getUserPlugin()->asString($collectionName, $field);
    }

    public function asArray($collectionName, $field = SimplerPlugin::DEFAULT_FIELD)
    {
        return $this->getUserPlugin()->asArray($collectionName, $field);
    }

    public function isAdmin()
    {
        return $this->getUserPlugin()->isAdmin();
    }

    /**
     * @param string $resource
     * @return bool
     * @throws \Exception
     */
    public function hasAccess($resource)
    {
        return $this->getUserPlugin()->hasAccess($resource);
    }

    public function collectionToArray($collection, $fields = ['email'])
    {
        $items = [];
        foreach ($collection as $item) {
            $data = [];
            foreach ($fields as $field) {
                $data[$field] = $item->{'get' . ucfirst($field)}();
            }
            $items[$item->getId()] = $data;
        }

        return $items;
    }
}