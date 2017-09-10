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
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Authentication\Adapter\Exception\RuntimeException;

class UserAuthentication extends AbstractPlugin
{
    const SALT = 'G6t8?Mj$7h#ju';

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
    public function setAuthAdapter(CredentialTreatmentAdapter $authAdapter)
    {
        $this->_authAdapter = $authAdapter;

        return $this;
    }

    /**
     * Returns Auth Adapter
     *
     * @return null|CredentialTreatmentAdapter
     * @throws \Zend\Authentication\Adapter\Exception\RuntimeException
     */
    public function getAuthAdapter()
    {
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
    public function setAuthService(AuthenticationService $authService)
    {
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
        return ($password) ? md5($password . self::SALT) : '';
    }

    public function authenticate($email, $password)
    {
        $authService = $this->getAuthService();
        $authAdapter = $this->getAuthAdapter();

        $passwordHash = self::getHashPassword($password);
        $authAdapter->setIdentity($email);
        $authAdapter->setCredential($passwordHash);
        $result = $authService->authenticate($authAdapter);

        return $result->isValid();
    }

    public function unAuthenticate()
    {
        $this->getAuthService()->clearIdentity();
        session_unset();
    }
}
