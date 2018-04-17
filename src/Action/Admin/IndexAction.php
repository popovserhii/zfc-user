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

use Popov\ZfcUser\Block\Grid\UserGrid;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Fig\Http\Message\RequestMethodInterface;
use Zend\View\Model\ViewModel;
use Popov\ZfcCurrent\CurrentHelper;
use Popov\ZfcUser\Block\Grid\RoleGrid;
use Popov\ZfcUser\Service\UserService;

class IndexAction implements MiddlewareInterface, RequestMethodInterface
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var CurrentHelper
     */
    protected $currentHelper;

    protected $userGrid;

    protected $config;

    public function __construct(UserService $userService, UserGrid $userGrid, CurrentHelper $currentHelper)
    {
        $this->userService = $userService;
        $this->userGrid = $userGrid;
        $this->currentHelper = $currentHelper;
        //$this->config = $config;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $questions = $this->userService->getUsers();

        $this->userGrid->init();
        $productDataGrid = $this->userGrid->getDataGrid();
        //$productDataGrid->setUrl($this->url()->fromRoute($route->getMatchedRouteName(), $url));
        $productDataGrid->setDataSource($questions);
        $productDataGrid->render();
        $response = $productDataGrid->getResponse();

        return $handler->handle($request->withAttribute(ViewModel::class, $response));
    }
}

