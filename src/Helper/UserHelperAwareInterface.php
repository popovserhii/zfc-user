<?php

namespace Popov\ZfcUser\Helper;

interface UserHelperAwareInterface
{
    /**
     * @param UserHelper $userPlugin
     */
    public function setUserHelper(UserHelper $userPlugin);

    /**
     * @return UserHelper
     */
    public function getUserHelper();
}