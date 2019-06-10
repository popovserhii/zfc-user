<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2018 Serhii Popov
 * This source file is subject to The MIT License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 *
 * @category Popov
 * @package Popov_ZfcUser
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Popov\ZfcUser\Action\Admin;

use Popov\ZfcUser\Auth\Auth;

trait LogoutTrait
{
    /** @var Auth */
    protected $auth;

    protected $redirect = [
        'route' => 'default',
        'params' => [
            'controller' => 'index',
            'action' => 'index',
        ],
    ];

    public function logout()
    {
        $authService = $this->auth->getAuthService();
        if ($authService->hasIdentity()) {
            $this->auth->unAuthenticate();
        }
    }
}