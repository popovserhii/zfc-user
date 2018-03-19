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
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Helper\UrlHelper;

trait LogoutTrait
{
    /** @var Auth */
    protected $auth;

    /** @var RequestHelper */
    protected $urlHelper;

    protected $redirect = [
        'route' => 'default',
        'params' => [
            'resource' => 'index',
            'action' => 'index',
        ],
    ];

    public function logout()
    {
        $authService = $this->auth->getAuthService();
        if ($authService->hasIdentity()) {
            $this->auth->unAuthenticate();
        }

        return new RedirectResponse($this->urlHelper->generate($this->redirect['route'], $this->redirect['params']));
    }
}