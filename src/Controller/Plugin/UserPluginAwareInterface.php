<?php

namespace Popov\ZfcUser\Controller\Plugin;

interface UserPluginAwareInterface
{
    /**
     * @param User $userPlugin
     */
    public function setUserPlugin(User $userPlugin);

    /**
     * @param $userPlugin
     * @return User
     */
    public function getUserPlugin();
}