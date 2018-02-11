<?php
namespace Popov\ZfcUser;

return [
    'acl' => require __DIR__ . '/acl.config.php',

    'assetic_configuration' => require __DIR__ . '/assets.config.php',

    'dependencies' => [
        'aliases' => [
            'Acl' => Acl\Acl::class,
            'User' => Model\User::class,
            'UserService' => Service\UserService::class,
            'UserCityService' => Service\UserCityService::class,
            'UserRoleService' => Service\UserRoleService::class,
            'UserAuthentication' => Controller\Plugin\UserAuthentication::class,
        ],
        'invokables' => [
            Acl\Acl::class => Acl\Acl::class,
            Service\UserCityService::class => Service\UserCityService::class,
            Service\UserRoleService::class => Service\UserRoleService::class,
        ],
        'factories' => [
            Service\UserService::class => Service\Factory\UserServiceFactory::class,
            Event\Authentication::class => Event\Factory\AuthenticationFactory::class,
            Controller\Plugin\UserAuthentication::class => Controller\Plugin\Factory\UserAuthenticationFactory::class,
        ],
    ],

    'controllers' => [
        'invokables' => [
            'user' => Controller\UserController::class,
            'staff' => Controller\StaffController::class,
        ],
    ],

    'controller_plugins' => [
        'factories' => [
            'user' => Controller\Plugin\Factory\UserPluginFactory::class,
        ],
    ],

    'view_helpers' => [
        'invokables' => [
            'user' => View\Helper\UserHelper::class,
        ],
    ],

    // mvc
    'view_manager' => [
        'template_map' => [
            'widget/logout' => __DIR__ . '/../view/widget/logout.phtml',
            'users/children-index' => __DIR__ . '/../view/popov/user/children/index/index.phtml',
            'users/children-monitoring' => __DIR__ . '/../view/popov/user/children/monitoring/index.phtml',
            'users/edit/basic-data' => __DIR__ . '/../view/popov/user/tabs/edit/basic-data.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

    // middleware
    'templates' => [
        'paths' => [
            'admin-user'  => [__DIR__ . '/../view/admin/user'],
            //'layout' => [__DIR__ . '/../view/layout'],
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