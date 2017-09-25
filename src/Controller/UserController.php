<?php
namespace Popov\ZfcUser\Controller;

use Popov\ZfcPermission\Model\Permission;
use Popov\ZfcPermission\Model\PermissionAccess;
use Popov\ZfcUser\Acl\Acl;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Session\Container as SessionContainer;
use Popov\Agere\File\Transfer\Adapter\Http;
use Popov\Agere\File\Resize\Adapter\GbResize;
use Popov\Agere\String\StringUtils as AgereString;
//use Popov\ZfcUser\Form\User as UserForm;
use Popov\ZfcUser\Form\Login as LoginForm;
use Popov\ZfcUser\Form\UserForm;
use Popov\ZfcUser\Form\ForgotPassword as ForgotPasswordForm;
use Popov\ZfcUser\Form\ChangePassword as ChangePasswordForm;
use Popov\ZfcUser\Model\User as User;
use Popov\ZfcRole\Model\Role;
//use Popov\City\Model\City;
use Popov\ZfcUser\Controller\Plugin\UserAuthentication;
use Popov\ZfcUser\Service\UserService;
use Popov\ZfcEntity\Controller\Plugin\EntityPlugin;

/**
 * @method EntityPlugin entity($context = null)
 */
class UserController extends AbstractActionController {

    public $serviceName = 'UserService';
    public $sessionName = 'userIndex';
    public $controllerRedirect = 'user';

    // For search
    public $sessionNameFilters = 'userFilters';
    public $limit = 36;

    protected $formElementManager;

    public function getFormElementManager()
    {
        if (!$this->formElementManager) {
            $this->formElementManager = $this->serviceLocator->get('FormElementManager');

        }
        return $this->formElementManager;
    }

    public function indexAction($action = 'index', $sessionNameFilters = '')
    {
        $sessionNameFilters = ($sessionNameFilters != '') ? $sessionNameFilters : $this->sessionNameFilters;
        $session = new SessionContainer($sessionNameFilters);
        $request = $this->getRequest();
        $route = $this->getEvent()->getRouteMatch();
        $currentPage = $route->getParam('page', 0);
        $locator = $this->getServiceLocator();
        /** @var \Popov\ZfcUser\Service\UsersService $service */
        $service = $locator->get($this->serviceName);

        // Filters
        $filtersSelected = [
            'roleId' => 0,
        ];

        $groupedСity = false;
        $groupedRole = false;
        $search = '';
        // END Filters

        $totalItems = null;
        $orderBy = [];

        if ($request->isPost())
        {
            $currentPage = 0;
            $post = $request->getPost()->toArray();

            // Clear session
            $session->getManager()->getStorage()->clear($sessionNameFilters);


            if (! isset($post['reset_filters']))
            {
                // Set parameters

                // Filters selected
                $filtersSelected = $service->filters($filtersSelected, $post);
                $orderBy = [];

                // Field search
                if (isset($post['groupedСity']))
                {
                    $groupedСity = true;
                    $orderBy = ['city' => 'ASC', 'email' => 'ASC'];
                }
                
                // Field search
                if (isset($post['groupedRole']))
                {
                    $groupedRole = true;
                    $orderBy['roleId'] = 'ASC';
                }

                $search = $post['search'];

                // END Set parameters


                // Set session
                $session->filtersSelected = $filtersSelected;
                $session->search = $search;
                $session->groupedСity = $groupedСity;
                $session->orderBy = $orderBy;
            }
        }
        else if ($session->offsetExists('search')) // Session
        {
            // Set parameters
            $sessionStoreFilters = $session->getManager()->getStorage()->offsetGet($sessionNameFilters);

            $search = $sessionStoreFilters['search'];
            $filtersSelected = $sessionStoreFilters['filtersSelected'];
            $groupedСity = $sessionStoreFilters['groupedСity'];
            $orderBy = $sessionStoreFilters['orderBy'];
            // END Set parameters
        }

        // Table users
        $whereStr = '';

        $where['u.remove'] = 0;

        if ($filtersSelected['roleId'])
        {
            $where['ur.roleId'] = $filtersSelected['roleId'];
        }

        if ($search)
        {
            $fieldsSearch = ['u.email', 'u.lastName', 'c.city'];
            $argsSearch = [];

            foreach ($fieldsSearch as $field)
            {
                $argsSearch[] = "{$field} LIKE '%{$search}%'";
            }

            if ($argsSearch)
            {
                $whereStr = ' AND ('.implode(' OR ', $argsSearch).')';
            }
        }

        $users = $service->getItemsCollection($where, $currentPage, true, $totalItems, $orderBy, [], $whereStr);

        $target = 'store';
        $type = 'controller';

        // Table permission_access
        /** @var \Popov\ZfcPermission\Service\PermissionAccessService $servicePermissionAccess */
        $servicePermissionAccess = $locator->get('PermissionAccessService');
        $permissionBrands = ($action == 'index' && $users) ? $servicePermissionAccess->getItemsByRoleId($target, $type, $users) : [];

        $data = [
            'fields'            => $service->getFields(),
            'users'                => $users,
            'permissionBrands'    => $permissionBrands,
            'filtersSelected'    => ([
                'roleId'        => $filtersSelected['roleId'],
                'search'        => $search,
                'groupedСity'    => $groupedСity,
                'groupedRole'    => $groupedRole,
            ]),
            'paginator'            => $service->getPager()->getStrategy($totalItems),
            'partialTemplate'    => 'children-index',
        ];

        $this->layout('layout/home');

        if ($action != 'index')
        {
            return $data;
        }

        return new ViewModel($data);
    }

