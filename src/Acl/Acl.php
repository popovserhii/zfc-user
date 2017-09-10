<?php
/**
 * File for Acl Class
 *
 * @category  User
 * @package   User_Acl
 * @author    Marco Neumann <webcoder_at_binware_dot_org>
 * @copyright Copyright (c) 2011, Marco Neumann
 * @license   http://binware.org/license/home/type:new-bsd New BSD License
 */
/**
 * @namespace
 */
namespace Popov\ZfcUser\Acl;

/**
 * @uses Zend\Acl\Acl
 * @uses Zend\Acl\Role\GenericRole
 * @uses Zend\Acl\Resource\GenericResource
 */
use Zend\Permissions\Acl\Acl as ZendAcl,
    Zend\Permissions\Acl\Role\GenericRole as Role,
    Zend\Permissions\Acl\Resource\GenericResource as Resource,
    Zend\Permissions\Acl\Role\RoleInterface,
    Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * Class to handle Acl
 * This class is for loading ACL defined in a config
 *
 * @category User
 * @package  User_Acl
 * @copyright Copyright (c) 2011, Marco Neumann
 * @license   http://binware.org/license/home/type:new-bsd New BSD License
 */
class Acl extends ZendAcl
{
    /**
     * Default Role
     */
    const DEFAULT_ROLE = 'guest';

    const ACCESS_READ = 4;

    const ACCESS_WRITE = 2;

    const ACCESS_TOTAL = Acl::ACCESS_READ + Acl::ACCESS_WRITE;

    #const ACCESS_TOTAL = 6;
    protected static $_access = [
        'read' => self::ACCESS_READ,
        'write' => self::ACCESS_WRITE,
        //'total'	=> self::ACCESS_TOTAL,
    ];

    public static function getAccess($key = '')
    {
        return ($key != '' && array_key_exists($key, self::$_access)) ? self::$_access[$key] : self::$_access;
    }

    public static function getAccessTotal()
    {
        return array_sum(self::$_access);
    }

    public static function getAccessForm()
    {
        return [
            'write' => self::$_access['write'],
            'all' => self::getAccessTotal(),
        ];
    }

    /**
     * Returns true if and only if the Role has access to the Resource
     * The $role and $resource parameters may be references to, or the string identifiers for,
     * an existing Resource and Role combination.
     * If either $role or $resource is null, then the query applies to all Roles or all Resources,
     * respectively. Both may be null to query whether the ACL has a "blacklist" rule
     * (allow everything to all). By default, Zend\Permissions\Acl creates a "whitelist" rule (deny
     * everything to all), and this method would return false unless this default has
     * been overridden (i.e., by executing $acl->allow()).
     * If a $privilege is not provided, then this method returns false if and only if the
     * Role is denied access to at least one privilege upon the Resource. In other words, this
     * method returns true if and only if the Role is allowed all privileges on the Resource.
     * This method checks Role inheritance using a depth-first traversal of the Role registry.
     * The highest priority parent (i.e., the parent most recently added) is checked first,
     * and its respective parents are checked similarly before the lower-priority parents of
     * the Role are checked.
     *
     * @param  RoleInterface|string|array $role
     * @param  ResourceInterface|string $resource
     * @param  string $privilege
     * @return bool
     */
    public function isAllowed($role = null, $resource = null, $privilege = null)
    {
        if (is_array($role)) {
            $isAllowed = false;
            foreach ($role as $val) {
                if ($isAllowed = parent::isAllowed($val, $resource, $privilege)) {
                    break;
                }
            }

            return $isAllowed;
        }

        return parent::isAllowed($role, $resource, $privilege);
    }

    public function hasAccess($user, $resource)
    {
        $has = $this->hasAccessByRoles($user->getRoles(), $resource);

        return $has;
    }

    public function hasAccessByRoles($roles, $resource)
    {
        $resource = ltrim($resource, '/');
        foreach ($roles as $role) {
            $mnemo = is_object($role) ? $role->getMnemo() : $role;
            //if ($this->hasResource('all')) {
                $allowed = ['all' => $this->isAllowed($mnemo, 'all', Acl::getAccessTotal())];
            //}
            if ($this->hasResource($resource)) {
                $allowed['total'] = $this->isAllowed($mnemo, $resource, Acl::getAccessTotal());
                $allowed['write'] = $this->isAllowed($mnemo, $resource, Acl::getAccess()['write']);
                $allowed['read'] = $this->isAllowed($mnemo, $resource, Acl::getAccess()['read']);
            }
            if (/*isset($allowed) &&*/ in_array(true, $allowed)) {
                return true;
            }
        }

        return false;
    }
}
