<?php
/**
 * User Service
 *
 * @category Agere
 * @package Agere_User
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 05.10.2016 10:04
 */
namespace Popov\ZfcUser\Service;

use Popov\ZfcCore\Service\DomainServiceAbstract;
use Popov\ZfcUser\Model\User as User;

class UserService extends DomainServiceAbstract
{
    const SALT = 'G6t8?Mj$7h#ju';

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

    /**
     * @param string $password
     * @return string
     */
    public static function getHashPassword($password)
    {
        return ($password) ? md5($password . self::SALT) : static::generatePassword();
    }

    public static function generatePassword()
    {
        $password = '';

        $len = 6;
        $str = 'qwertyuiopasdfghjklzxcvbnm1234567890QWERTYUIOPASDFGHJKLZXCVBNM';
        $strLen = strlen($str) - 1;

        for ($i = 0; $i < $len; ++ $i) {
            $j = rand(0, $strLen);
            $password .= $str[$j];
        }

        return $password;
    }
}