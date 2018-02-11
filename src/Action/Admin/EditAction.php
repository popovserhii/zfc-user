<?php

namespace Popov\ZfcUser\Action\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Fig\Http\Message\RequestMethodInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Router\RouteResult;
use Zend\Expressive\Helper\UrlHelper;
use Zend\View\Model\ViewModel;
use Zend\Expressive\Flash\FlashMessageMiddleware;
use Popov\ZfcForm\FormElementManager;
use Popov\ZfcUser\Form\UserForm;
use Popov\ZfcUser\Service\UserService;
use Popov\ZfcUser\Model\User;

class EditAction implements MiddlewareInterface, RequestMethodInterface
{
    /** @var UserService */
    protected $userService;

    /** @var FormElementManager */
    protected $formManager;

    /** @var UrlHelper */
    protected $urlHelper;

    public function __construct(UserService $userService, FormElementManager $formManager, UrlHelper $urlHelper)
    {
        $this->userService = $userService;
        $this->formManager = $formManager;
        $this->urlHelper = $urlHelper;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $flashMessages = $request->getAttribute(FlashMessageMiddleware::FLASH_ATTRIBUTE);
        $route = $request->getAttribute(RouteResult::class);

        /** @var User $user */
        $user = ($user = $this->userService->find($id = (int) $route->getMatchedParams()['id']))
            ? $user
            : $this->userService->getObjectModel();

        /** @var UserForm $form */
        $form = $this->formManager->get(UserForm::class);
        $form->bind($user);

        if ($request->getMethod() == self::METHOD_POST) {
            $params = $request->getParsedBody();
            $form->setData($params);
            if ('' === ($password = $form->get('user')->get('password')->getValue())) {
                $form->getInputFilter()->get('user')->remove('password');
            }

            if ($form->isValid()) {
                if ($password) { // password is send by POST
                    $user->setPassword(UserService::getHashPassword($password));
                }

                $om = $this->userService->getObjectManager();
                $om->persist($user);
                $om->flush();

                #$this->getEventManager()->trigger($route->getParam('action') . '.post', $user, ['password' => $password]);

                $msg = 'User has been successfully saved';
                $flashMessages->flash('success', $msg);

                return new RedirectResponse($this->urlHelper->generate('admin/default', [
                    'controller' => 'index', //@TODO implement UserGrid
                ]));
            } else {
                $msg = 'Form is invalid. Please, check the correctness of the entered data';
                $flashMessages->flash('error', $msg);
            }
        }

        $view = new ViewModel([
            'form' => $form,
        ]);

        return $handler->handle($request->withAttribute(ViewModel::class, $view));
    }
}