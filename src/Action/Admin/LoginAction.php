<?php

namespace Popov\ZfcUser\Action\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Fig\Http\Message\RequestMethodInterface;
use Zend\View\Model\ViewModel;
use Zend\Session\Container as SessionContainer;
use Popov\ZfcUser\Controller\Plugin\UserAuthentication;
use Popov\ZfcUser\Form\LoginForm;
use Popov\ZfcUser\Service\UserService;
use Popov\ZfcUser\Model\User;

class LoginAction implements MiddlewareInterface, RequestMethodInterface
{
    /** @var UserService */
    protected $userService;

    /** @var LoginForm */
    protected $loginForm;

    /** @var UserAuthentication */
    protected $userAuth;

    public function __construct(UserService $userService, LoginForm $loginForm, UserAuthentication $userAuth)
    {
        $this->userService = $userService;
        $this->loginForm = $loginForm;
        $this->userAuth = $userAuth;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Session user email
        //$sessionUserEmail = new SessionContainer('userEmail');

        /** @var ServiceManager $serviceManager */
        #$serviceManager = $this->getServiceLocator();
        /** @var \Popov\ZfcUser\Controller\Plugin\UserAuthentication $uAuth */
        //$uAuth = $serviceManager->get('UserAuthentication'); //@FIXME improve realisation
        $uAuth = $this->userAuth;
        $authService = $uAuth->getAuthService();
        if ($authService->hasIdentity()) {
            return $this->redirect()->toRoute('default', ['controller' => 'index', 'action' => 'index']);
        }

        //$form = new LoginForm();
        $form = $this->loginForm;
        //$login = ($sessionUserEmail->offsetExists('userEmail')) ? $sessionUserEmail->userEmail : '';
        //$form->get('email')->setValue($login);

        //$request = $this->getRequest();
        if ($request->getMethod() == self::METHOD_POST) {
            $params = $request->getParsedBody();
            #$form->setData($request->getPost());
            $form->setData($params);
            if ($form->isValid()) {
                /** @var \Popov\ZfcUser\Service\UserService $userService */
                //$userService = $serviceManager->get($this->serviceName);
                $email = $request->getAttribute('email');
                $password = $request->getAttribute('password');
                //if (($auth = $uAuth->authenticate($email, $password)) && $auth->isValid()) {
                if ($uAuth->authenticate($email, $password)) {
                    //$om = $serviceManager->get('Doctrine\ORM\EntityManager');
                    $user = $this->userService->getRepository()->findOneBy([
                        'email' => $email,
                        'password' => UserAuthentication::getHashPassword($password)
                    ]);

                    $authService->getStorage()->write($user);
                    if ('all' === $user->getRoles()->first()->getResource()) {
                        // Set expire login
                        $sessionAuth = new SessionContainer('Zend_Auth');
                        $sessionAuth->setExpirationSeconds(3600); // 60 minutes
                    }

                    #$this->redirect()->toRoute('default', ['controller' => 'index', 'action' => 'index']);
                }
            }
        }

        $view = new ViewModel([
            'form' => $form,
        ]);
        // Disable layouts; use this view model in the MVC event instead
        //$view->setTerminal(true);
        //return $view;

        return $handler->handle($request->withAttribute(ViewModel::class, $view));
    }



}