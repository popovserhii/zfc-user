<?php
namespace Popov\ZfcUser\Service;

use Popov\ZfcUser\Model\User as User;

interface UserAwareInterface {
    /**
     * Set the user object
     *
     * @param User $user
     * @return $this
     */
    public function setUser(User $user);

    /**
     * Get the user object
     *
     * @return User
     */
    public function getUser();
}