    public function addAction()
    {
        //$this->layout('layout/home');

        /*$viewModel = new ViewModel();
        $viewModel->setVariables($this->editAction());
        return $viewModel->setTemplate('popov/user/edit.phtml');*/
        return $this->editAction()->setTemplate('popov/user/edit.phtml');
    }

    public function editAction()
    {
        $sm = $this->serviceLocator;

        $request = $this->getRequest();
        $route = $this->getEvent()->getRouteMatch();
        /** @var UserService $service */
        $service = $sm->get($this->serviceName);
        $fm = $this->getFormElementManager();

        /** @var User $user */
        $user = ($user = $service->find($id = (int) $route->getParam('id')))
            ? $user
            : $service->getObjectModel();

        /** @var UserForm $form */
        $form = $fm->get(UserForm::class);
        $form->bind($user);

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ('' === ($password = $form->get('user')->get('password')->getValue())) {
                //$form->getInputFilter()->remove('password');
                $form->getInputFilter()->get('user')->remove('password');
            }

            if ($form->isValid()) {
                if ($password) { // password is send by POST
                    $user->setPassword(UserService::getHashPassword($password));
                }

                $om = $service->getObjectManager();
                $om->persist($user);
                $om->flush();

                $this->getEventManager()->trigger($route->getParam('action') . '.post', $user, ['password' => $password]);

                $msg = 'User has been successfully saved';
                $this->flashMessenger()->addSuccessMessage($msg);

                $this->redirect()->toRoute('default', [
                    //'controller' => $route->getParam('controller'),
                    'controller' => 'index', //@TODO implement UserGrid
                ]);
            } else {
                $msg = 'Form is invalid. Please, check the correctness of the entered data';
                $this->flashMessenger()->addErrorMessage($msg);
            }
        }

