<?php
namespace Popov\ZfcUser;

//use Zend\Authentication\AuthenticationService;

use Zend\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;

return [

    'assetic_configuration' => require __DIR__ . '/assets.config.php',

    'dependencies' => [
        'aliases' => [
            'User' => Model\User::class,
            'UserService' => Service\UserService::class,
            'UserCityService' => Service\UserCityService::class,
            'UserRoleService' => Service\UserRoleService::class,
            //'UserAuthentication' => Controller\Plugin\AuthService::class, // instead use \Popov\ZfcUser\Auth\Auth
        ],
        'invokables' => [
            Service\UserCityService::class => Service\UserCityService::class,
            Service\UserRoleService::class => Service\UserRoleService::class,
        ],
        'factories' => [
            Service\UserService::class => Service\Factory\UserServiceFactory::class,
            Helper\UserHelper::class => Helper\UserHelperFactory::class,
            Auth\Auth::class => Auth\Factory\AuthFactory::class,
        ],
    ],

    'actions' => [
        'user' => __NAMESPACE__ . '\Action'
    ],

    'controllers' => [
        'aliases' => [
            'user' => Controller\UserController::class,
            'staff' => Controller\StaffController::class,
        ],
        'factories' => [
            Controller\UserController::class => ReflectionBasedAbstractFactory::class,
        ]
    ],

    'controller_plugins' => [
        'factories' => [
            'user' => Controller\Plugin\Factory\UserHelperFactory::class,
        ],
    ],

    'view_helpers' => [
        'aliases' => [
            'user' => View\Helper\UserHelper::class,
        ],
        'factories' => [
            View\Helper\UserHelper::class => View\Helper\Factory\SimplerHelperFactory::class,
        ]
    ],

    // mvc
    'view_manager' => [
        'template_map' => [
            'layout::admin-login' => __DIR__ . '/../view/layout/admin/login.phtml',
            'widget::logout' => __DIR__ . '/../view/widget/logout.phtml',

            #'admin-user::login' => __DIR__ . '/../view/admin/user/login.phtml',

            'users/children-index' => __DIR__ . '/../view/popov/user/children/index/index.phtml',
            'users/children-monitoring' => __DIR__ . '/../view/popov/user/children/monitoring/index.phtml',
            'users/edit/basic-data' => __DIR__ . '/../view/popov/user/tabs/edit/basic-data.phtml',
        ],
        /*'template_path_stack' => [
            __DIR__ . '/../view',
        ],*/
        'prefix_template_path_stack' => [
            'user::' => __DIR__ . '/../view/user',
        ],
    ],

    // middleware
    'templates' => [
        'map' => [
            'layout::admin-login'  => __DIR__ . '/../view/admin/layout/login.phtml',
        ],
        'paths' => [
            'admin-user'  => [__DIR__ . '/../view/admin/user'],
            'widget' => [__DIR__ . '/../view/admin/widget'],
        ],
    ],

    'translator' => [
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
                'text_domain' => __NAMESPACE__,
            ],
        ],
    ],

    // Doctrine config
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src//Model'],
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Model' => __NAMESPACE__ . '_driver',
                ],
            ],
        ],
    ],
    // @link http://adam.lundrigan.ca/2012/07/quick-and-dirty-zf2-zend-navigation/
    // All navigation-related configuration is collected in the 'navigation' key
    'navigation' => [
        // The DefaultNavigationFactory we configured in (1) uses 'default' as the sitemap key
        'default' => [
            // And finally, here is where we define our page hierarchy
            'users' => [
                'module' => 'users',
                'label' => 'Главная',
                'route' => 'default',
                'controller' => 'index',
                'action' => 'index',
                'pages' => [
                    'settings-index' => [
                        'label' => 'Настройки',
                        'route' => 'default',
                        'controller' => 'settings',
                        'action' => 'index',
                        'pages' => [
                            'users-index' => [
                                'label' => 'Пользователи',
                                'route' => 'default',
                                'controller' => 'users',
                                'action' => 'index',
                                'pages' => [
                                    'users-add' => [
                                        'label' => 'Добавить',
                                        'route' => 'default',
                                        'controller' => 'users',
                                        'action' => 'add',
                                    ],
                                    'users-edit' => [
                                        'label' => 'Редактировать',
                                        'route' => 'default/id',
                                        'controller' => 'users',
                                        'action' => 'edit',
                                    ],
                                    'users-history' => [
                                        'label' => 'Редактировать',
                                        'route' => 'default/id',
                                        'controller' => 'users',
                                        'action' => 'history',
                                    ],
                                ],
                            ],
                            'users-index-wildcard' => [
                                'label' => 'Пользователи',
                                'route' => 'default/id/wildcard',
                                'controller' => 'users',
                                'action' => 'index',
                            ],
                            'users-index-id' => [
                                'label' => 'Пользователи',
                                'route' => 'default/id',
                                'controller' => 'users',
                                'action' => 'index',
                            ],
                            'users-monitoring' => [
                                'label' => 'Мониторинг пользователей',
                                'route' => 'default',
                                'controller' => 'users',
                                'action' => 'monitoring',
                            ],
                            'users-monitoring-wildcard' => [
                                'label' => 'Мониторинг пользователей',
                                'route' => 'default/id/wildcard',
                                'controller' => 'users',
                                'action' => 'monitoring',
                            ],
                            'users-change-password' => [
                                'label' => 'Личные данные',
                                'route' => 'default',
                                'controller' => 'users',
                                'action' => 'change-password',
                            ],
                        ],
                    ],
                    'staff-index-id' => [
                        'label' => 'Наши сотрудники',
                        'route' => 'default',
                        'controller' => 'staff',
                        'action' => 'index',
                        'pages' => [
                            'staff-edit' => [
                                'label' => 'Редактировать',
                                'route' => 'default/id',
                                'controller' => 'staff',
                                'action' => 'edit',
                            ],
                        ],
                    ],
                    'staff-index' => [
                        'label' => 'Наши сотрудники',
                        'route' => 'default',
                        'controller' => 'staff',
                        'action' => 'index',
                    ],
                    'staff-index-wildcard' => [
                        'label' => 'Наши сотрудники',
                        'route' => 'default',
                        'controller' => 'staff',
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
];