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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// @todo wait until they will start to use Pst in codebase @see https://github.com/zendframework/zend-mvc/blob/master/src/MiddlewareListener.php#L11
//use Psr\Http\Server\MiddlewareInterface;
//use Psr\Http\Server\RequestHandlerInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Fig\Http\Message\RequestMethodInterface;

use Zend\Router\RouteMatch;
use Zend\View\Model\ViewModel;
use Popov\ZfcCore\Helper\UrlHelper;
use Popov\ZfcCurrent\CurrentHelper;
use Popov\ZfcUser\Block\Grid\UserGrid;
use Popov\ZfcUser\Service\UserService;

class IndexAction implements MiddlewareInterface, RequestMethodInterface
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var UserGrid
     */
    protected $userGrid;

    /**
     * @var CurrentHelper
     */
    protected $currentHelper;

    /**
     * @var UrlHelper
     */
    protected $urlHelper;

    public function __construct(
        UserService $userService,
        UserGrid $userGrid,
        CurrentHelper $currentHelper,
        UrlHelper $urlHelper
    )
    {
        $this->userService = $userService;
        $this->userGrid = $userGrid;
        $this->currentHelper = $currentHelper;
        $this->urlHelper = $urlHelper;
        //$this->config = $config;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $request->getAttribute(RouteMatch::class);

        $userQb = $this->userService->getUsers();

        $this->userGrid->init();
        $productDataGrid = $this->userGrid->getDataGrid();
        $productDataGrid->setUrl($this->urlHelper->generate($route->getMatchedRouteName(), $route->getParams()));
        //$dataGrid->setUrl($this->url()->fromRoute($route->getMatchedRouteName(), $route->getParams()));
        $productDataGrid->setDataSource($userQb);
        $productDataGrid->render();
        $response = $productDataGrid->getResponse();

        return $handler->handle($request->withAttribute(ViewModel::class, $response));
    }
}

