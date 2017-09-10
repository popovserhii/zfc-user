<?php
/**
 * User Service
 *
 * @category Agere
 * @package Agere_User
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 05.10.2016 10:04
 */
namespace Popov\ZfcUser\Service;

use Popov\ZfcCore\Service\DomainServiceAbstract;
use Popov\ZfcUser\Model\User as User;

class UserService extends DomainServiceAbstract
{
    protected $entity = User::class;

    /** @var User */
    protected $current;

    /**
     * @param User $current
     * @return $this
     */
    public function setCurrent(User $current)
    {
        $this->current = $current;

        return $this;
    }

    /**
     * @return User
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /*public function setAuthAdapter($authAdapter)
    {
        $this->authAdapter = $authAdapter;
    }

    public function getAuthAdapter()
    {
        return $this->authAdapter;
    }

    */
}