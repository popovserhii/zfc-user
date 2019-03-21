<?php

namespace Popov\ZfcUser\Action\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// @todo wait until they will start to use Pst in codebase @see https://github.com/zendframework/zend-mvc/blob/master/src/MiddlewareListener.php#L11
//use Psr\Http\Server\MiddlewareInterface;
//use Psr\Http\Server\RequestHandlerInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;

use Fig\Http\Message\RequestMethodInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Popov\ZfcCore\Helper\UrlHelper;
use Zend\Router\RouteMatch;
use Zend\View\Model\ViewModel;
use Zend\Expressive\Flash\FlashMessageMiddleware;
use Zend\EventManager\EventManagerAwareInterface;
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
        #$flash = $request->getAttribute('flash');
        $route = $request->getAttribute(RouteMatch::class);

        //$route->getMatchedRouteName(), $route->getParams()

        /** @var User $user */
        $user = ($user = $this->userService->find($id = (int) ($route->getParams()['id'] ?? 0)))
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
                if (!$om->contains($user)) {
                    $om->persist($user);
                }
                $om->flush();

                #$this->getEventManager()->trigger($route->getParam('action') . '.post', $user, ['password' => $password]);
                #$flash->addMessage('User has been successfully saved', 'success');

                return new RedirectResponse($this->urlHelper->generate('admin/default', [
                    'controller' => 'user',
                    'action' => 'index',
                ]));
            } else {
                #$flash->addMessage('Form is invalid. Please, check the correctness of the entered data', 'error');
            }
        }

        $view = new ViewModel([
            'form' => $form,
        ]);

        return $handler->handle($request->withAttribute(ViewModel::class, $view));
    }
}