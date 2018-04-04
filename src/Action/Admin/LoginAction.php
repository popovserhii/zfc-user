<?php

namespace Popov\ZfcUser\Action\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Fig\Http\Message\RequestMethodInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Helper\UrlHelper;
use Zend\View\Model\ViewModel;
use Zend\Session\Container as SessionContainer;
use Popov\ZfcUser\Auth\Auth;
use Popov\ZfcUser\Form\LoginForm;
use Popov\ZfcUser\Service\UserService;
use Popov\ZfcUser\Model\User;
use Popov\ZfcForm\FormElementManager;

class LoginAction implements MiddlewareInterface, RequestMethodInterface
{
    use LoginTrait;

    /** @var UserService */
    protected $userService;

    /** @var LoginForm */
    protected $loginForm;

    /** @var Auth */
    protected $auth;

    /** @var UrlHelper */
    protected $urlHelper;

    public function __construct(
        UserService $userService,
        //LoginForm $loginForm,
        FormElementManager $fm,
        Auth $auth,
        UrlHelper $urlHelper
    )
    {
        $this->userService = $userService;
        $this->loginForm = $fm->get(LoginForm::class);;
        $this->auth = $auth;
        $this->urlHelper = $urlHelper;
        $this->redirect['route'] = 'admin/default';
        $this->redirect['params'] = [
            'resource' => 'admin',
            'action' => 'dashboard',
        ];
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authService = $this->auth->getAuthService();

        if ($authService->hasIdentity()) {
            return new RedirectResponse($this->urlHelper->generate($this->redirect['route'], $this->redirect['params']));
        }

        if ($this->login($request)) {
            $user = $authService->getIdentity();
            if ('all' === $user->getRoles()->first()->getResource()) {
                // Set expire login
                $sessionAuth = new SessionContainer('Zend_Auth');
                $sessionAuth->setExpirationSeconds(3600); // 60 minutes
            }
            $url = $this->urlHelper->generate($this->redirect['route'], $this->redirect['params']);

            return new RedirectResponse($url);
        }

        $view = new ViewModel([
            'layout' => 'layout::admin-login',
            'form' => $this->loginForm,
        ]);

        return $handler->handle($request->withAttribute(ViewModel::class, $view));
    }
}