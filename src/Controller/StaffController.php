<?php
namespace Popov\ZfcUser\Controller;

use Zend\Mvc\Controller\AbstractActionController,
    Zend\View\Model\ViewModel,
    Zend\View\Model\JsonModel,
    Zend\Session\Container as SessionContainer,
    Popov\Agere\File\Transfer\Adapter\Http,
    Popov\Agere\File\Resize\Adapter\GbResize,
    Popov\ZfcUser\Form\User as UserForm,
    Popov\ZfcUser\Controller\UserController;
use Popov\City\Model\City;
use Popov\ZfcUser\Model\User as User;
use Popov\Roles\Model\Roles as Role;

/**
 * Class StaffController
 *
 * @method \Agere\Simpler\Plugin\SimplerPlugin simpler($collection = null)
 */
class StaffController extends AbstractActionController {

    public $serviceName = 'UsersService';
    public $sessionName = 'staffFilters';
    public $controllerRedirect = 'staff';


    public function indexAction()
    {
        $session = new SessionContainer($this->sessionName);
        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();
        $route = $this->getEvent()->getRouteMatch();
        $locator = $this->getServiceLocator();
        /** @var \Popov\ZfcUser\Service\UsersService $userService */
        $userService = $locator->get($this->serviceName);

        $currentPage = (int) $route->getParam('page');

        // search fields: cityId, departmentId, lastName, post

        $filtersSelected = [
            'cityId'		=> 0,
            //'departmentId'	=> 0,
            'supplierId'	=> 0,
        ];

        $search = '';

        $viewHelper = $locator->get('viewhelpermanager');
        /** @var \Popov\Store\View\Helper\Conditions $helperConditions */
        $helperConditions = $viewHelper->get('conditions');
        $where = $helperConditions->conditionPermission('staffIndex');

        if ($request->isPost())
        {
            $currentPage = 0;
            $post = $request->getPost()->toArray();

            // Clear session
            $session->getManager()->getStorage()->clear($this->sessionName);


            if (! isset($post['reset_filters']))
            {
                // Set parameters

                // Filters selected
                $filtersSelected = $userService->filters($filtersSelected, $post);

                $search = $post['search'];

                // END Set parameters


                // Set session
                $session->filtersSelected = $filtersSelected;
                $session->search = $search;
            }
        }
        else if ($session->offsetExists('search')) // Session
        {
            // Set parameters
            $sessionStoreFilters = $session->getManager()->getStorage()->offsetGet($this->sessionName);

            $search = $sessionStoreFilters['search'];
            $filtersSelected = $sessionStoreFilters['filtersSelected'];
            // END Set parameters
        }

        $session->page = $currentPage;

        // Table users
        $whereStr = '';

        $where['u.remove'] = 0;

        if ($filtersSelected['cityId'])
        {
            $where['uc.cityId'] = $filtersSelected['cityId'];
        }

        /*if ($filtersSelected['departmentId'])
        {
            $where['u.departmentId'] = $filtersSelected['departmentId'];
        }*/

        if ($filtersSelected['supplierId'])
        {
            $where['u.supplierId'] = $filtersSelected['supplierId'];
        }

        if ($search)
        {
            $fieldsSearch = ['u.lastName', 'u.post'];
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

        $om = $userService->getEntityManager();


        $userItems = $userService->getItemsCollection($where, $currentPage, true, null, [], ['u.id'], $whereStr, 'u.id', 19);
        $ids = $userService->toArrayKeyVal('id', $userItems);

        $users = $om->getRepository(User::class)->findBy(['id' => $ids]);

        return [
            'fields'			=> $userService->getFields(),
            'users'				=> $users,
            'filtersSelected'	=> ([
                'cityId'		=> $filtersSelected['cityId'],
                //'departmentId'	=> $filtersSelected['departmentId'],
                'supplierId'	=> $filtersSelected['supplierId'],
                'search'		=> $search,
            ]),
            'paginator'			=> $userService->getPager()->getStrategy(),
            'sizesPhoto'		=> $userService->getSizesPhoto(),
        ];
    }

    public function editAction()
    {
        $session = new SessionContainer($this->sessionName);
        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();
        $route = $this->getEvent()->getRouteMatch();
        $locator = $this->getServiceLocator();
        /** @var \Popov\ZfcUser\Service\UsersService $userService */
        $userService = $locator->get($this->serviceName);
        $pathUploadFiles = $userService->getPathUploadFiles();
        $sizesPhoto = $userService->getSizesPhoto();

        $id = (int) $route->getParam('id');

        // Check permission to page
        $viewHelper = $locator->get('viewhelpermanager');
        /** @var \Popov\Store\View\Helper\Conditions $helperConditions */
        $helperConditions = $viewHelper->get('conditions');
        $where = $helperConditions->conditionPermission('staffIndex');

        $where['u.remove'] = 0;
        $where['u.id'] = $id;
        $items = $userService->getItemsCollection($where);

        if ($id && ! $items)
        {
            // redirect to url
            $uri = $request->getUri();
            $baseUri = sprintf('%s://%s', $uri->getScheme(), $uri->getHost());

            $response = $this->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $baseUri.'/staff/index');
            $response->setStatusCode(302);
            $response->sendHeaders();
            exit;
        }
        // END Check permission to page

        $user = $userService->getItem($id, 'id', '0');

        /** @var \Popov\ZfcUser\Service\UsersCityService $serviceUsersCity */
        $serviceUsersCity = $locator->get('UsersCityService');
        $fields = [/*'departmentId', */
            'supplierId',
            'email',
            'firstName',
            'lastName',
            'patronymic',
            'phone',
            'phoneWork',
            'phoneInternal',
            'post',
            'dateBirth',
            'dateEmployment',
            'photo',
            'cityId[]',
            'showIndex',
            'notation',
        ];

        $form = new UserForm($id, $fields, $locator->get('Zend\Db\Adapter\Adapter'));
        foreach ($fields as $field) {
            $method = 'get' . ucfirst($field);
            if ($field == 'cityId[]') {
                $value = $this->simpler($user->getCities())->asArray();
            } else if (stripos($field, 'date') !== false) {
                $value = is_object($user->$method()) ? $user->$method()->format('Y-m-d') : '';
            } else {
                $value = $user->$method();
            }
            $form->get($field)->setValue($value);
        }

        if ($request->isPost())
        {
            $values = $request->getPost()->toArray();

            if (isset($values['cityId']))
            {
                $values['cityId'] = array_filter($values['cityId']);
            }

            unset($values['save']);

            $form->setData($values);

            if ($form->isValid())
            {
                $post = $form->getData();

                $thumb = (isset($values['thumb_values']) && $values['thumb_values']) ? json_decode($values['thumb_values']) : '';

                if ($thumb)
                {
                    // Upload files
                    $upload = new Http();
                    $upload->createFolder($pathUploadFiles.$id.'/');

                    $photoName = explode('.', $thumb->name);
                    $photoExt = '.'.end($photoName);

                    // Resize image
                    $gb = new GbResize();
                    $gb->resizeToWidth($thumb->data, $pathUploadFiles.$id.'/'.$thumb->name, $sizesPhoto['large'], $thumb->left, $thumb->top);
                    $gb->resizeToWidth($pathUploadFiles.$id.'/'.$thumb->name, $pathUploadFiles.$id.'/middle'.$photoExt, $sizesPhoto['middle']);
                    $gb->resizeToWidth($pathUploadFiles.$id.'/'.$thumb->name, $pathUploadFiles.$id.'/small'.$photoExt, $sizesPhoto['small']);
                }

                $saveData = [];
                $saveDataCities = [];

                foreach ($fields as $field)
                {
                    if ($field == 'photo')
                    {
                        if ($thumb)
                        {
                            $saveData['photo'] = $thumb->name;
                        }
                        else if (! $id)
                        {
                            $saveData['photo'] = '';
                        }
                    }
                    else if ($field == 'cityId[]' && isset($post['cityId']))
                    {
                        $saveDataCities['cityId'] = $post['cityId'];
                    }

                    if (isset($_POST[$field]))
                    {
                        if (stripos($field, 'date') !== false)
                        {
                            $saveData[$field] = $post[$field] ? \DateTime::createFromFormat('Y-m-d', $post[$field]) : null;
                        }
                        else
                        {
                            $saveData[$field] = $post[$field];
                        }
                    }
                }

                if ($saveData)
                {
                    $user = $userService->save($saveData, $user);

                    // Save users city
                    if ($saveDataCities && $user->getId())
                    {
                        $serviceUsersCity->saveData($saveDataCities, $user);
                    }

                    // Write log
                    $params = [
                        'type'		=> 'action',
                        'target'	=> "{$this->controllerRedirect}/edit/{$user->getId()}",
                        'itemId'	=> $user->getId(),
                        'message'	=> "Редактирование <br>
										{$userService->getMessageLog($fields, $user, $locator)}",
                    ];

                    $userService->writeLog('Popov\Users\Controller\UsersController', $params);
                }

                // Redirect from session container
                $page = ($session->offsetExists('page') && $session->page) ? '/page/'.$session->page : '';

                $this->redirect()->toUrl('/staff/index'.$page);
            }
        }

        return [
            'id'			=> $id,
            'form'			=> $form,
            'fields'		=> $userService->getFields(),
            'items'			=> $items,
        ];
    }


    //------------------------------------AJAX----------------------------------------
    /**
     * Ajax delete
     */
    public function deleteAction()
    {
        $request = $this->getRequest();
        $locator = $this->getServiceLocator();

        $usersController = new UserController();
        return $usersController->deleteAction(__CLASS__, $this->controllerRedirect, $request, $locator);
    }

    public function deleteFileAction()
    {
        $request = $this->getRequest();
        $route = $this->getEvent()->getRouteMatch();
        $locator = $this->getServiceLocator();

        $usersController = new UserController();
        return $usersController->deleteFileAction(__CLASS__, $this->controllerRedirect, $request, $locator, $route);
    }

}