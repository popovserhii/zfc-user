<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2018 Serhii Stagem
 * This source file is subject to The MIT License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 *
 * @category Popov
 * @package Popov_ZfcUser
 * @author Serhii Stagem <popow.serhii@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Popov\ZfcUser\Action\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#use Psr\Http\Server\RequestHandlerInterface;
#use Psr\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Stagem\ZfcAction\MiddlewareInterface;
use Zend\View\Model\ViewModel;

class DashboardAction implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $viewModel = new ViewModel(['message' => '<h1>Hi there!</h1>']);

        return $handler->handle($request->withAttribute(ViewModel::class, $viewModel));
    }
}