        return (new ViewModel([
            'form' => $form,
        ]));
    }

    public function editOldAction()
    {
        $request = $this->getRequest();
        $route = $this->getEvent()->getRouteMatch();
        $sm = $this->getServiceLocator();
        $om = $sm->get('Doctrine\ORM\EntityManager');

        $id = $route->getParam('id');

        // Items user
        $target = 'store';
        $type = 'controller';
        // Used roleId with possible user, role, group
        //$maskId = AgereString::getStringAssocDigit($id, 'user');

        /** @var UserService $service */
        $service = $sm->get($this->serviceName);
        //$user = $service->getItem($id, 'id', '0');
        $user = $service->find($id);

        /** @var \Popov\ZfcUser\Service\UsersCityService $serviceUsersCity */
        //$serviceUsersCity = $sm->get('UsersCityService');

        /** @var \Popov\ZfcUser\Service\UserRoleService $serviceUsersRoles */
        //$serviceUsersRoles = $sm->get('UsersRolesService');

        /** @var \Popov\ZfcPermission\Service\PermissionAccessService $servicePermissionAccess */
        $servicePermissionAccess = $sm->get('PermissionAccessService');

        $fields = [/*'departmentId', */
            'id',
            'supplierId',
            'email',
            'password',
            'firstName',
            'lastName',
            'patronymic',
            'phone',
            'phoneWork',
            'phoneInternal',
            'post',
            'dateBirth',
            'dateEmployment',
            'cityId[]',
            'roleId[]',
            'showIndex',
            'notation',
            'sendEmails',
        ];

        $form = new UserForm($id, $fields, $this->dbAdapter);

        foreach ($fields as $field) {
            $method = 'get' . ucfirst($field);
            if ($field == 'cityId[]') {
                //$value = $serviceUsersCity->getCityIdItemsArray($id, 'userId');
                $value = [];
                foreach ($user->getCities() as $city) {
                    $value[] = $city->getId();
                }
            } else {
                if ($field == 'roleId[]') {
                    //$value = $serviceUsersRoles->getRoleIdItemsArray($id, 'userId');
                    $value = [];
                    foreach ($user->getRoles() as $role) {
                        $value[] = $role->getId();
                    }
                } else {
                    if (stripos($field, 'date') !== false) {
                        $value = is_object($user->{$method}()) ? $user->{$method}()->format('Y-m-d') : '';
                    } else {
                        $value = $user->{$method}();
                    }
                }
            }
            $form->get($field)->setValue($value);
        }

        if ($request->isPost()) {
            $values = $request->getPost()->toArray();
            $values['cityId'] = array_filter($values['cityId']);
            $values['roleId'] = array_filter($values['roleId']);
            unset($values['save']);
            if ($values['password'] == '') {
                $values['password'] = $user->getPassword();
                $service->setIsMd5(true);
            }
            $issetEmail = $this->getEM()->getRepository(User::class)
                ->findOneBy(['email' => $values['email']]);
            if (!empty($values['id'])) {
                $issetUser = $this->getEM()->find(User::class, $values['id']);
                if ($issetEmail && $issetUser) {
                    if ($issetUser->getEmail() != $issetEmail->getEmail() && $issetEmail->getEmail()) {
                        $values['email'] = null;
                    }
                }
            } elseif ($issetEmail) {
                $values['email'] = null;
            }
            unset($values['id']);

            $form->setData($values);
            if ($form->isValid()) {
                $post = $form->getData();
                $saveData = [];
                $saveDataCities = [];
                $saveDataRoles = [];
                foreach ($fields as $field) {
                    if (stripos($field, 'date') !== false) {
                        $saveData[$field] = $post[$field] ? \DateTime::createFromFormat('Y-m-d', $post[$field]) : null;
                    } else if ($field == 'cityId[]') {
                        $saveDataCities['cityId'] = $post['cityId'];
                    } else if ($field == 'roleId[]') {
                        $saveDataRoles['roleId'] = $post['roleId'];
                    } else if ($field == 'id') {
                        // $saveDataRoles['roleId'] = $post['roleId'];
                    } else {
                        $saveData[$field] = $post[$field];
                    }
                }
                if ($saveData) {
                    $oneItem = $service->save($saveData, $user);
                }

                // Save permission access
                $data['permission'] = [
                    'target' => $target,
                    'entityId' => 0,
                    'type' => $type,
                ];
                /** @var Permission[] $permissions */
                $permissions = $om->getRepository(Permission::class)->findBy($data['permission']);
                $permissionIds = [];
                foreach ($permissions as $permission) {
                    $permissionIds[$permission->getParent()] = $permission->getId();
                }

                // Used roleId with possible user, role, group
                $maskId = ($id > 0)
                    ? AgereString::getStringAssocDigit($id, 'user')
                    : AgereString::getStringAssocDigit($oneItem->getId(), 'user');
                /*foreach ($permissionIds as $pParent => $pId) {
                    if (!empty($values['permissionAccess'][$pParent])) {
                        $permissionAccess = $om->getRepository('Popov\Permission\Model\PermissionAccess')->findOneBy([
                            'permissionId' => $pId,
                            'roleId' => $maskId,
                            'access' => 4,
                        ]);
                        if (empty($permissionAccess)) {
                            $permission = $om->find(Permission::class, $pId);
                            $permissionAccess = new PermissionAccess();
                            $permissionAccess->setPermissionId($pId);
                            $permissionAccess->setRoleId($maskId);
                            $permissionAccess->setAccess(4);
                            $permissionAccess->setPermission($permission);
                            $om->persist($permissionAccess);
                            $om->flush();
                        }
                    } else {
                        $permissionAccess = $om->getRepository('Popov\Permission\Model\PermissionAccess')->findOneBy([
                            'permissionId' => $pId,
                            'roleId' => $maskId,
                            'access' => 4,
                        ]);
                        if (!empty($permissionAccess)) {
                            $om->remove($permissionAccess);
                            $om->flush();
                        }
                    }
                }*/

                foreach ($permissionIds as $pParent => $pId) {
                    $permissionAccess = $om->getRepository(PermissionAccess::class)->findOneBy([
                        'permissionId' => $pId,
                        'roleId' => $maskId,
                        'access' => Acl::ACCESS_READ,
                    ]);
                    if (isset($values['permissionAccess'][$pParent])) {
                        if (empty($permissionAccess)) {
                            $permission = $om->find(Permission::class, $pId);
                            $permissionAccess = new PermissionAccess();
                            $permissionAccess->setPermissionId($pId);
                            $permissionAccess->setRoleId($maskId);
                            $permissionAccess->setAccess(Acl::ACCESS_READ);
                            $permissionAccess->setPermission($permission);
                            $om->persist($permissionAccess);
                            //$om->flush();
                        }
                    } elseif (isset($permissionAccess)) {
                        $om->remove($permissionAccess);
                        //$om->flush();
                    }
                }

                /*
                foreach ($values['permissionAccessId'] as $key => $val)
                {
                    if (isset($values['permissionAccess'][$key]) && ! (bool) $val)
                    {
                        $data['permission']['parent'] = $key;
                        $data['roleId'] = $roleId;
                        $data['access'] = 4;

                        $servicePermissionAccess->insert($data);
                    }
                    else if (! isset($values['permissionAccess'][$key]) && $val)
                    {
                        $itemPermissionAccess = $servicePermissionAccess->getItem($values['permissionAccessId'][$key]);

                        if ($itemPermissionAccess)
                        {
                            $permissionId = $itemPermissionAccess->getPermissionId();
                            $servicePermissionAccess->delete($itemPermissionAccess);
                        }
                    }
                }
                */
                // END Save permission access

                // var_dump($saveData); die;
                if ($saveData) {
                    // Save users roles
                    if ($saveDataRoles && $oneItem->getId()) {
                        /** @var \Popov\Roles\Service\RolesService $roleService */
                        $oneItem->getRoles()->clear();
                        $roles = $om->getRepository(Role::class)->findById($saveDataRoles['roleId']);
                        $oneItem->setRoles($roles);
                    }
                    // Save users city
                    if ($saveDataCities && $oneItem->getId()) {
                        /** @var \Popov\City\Service\CityService $cityService */
                        $oneItem->getCities()->clear();
                        $cities =$om->getRepository(City::class)->findById($saveDataCities['cityId']);
                        $oneItem->setCities($cities);
                    }
                    $om->persist($oneItem);
                    $om->flush();
                    // Write log
                    $params = [
                        'type' => 'action',
                        'target' => "{$this->controllerRedirect}/edit/{$oneItem->getId()}",
                        'itemId' => $oneItem->getId(),
                        'message' => "Редактирование <br>
                                        {$service->getMessageLog($fields, $oneItem, $sm)}",
                    ];
                    $service->writeLog(__CLASS__, $params);
                }
                // Redirect from session container
                $session = new SessionContainer($this->sessionName);
                $redirect = $session->offsetExists('index') ? $session->index : [
                    'controller' => $this->controllerRedirect,
                    'action' => 'index',
                ];
                //$this->redirect()->toRoute('default/id/wildcard', $redirect);
                $this->redirect()->toRoute('default', $redirect);
            }
        }
        // Table brand
        $serviceBrand = $sm->get('BrandService');
        $this->layout('layout/home');

        return [
            'id' => $id,
            'fields' => $service->getFields(),
            'form' => $form,
            'brands' => $serviceBrand->getItemsCollection(),
            'itemsUser' => $servicePermissionAccess->getItemsUserArray($target, $type, $maskId),
            'partialTab' => 'basic-data',
        ];
    }

    public function historyAction()
    {
        /** @var \Zend\Mvc\Router\RouteMatch $route */
        $route = $this->getEvent()->getRouteMatch();
        $locator = $this->getServiceLocator();
        /** @var \Popov\ZfcUser\Service\UsersService $service */
        $service = $locator->get($this->serviceName);

        $id = (int) $route->getParam('id');

        $logs = [];

        if ($id)
        {
            // Table logs
            /** @var \Popov\Logs\Service\LogsService $logsService */
            $logsService = $locator->get('LogsService');

            $conditionLogs = [
                'itemId'    => $id,
                'module'    => 'Popov\Users',
                'type'        => 'action',
            ];

            $logs = $logsService->getItemsCollection($conditionLogs, 'date');
        }

        $this->layout('layout/home');

        $view = new ViewModel ([
            'id'            => $id,
            'logs'            => $logs,
            'partialTab'    => 'history',
        ]);

        $view->setTemplate("magere/users/edit.phtml");

        return $view;
    }

    public function loginAction()
	{
        // Session user email
        //$sessionUserEmail = new SessionContainer('userEmail');

		/** @var ServiceManager $serviceManager */
        $serviceManager = $this->getServiceLocator();
        /** @var \Popov\ZfcUser\Controller\Plugin\UserAuthentication $uAuth */
        $uAuth = $serviceManager->get('UserAuthentication'); //@FIXME improve realisation
        $authService = $uAuth->getAuthService();
        if ($authService->hasIdentity()) {
            return $this->redirect()->toRoute('default', ['controller' => 'index', 'action' => 'index']);
        }

        $form = new LoginForm();
        //$login = ($sessionUserEmail->offsetExists('userEmail')) ? $sessionUserEmail->userEmail : '';
        //$form->get('email')->setValue($login);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                /** @var \Popov\ZfcUser\Service\UserService $userService */
                //$userService = $serviceManager->get($this->serviceName);
                $email = $request->getPost('email');
                $password = $request->getPost('password');
                //if (($auth = $uAuth->authenticate($email, $password)) && $auth->isValid()) {
                if ($uAuth->authenticate($email, $password)) {
                    $om = $serviceManager->get('Doctrine\ORM\EntityManager');
                    $user = $om->getRepository(User::class)->findOneBy([
                        'email' => $email,
                        'password' => UserAuthentication::getHashPassword($password)
                    ]);

                    $authService->getStorage()->write($user);

                    //$resource = unserialize($currentUser['resource']);
                    if ('all' === $user->getRoles()->first()->getResource()) {
                        // Set expire login
                        $sessionAuth = new SessionContainer('Zend_Auth');
                        $sessionAuth->setExpirationSeconds(3600); // 60 minutes
                        // Set user email
                        // Session user email
                        //$sessionUserEmail = new SessionContainer('userEmail');
                        //$sessionUserEmail->userEmail = $email;
                    }

                    $this->redirect()->toRoute('default', ['controller' => 'index', 'action' => 'index']);
                }
            }
        }

        $view = new ViewModel([
            'form' => $form,
        ]);
        // Disable layouts; use this view model in the MVC event instead
        $view->setTerminal(true);
        return $view;
    }

    public function logoutAction()
	{
	    $sm = $this->getServiceLocator();
        /** @var \Popov\ZfcUser\Controller\Plugin\UserAuthentication $uAuth */
        $uAuth = $sm->get('UserAuthentication');
        //$authService = $uAuth->getAuthService();

		/** @var \Popov\ZfcUser\Service\UsersService $usersService */
		//$usersService = $this->getServiceLocator()->get($this->serviceName);
        $uAuth->unAuthenticate();
        $this->redirect()->toRoute('default', ['controller' => $this->controllerRedirect, 'action' => 'login']);
    }

    public function forgotPasswordAction()
    {
        $request = $this->getRequest();
        $route = $this->getEvent()->getRouteMatch();
        $locator = $this->getServiceLocator();
        /** @var \Popov\ZfcUser\Service\UsersService $service */
        $service = $locator->get($this->serviceName);
        $statuses = $service->getStatuses();

        $form = new ForgotPasswordForm();
        $fields = ['email'];
        foreach ($fields as $field)
        {
            $form->get($field)->setValue('');
        }

        if ($request->isPost())
        {
            $post = $request->getPost();

            $form->setData($post);
            if ($form->isValid())
            {
                $postForm = $form->getData();
                $user = $service->getItem($postForm['email'], 'email', 0);

                if ($user->getId())
                {
                    $saveData = ['password' => $service::generatePassword()];
                    $service->save($saveData, $user);

                    // Send mail
                    $mailService = $locator->get('MailService');
                    $mailItem = $mailService->getOneItem($statuses['forgotPassword'], 'statusId');

                    if ($mailItem->getId())
                    {
                        $params = [
                            'mailItem'    => $mailItem,
                            'body'        => str_replace('{password}', $saveData['password'], $mailItem->getBody()),
                            'to'        => $postForm['email'],
                            'itemId'    => 0,
                        ];

                        $service->sendMail(__CLASS__, $params);
                    }
                    // END Send mail
                }
            }
        }

        $this->layout('layout/home');

        $view = new ViewModel([
            'form'            => $form,
            'errorsCount'    => count($form->getMessages()),
            'issetSaveData'    => isset($saveData),
        ]);

        // Disable layouts; use this view model in the MVC event instead
        $view->setTerminal(true);

        return $view;
    }

    public function generatePasswordsAction()
    {
        $locator = $this->getServiceLocator();
        /** @var \Popov\ZfcUser\Service\UsersService $service */
        $service = $locator->get($this->serviceName);
        $statuses = $service->getStatuses();

        // Send mail
        $mailService = $locator->get('MailService');
        $mailItem = $mailService->getOneItem($statuses['forgotPassword'], 'statusId');

        if ($mailItem->getId())
        {
            $items = $service->getAllCollection(0);

            foreach ($items as $item)
            {
                // Generate and change password
                $saveData = ['password' => $service::generatePassword()];
                $service->save($saveData, $item);

                $params = [
                    'mailItem'    => $mailItem,
                    'body'        => str_replace('{password}', $saveData['password'], $mailItem->getBody()),
                    'to'        => $item->getEmail(),
                    'itemId'    => 0,
                ];

                $service->sendMail(__CLASS__, $params);
            }
        }
        // END Send mail

        return;
    }

    public function changePasswordAction()
    {
        $request = $this->getRequest();
        $route = $this->getEvent()->getRouteMatch();
        $locator = $this->getServiceLocator();
        /** @var \Popov\ZfcUser\Service\UsersService $service */
        $service = $locator->get($this->serviceName);
        $pathUploadFiles = $service->getPathUploadFiles();

        $item = $service->getItem($this->currentUser['id']);

        $form = new ChangePasswordForm($this->dbAdapter);
        $fields = ['passwordOld', 'password', 'supplierId', 'email', 'firstName', 'lastName',
            'patronymic', 'phone', 'phoneWork', 'phoneInternal', 'post', 'dateBirth', 'dateEmployment', /*'photo',*/
            'showIndex', 'notation'];
        foreach ($fields as $field)
        {
            $method = 'get'.ucfirst($field);
            $value = (in_array($field, ['passwordOld', 'password'])) ? '' : $item->{$method}();

            if (stripos($field, 'date') !== false)
            {
                $value = is_object($value) ? $value->format('Y-m-d') : '';
            }

            $form->get($field)->setValue($value);
        }

        if ($request->isPost())
        {
            $_POST['id'] = $this->currentUser['id'];
            $post = $request->getPost();

            if ($post['passwordOld'] == '' && $post['password'] == '')
            {
                $form->getInputFilter()
                    ->remove('passwordOld')
                    ->remove('password');
            }

            $form->setData($post);
            /*$files = $request->getFiles()->toArray();

            $validatorFiles = new \Popov\Agere\Validator\File\Size([
                'max' => ini_get('upload_max_filesize').'B'
            ]);*/

            if (/*$validatorFiles->isValid($files['photo']) && */$form->isValid())
            {
                $postForm = $form->getData();

                // Upload files
                /*$upload = new Http();
                $upload->setDestination($pathUploadFiles.$item->getId().'/');
                $upload->setPrefixFileName('photo');
                $uploadFiles = $upload->receive($files);

                if ($uploadFiles)
                {
                    $photoName = explode('.', $uploadFiles[0]);
                    $photoExt = '.'.end($photoName);

                    // Resize image
                    $gb = new GbResize();
                    $gb->resizeToWidth($pathUploadFiles.$item->getId().'/'.$uploadFiles[0], $pathUploadFiles.$item->getId().'/small'.$photoExt, 130);
                    $gb->resizeToWidth($pathUploadFiles.$item->getId().'/'.$uploadFiles[0], $pathUploadFiles.$item->getId().'/'.$uploadFiles[0], 300);
                }*/


                $saveData = [];

                foreach ($fields as $field)
                {
                    /*if ($field == 'photo')
                    {
                        $saveData['photo'] = $uploadFiles ? $uploadFiles[0] : $item->getPhoto();
                    }
                    else */if (stripos($field, 'date') !== false)
                    {
                        $saveData[$field] = $postForm[$field] ? \DateTime::createFromFormat('Y-m-d', $postForm[$field]) : null;
                    }
                    else if ($field != 'passwordOld' && isset($postForm[$field]))
                    {
                        $saveData[$field] = $postForm[$field];
                    }
                }

                if ($saveData)
                {
                    $item = $service->save($saveData, $item);

                    // Update auth user
                    /** @var \Popov\ZfcUser\Controller\Plugin\UserAuthentication $uAuth */
                    $uAuth = $locator->get('UserAuthentication');
                    $authStorage = $uAuth->getAuthService()->getStorage();
                    $currentUser = $authStorage->read();

                    $fields = ['email', 'firstName', 'lastName', 'patronymic'];

                    foreach ($fields as $field)
                    {
                        $method = 'get'.ucfirst($field);
                        $currentUser[$field] = $item->{$method}();
                    }

                    $authStorage->write($currentUser);
                    // END Update auth user

                    // Write log
                    $params = [
                        'type'        => 'action',
                        'target'    => "{$this->controllerRedirect}/edit/{$item->getId()}",
                        'itemId'    => $item->getId(),
                        'message'    => "Редактирование <br>
                                        {$service->getMessageLog($fields, $item, $locator)}",
                    ];

                    $service->writeLog('Popov\Users\Controller\UsersController', $params);
                }

                //$this->redirect()->toRoute('home');
                $this->redirect()->toRoute('default', ['controller' => $this->controllerRedirect, 'action' => 'change-password']);
            }
        }

        $this->layout('layout/home');

        return [
            'id'        => $this->currentUser['id'],
            'form'        => $form,
            'fields'    => $service->getFields(),
        ];
    }

    public function monitoringAction()
    {
        $data = $this->indexAction('monitoring', 'monitoringFilters');
        $data['partialTemplate'] = 'children-monitoring';

        // Table logs
        /** @var \Popov\Logs\Service\LogsService $logsService */
        $logsService = $this->getServiceLocator()->get('LogsService');
        $data['logs'] = $logsService->getItemsMaxDate([
            'module'    => 'Popov\Users',
            'type'        => 'action',
            'target'    => 'users/login',
        ], 'userId', 'userId');

        $this->layout('layout/home');

        $viewModel = new ViewModel();
        $viewModel->setVariables($data);
        return $viewModel->setTemplate("magere/users/index.phtml");
    }


    //------------------------------------AJAX----------------------------------------
    /**
     * @param string $class
     * @param string $name
     * @param null|\Zend\Http\Request $request
     * @param null $locator
     * @return JsonModel
     */
    public function deleteAction($class = __CLASS__, $name = 'users', $request = null, $locator = null)
    {
        if (is_null($request)) {
            $request = $this->getRequest();
            $locator = $this->getServiceLocator();
        }

        if ($request->isPost() && $request->isXmlHttpRequest()) {
            /** @var \Popov\ZfcUser\Service\UsersService $service */
            $service = $locator->get($this->serviceName);
            // Access to page for current user
            $responseEvent = $service->delete($class, [], $name);
            $message = $responseEvent->first()['message'];
            // END Access to page for current user
            if ($message == '') {
                $allow = false;
                $post = $request->getPost();
                $user = $service->getItem($post['id']);

                //    $allow = $service->deleteItem($user);

                
                if (!$allow) {
                    $service->save(['remove' => 1], $user);
                    // Write log
                    $params = [
                        'type' => 'action',
                        'target' => 'users/delete',
                        'itemId' => $user->getId(),
                        'message' => 'Удалено пользователя: ' . $user->getEmail(),
                    ];
                    $service->writeLog(__CLASS__, $params);
                }
                $result = new JsonModel([
                    //'message' => ($allow) ? '' : 'Невозможно удалить № '.$post['id'].'. Сначала уберите прив\'язку к позиции!',
                    'message' => '',
                ]);
            } else {
                $result = new JsonModel([
                    'message' => $message,
                ]);
            }

            return $result;
        } else {
            $this->getResponse()->setStatusCode(404);
        }
    }

    /**
     * @param string $class
     * @param string $name
     * @param null|\Zend\Http\Request $request
     * @param null $locator
     * @param null|\Zend\Mvc\Router\RouteMatch $route
     * @return JsonModel
     */
    public function deleteFileAction($class = __CLASS__, $name = 'users', $request = null, $locator = null, $route = null)
    {
        if (is_null($request))
        {
            $request = $this->getRequest();
            $route = $this->getEvent()->getRouteMatch();
            $locator = $this->getServiceLocator();
        }

        if ($request->isPost() && $request->isXmlHttpRequest())
        {
            /** @var \Popov\ZfcUser\Service\UsersService $service */
            $service = $locator->get($this->serviceName);

            // Access to page for current user
            $responseEvent = $service->deleteFile($class, [], $name);
            $message = $responseEvent->first()['message'];
            // END Access to page for current user

            if ($message == '')
            {
                $user = $service->getItem($route->getParam('id'));

                // Delete file
                $pathUploadFiles = $service->getPathUploadFiles();

                $service->deleteFile(__CLASS__, [
                    'filePath' => $pathUploadFiles.$user->getId().'/'.$user->getPhoto(),
                ]);

                $photoName = explode('.', $user->getPhoto());
                $photoExt = '.'.end($photoName);
                $service->deleteFile(__CLASS__, [
                    'filePath' => $pathUploadFiles.$user->getId().'/middle'.$photoExt,
                ]);
                $service->deleteFile(__CLASS__, [
                    'filePath' => $pathUploadFiles.$user->getId().'/small'.$photoExt,
                ]);
                // END Delete file

                $service->save(['photo' => ''], $user);

                // Write log
                $params = [
                    'type'        => 'action',
                    'target'    => 'users/delete-file',
                    'itemId'    => $user->getId(),
                    'message'    => 'Удалено фото: '.$user->getEmail(),
                ];

                $service->writeLog(__CLASS__, $params);

                $message = '';
            }

            return new JsonModel([
                'message' => $message,
            ]);
        }
        else
            $this->getResponse()->setStatusCode(404);
    }

    public function getEM() {
        return $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        //return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }
}
