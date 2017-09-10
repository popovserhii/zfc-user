<?php

namespace Popov\ZfcUser\Controller\Plugin;

trait ProvideUserPlugin
{
    /**
     * @var User
     */
    protected $userPlugin;

    /**
     * Set the user plugin
     *
     * @param User $userPlugin
     */
    public function setUserPlugin(User $userPlugin)
    {
        $this->userPlugin = $userPlugin;
    }

    /**
     * Get the user plugin
     *
     * @return User
     */
    public function getUserPlugin()
    {
        return $this->userPlugin;
    }
}