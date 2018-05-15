<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2018 Serhii Popov
 * This source file is subject to The MIT License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 *
 * @category Popov
 * @package Popov_ZfcUser
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Popov\ZfcUser\Auth;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Authentication\Adapter\Exception\RuntimeException;

class Auth
{
    const SALT = 'G6t8?Mj$7h#ju';

    /**
     * @var CredentialTreatmentAdapter
     */
    protected $authAdapter = null;

    /**
     * @var AuthenticationService
     */
    protected $authService = null;

    /**
     * Auth constructor.
     *
     * @param CredentialTreatmentAdapter|null $authAdapter
     * @param AuthenticationService|null $authService
     * @todo Allow use any adapter such as Doctrine or Zf\Db @see https://samsonasik.wordpress.com/2014/06/21/zend-framework-2-using-custom-authentication-condition-with-doctrinemodule/
     */
    public function __construct(CredentialTreatmentAdapter $authAdapter = null, AuthenticationService $authService = null)
    {
        $this->authAdapter = $authAdapter;
        $this->authService = $authService;
    }

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
     * {@inheritdoc}
     */
    public function getStorage()
    {
        return $this->getAuthService()->getStorage();
    }

    /**
     * Sets Auth Adapter
     *
     * @param CredentialTreatmentAdapter $authAdapter
     * @return Auth
     */
    public function setAuthAdapter(CredentialTreatmentAdapter $authAdapter)
    {
        $this->authAdapter = $authAdapter;

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
        if ($this->authAdapter === null) {
            throw new RuntimeException(__CLASS__ . '::_authAdapter should be set via setAuthAdapter() method!');
        }

        return $this->authAdapter;
    }

    /**
     * Sets Auth Service
     *
     * @param \Zend\Authentication\AuthenticationService $authService
     * @return Auth
     */
    public function setAuthService(AuthenticationService $authService)
    {
        $this->authService = $authService;

        return $this;
    }

    /**
     * Gets Auth Service
     *
     * @return \Zend\Authentication\AuthenticationService
     */
    public function getAuthService()
    {
        if ($this->authService === null) {
            $this->setAuthService(new AuthenticationService());
        }

        return $this->authService;
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
