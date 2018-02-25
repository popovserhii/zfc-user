<?php

namespace Popov\ZfcUser\Action\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Fig\Http\Message\RequestMethodInterface;
use Zend\Expressive\Helper\UrlHelper;
use Popov\ZfcUser\Auth\Auth;

class LogoutAction implements MiddlewareInterface, RequestMethodInterface
{
    use LogoutTrait;

    /** @var Auth */
    protected $auth;

    /** @var UrlHelper */
    protected $urlHelper;

    public function __construct(
        Auth $auth,
        UrlHelper $urlHelper
    ) {
        $this->auth = $auth;
        $this->urlHelper = $urlHelper;
        $this->redirect['route'] = 'admin/default';
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->logout();
    }
}