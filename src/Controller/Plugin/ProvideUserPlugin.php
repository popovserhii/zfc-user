<?php

namespace Popov\ZfcUser\Controller\Plugin;

trait ProvideUserPlugin
{
    /**
     * @var UserPlugin
     */
    protected $userPlugin;

    /**
     * Set the user plugin

     *
*@param UserPlugin $userPlugin
     */
    public function setUserPlugin(UserPlugin $userPlugin)
    {
        $this->userPlugin = $userPlugin;
    }

    /**
     * Get the user plugin

     *
*@return UserPlugin
     */
    public function getUserPlugin()
    {
        return $this->userPlugin;
    }
}