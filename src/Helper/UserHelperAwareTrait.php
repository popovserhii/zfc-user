<?php

namespace Popov\ZfcUser\Helper;

trait UserHelperAwareTrait
{
    /**
     * @var UserHelper
     */
    protected $userHelper;

    /**
     * Set the user helper
     *
     * @param UserHelper $userHelper
     */
    public function setUserHelper(UserHelper $userHelper)
    {
        $this->userHelper = $userHelper;
    }

    /**
     * Get the user plugin
     *
     * @return UserHelper
     */
    public function getUserHelper()
    {
        return $this->userHelper;
    }
}