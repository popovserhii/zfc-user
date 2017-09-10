<?php

namespace Popov\ZfcUser\Controller\Plugin;

interface UserPluginAwareInterface
{
    /**
     * @param UserPlugin $userPlugin
     */
    public function setUserPlugin(UserPlugin $userPlugin);

    /**
     * @param $userPlugin
     * @return UserPlugin
     */
    public function getUserPlugin();
}