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

use Psr\Http\Message\ServerRequestInterface;
use Popov\ZfcUser\Auth\Auth;
use Popov\ZfcUser\Model\User;
use Popov\ZfcUser\Form\LoginForm;
use Popov\ZfcUser\Service\UserService;

trait LoginTrait
{
    /** @var UserService */
    protected $userService;

    /** @var LoginForm */
    protected $loginForm;

    /** @var Auth */
    protected $auth;

    protected $redirect = [
        'route' => 'default',
        'params' => [
            'resource' => 'index',
            'action' => 'index',
        ],
    ];

    public function login(ServerRequestInterface $request)
    {
        if ($request->getMethod() == 'POST') {
            $params = $request->getParsedBody();
            $this->loginForm->setData($params);
            if ($this->loginForm->isValid()) {
                if ($this->auth->authenticate($params['email'], $params['password'])) {
                    /** @var User $user */
                    $user = $this->userService->getRepository()->findOneBy([
                        'email' => $params['email'],
                        'password' => Auth::getHashPassword($params['password'])
                    ]);

                    $this->auth->getAuthService()
                        ->getStorage()
                        ->write($user);

                    return true;
                }
            }
        }
        return false;
    }
}