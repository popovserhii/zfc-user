<?php
/**
 * File for UserAuthentication Class
 *
 * @category   User
 * @package    User_Controller
 * @subpackage User_Controller_Plugin
 * @author     Marco Neumann <webcoder_at_binware_dot_org>
 * @copyright  Copyright (c) 2011, Marco Neumann
 * @license    http://binware.org/license/home/type:new-bsd New BSD License
 */

namespace Popov\ZfcUser\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Authentication\AuthenticationService;
//use Popov\Agere\Authentication\Adapter\DbTable\CredentialTreatmentAdapter as AuthAdapter;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Authentication\Adapter\Exception\RuntimeException;

/**
 * Class for User Authentication
 *
 * Handles Auth Adapter and Auth Service to check Identity
 *
 * @category   User
 * @package    User_Controller
 * @subpackage User_Controller_Plugin
 * @copyright  Copyright (c) 2011, Marco Neumann
 * @license    http://binware.org/license/home/type:new-bsd New BSD License
 */
class UserAuthentication extends AbstractPlugin
{
    const SOLT = 'G6t8?Mj$7h#ju';

    /**
     * @var CredentialTreatmentAdapter
     */
    protected $_authAdapter = null;

    /**
     * @var AuthenticationService
     */
    protected $_authService = null;


    /**
     * Check if Identity is present
     *
     * @return bool
     */
    public function hasIdentity()
    {
        return $this->getAuthService()->hasIdentity();
    }

    /**
     * Return current Identity
     *
     * @return mixed|null
     */
    public function getIdentity()
    {
        return $this->getAuthService()->getIdentity();
    }

    /**
     * Sets Auth Adapter
     *
     * @param CredentialTreatmentAdapter $authAdapter
     * @return UserAuthentication
     */
    public function setAuthAdapter(CredentialTreatmentAdapter $authAdapter) {
        $this->_authAdapter = $authAdapter;

        return $this;
    }

	/**
	 * Returns Auth Adapter
	 *
	 * @return null|CredentialTreatmentAdapter
	 * @throws \Zend\Authentication\Adapter\Exception\RuntimeException
	 */
	public function getAuthAdapter() {
        if ($this->_authAdapter === null) {
            //$this->setAuthAdapter(new AuthAdapter());
			throw new RuntimeException(__CLASS__ . '::_authAdapter should be set via setAuthAdapter() method!');
        }

        return $this->_authAdapter;
    }

    /**
     * Sets Auth Service
     *
     * @param \Zend\Authentication\AuthenticationService $authService
     * @return UserAuthentication
     */
    public function setAuthService(AuthenticationService $authService) {
        $this->_authService = $authService;

        return $this;
    }

    /**
     * Gets Auth Service
     *
     * @return \Zend\Authentication\AuthenticationService
     */
    public function getAuthService()
    {
        if ($this->_authService === null) {
            $this->setAuthService(new AuthenticationService());
        }

        return $this->_authService;
    }

    /**
     * @param string $password
     * @return string
     */
    public static function getHashPassword($password)
    {
        return ($password != '') ? md5($password.self::SOLT) : '';
    }

    public function authenticate($email, $password)
    {
        //$sm = $this->getServiceLocator();
        /** @var \Popov\ZfcUser\Controller\Plugin\UserAuthentication $uAuth */
        //$uAuth = $sm->get('UserAuthentication'); //@FIXME improve realisation
        $authService = $this->getAuthService();
        /** @var \Popov\Agere\Authentication\Adapter\DbTable\CredentialTreatmentAdapter $authAdapter */
        $authAdapter = $this->getAuthAdapter();

        $passwordHash = self::getHashPassword($password);
        $authAdapter->setIdentity($email);
        $authAdapter->setCredential($passwordHash);
        //$authAdapter->setWhere(['remove' => [0]]);

        /*return */$result = $authService->authenticate($authAdapter);

        if ($result->isValid()) {
            //$currentUser = $authAdapter->getResultRowObject(['email', 'roleId', 'cityId']);
            //$currentUser = $userService->getItemsCollection(['email' => $email, 'password' => $passwordHash], 0)[0];
            //$currentUser = [];
            // Table users
            /*try {
                $itemsUser = $this->getItemsCollection(['email' => $email, 'password' => $passwordHash]);
            } catch (\Exception $e) {
                \Zend\Debug\Debug::dump($e->getMessage());
                \Zend\Debug\Debug::dump($e->getTraceAsString());
                die(__METHOD__); //@todo: Реалізувати нормальну обробку помилок
            }*/


            //\Zend\Debug\Debug::dump($result->isValid()); die(__METHOD__);
            //\Zend\Debug\Debug::dump(get_class($itemsUser)); die(__METHOD__);

            /*foreach ($itemsUser as $item) {
                foreach ($item as $field => $val) {
                    //if (in_array($field, ['city', 'cityId', 'role', 'roleId', 'mnemo', 'resource' ])) { // && isset($currentUser[$field]))
                    //if (in_array($field, ['role', 'roleId', 'mnemo', 'resource' ])) { // && isset($currentUser[$field]))
                    if (in_array($field, ['mnemo', 'resource' ])) { // && isset($currentUser[$field]))
                        if (isset($currentUser[$field]) && $currentUser[$field]) {
                            $currentUser[$field] = unserialize($currentUser[$field]);
                        } else {
                            if (!isset($currentUser[$field])) {
                                $currentUser[$field] = [];
                            }
                        }
                        if (!in_array($val, $currentUser[$field])) {
                            $currentUser[$field][] = $val;
                        }
                        $currentUser[$field] = serialize($currentUser[$field]);
                    } else {
                        $currentUser[$field] = $val;
                    }
                }
            }

            unset($currentUser['password']);*/
            // END Table users

            // Table permission_access (permission brands)
            /** @var \Popov\Permission\Service\PermissionAccessService $permissionAccessService */
            /*$permissionAccessService = $sm->get('PermissionAccessService');

            $target = 'store';
            $type = 'controller';
            $roleIds = [['id' => $currentUser['id']]];*/

            /*try {
                $itemsPermissionBrand = $permissionAccessService->getItemsByRoleId($target, $type, $roleIds);
            } catch(\Exception $e) {
                \Zend\Debug\Debug::dump($e->getMessage());
                \Zend\Debug\Debug::dump($e->getTraceAsString());
                die(__METHOD__);
            }

            $brandIds = [];
            if ($itemsPermissionBrand) {
                $brandIds = array_values($itemsPermissionBrand);
                $brandIds = $brandIds[0];
            }
            $currentUser['brandId'] = serialize($brandIds);*/
            // END Table permission_access

            /*$authService->getStorage()->write($currentUser);
            $resource = unserialize($currentUser['resource']);
            if (!in_array('all', $resource)) {
                // Set expire login
                $sessionAuth = new SessionContainer('Zend_Auth');
                $sessionAuth->setExpirationSeconds(3600); // 60 minutes
                // Set user email
                // Session user email
                //$sessionUserEmail = new SessionContainer('userEmail');
                //$sessionUserEmail->userEmail = $email;
            }*/

            // Write log
            /*$params = [
                'type'    => 'action',
                'target'  => 'users/login',
                'message' => 'Вход в систему',
            ];
            $this->writeLog(__CLASS__, $params);*/

            return true;
        }
        return false;
    }


    public function unAuthenticate()
    {
        /** @var ServiceManager $serviceManager */
        //$serviceManager = $this->getServiceLocator();
        //$uAuth = $serviceManager->get('UserAuthentication'); //@FIXME improve realisation
        $this->getAuthService()->clearIdentity();
        session_unset();
    }

}
