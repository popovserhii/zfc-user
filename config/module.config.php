<?php
namespace Popov\ZfcUser;

return array(
	'controllers' => array(
		'invokables' => array(
			'user' => Controller\UserController::class, // @deprecated
			'staff'	=> Controller\StaffController::class,
		),
	),

	'view_manager' => array(
		'template_map' => array(
            'widget/logout' => __DIR__ . '/../view/widget/logout.phtml',
            'users/children-index'		=> __DIR__ . '/../view/popov/user/children/index/index.phtml',
			'users/children-monitoring'	=> __DIR__ . '/../view/popov/user/children/monitoring/index.phtml',
			'users/edit/basic-data'		=> __DIR__ . '/../view/popov/user/tabs/edit/basic-data.phtml',
		),
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
	),


	'controller_plugins' => [
		/*'invokables' => [
			'user' => 'Popov\Users\Controller\Plugin\User',
		],*/
		'factories' => [
			'user' => Controller\Plugin\Factory\UserFactory::class,
		],
	],
	'view_helpers' => [
		'invokables' => [
			'user' => View\Helper\User::class,
		],
		/*'factories' => [
			'user' => 'Popov\Users\View\Helper\Factory\UserFactory',
		],*/
	],

	'service_manager' => array(
        'initializers' => [
            //'UserAwareInterface' => Service\Factory\UserInitializer::class,
        ],
		'aliases' => array(
			'Acl'		        => Acl\Acl::class,
			'UserService'		=> Service\UserService::class,
			'User'				=> Model\User::class,
			'UserCityService'	=> Service\UserCityService::class,
			'UserRoleService'	=> Service\UserRoleService::class,

			'UserAuthentication' => Controller\Plugin\UserAuthentication::class,
		),

        'invokables' => [
            Acl\Acl::class => Acl\Acl::class,
            Service\UserCityService::class => Service\UserCityService::class,
            Service\UserRoleService::class => Service\UserRoleService::class,
        ],

		'factories' => array(
            Service\UserService::class => Service\Factory\UserServiceFactory::class,

			/*'Popov\Users\Service\UsersCityService' => function ($sm) {
				$em = $sm->get('Doctrine\ORM\EntityManager');
				$service = \Popov\Agere\Service\Factory\Helper::create('users/usersCity', $em);

				return $service;
			},*/

			/*'Popov\Users\Service\UsersRolesService' => function ($sm) {
				$em = $sm->get('Doctrine\ORM\EntityManager');
				$service = \Popov\Agere\Service\Factory\Helper::create('users/usersRoles', $em);

				return $service;
			},*/

			/*'Popov\Users\Controller\Plugin\UserAuthentication' => function($sm) {
				$authAdapter = $sm->get('Popov\Agere\Authentication\Adapter\DbTable\CredentialTreatmentAdapter');

				$userAuthentication = new \Popov\ZfcUser\Controller\Plugin\UserAuthentication();
				//$userAuthentication->setController();
				$userAuthentication->setAuthAdapter($authAdapter);

				return $userAuthentication;
			},*/

            Controller\Plugin\UserAuthentication::class => Controller\Plugin\Factory\UserAuthenticationFactory::class,

            //\Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter::class => Authentication\Adapter\DbTable\Factory\CredentialTreatmentAdapterFactory::class,

            /*'Popov\Agere\Authentication\Adapter\DbTable\CredentialTreatmentAdapter' => function($sm) {
				$zendDb = $sm->get('Zend\Db\Adapter\Adapter');
				$tableName = 'user';
				$identityColumn = 'email';
				$credentialColumn = 'password';
				$credentialTreatment = '?';

				$adapter = new \Popov\Agere\Authentication\Adapter\DbTable\CredentialTreatmentAdapter(
					$zendDb,
					$tableName,
					$identityColumn,
					$credentialColumn,
					$credentialTreatment
				);

				return $adapter;
			},*/

			'Popov\Users\Event\Authentication' => Event\Factory\AuthenticationFactory::class
		),
	),

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
	'navigation' => array(
		// The DefaultNavigationFactory we configured in (1) uses 'default' as the sitemap key
		'default' => array(
			// And finally, here is where we define our page hierarchy
			'users' => array(
				'module' => 'users',
				'label' => 'Главная',
				'route' => 'default',
				'controller' => 'index',
				'action' => 'index',
				'pages' => array(

					'settings-index' => array(
						'label'      => 'Настройки',
						'route'      => 'default',
						'controller' => 'settings',
						'action'     => 'index',
						'pages' => array(

							'users-index' => array(
								'label' => 'Пользователи',
								'route' => 'default',
								'controller' => 'users',
								'action' => 'index',
								'pages' => array(
									'users-add' => array(
										'label' => 'Добавить',
										'route' => 'default',
										'controller' => 'users',
										'action' => 'add',
									),
									'users-edit' => array(
										'label' => 'Редактировать',
										'route' => 'default/id',
										'controller' => 'users',
										'action' => 'edit',
									),
									'users-history' => array(
										'label' => 'Редактировать',
										'route' => 'default/id',
										'controller' => 'users',
										'action' => 'history',
									),
								),
							),

							'users-index-wildcard' => array(
								'label' => 'Пользователи',
								'route' => 'default/id/wildcard',
								'controller' => 'users',
								'action' => 'index',
							),

							'users-index-id' => array(
								'label' => 'Пользователи',
								'route' => 'default/id',
								'controller' => 'users',
								'action' => 'index',
							),

							'users-monitoring' => array(
								'label' => 'Мониторинг пользователей',
								'route' => 'default',
								'controller' => 'users',
								'action' => 'monitoring',
							),

							'users-monitoring-wildcard' => array(
								'label' => 'Мониторинг пользователей',
								'route' => 'default/id/wildcard',
								'controller' => 'users',
								'action' => 'monitoring',
							),

							'users-change-password' => array(
								'label' => 'Личные данные',
								'route' => 'default',
								'controller' => 'users',
								'action' => 'change-password',
							),

						),
					),

					'staff-index-id' => array(
						'label' => 'Наши сотрудники',
						'route' => 'default',
						'controller' => 'staff',
						'action' => 'index',
						'pages' => array(

							'staff-edit' => array(
								'label' => 'Редактировать',
								'route' => 'default/id',
								'controller' => 'staff',
								'action' => 'edit',
							),

						),
					),

					'staff-index' => array(
						'label' => 'Наши сотрудники',
						'route' => 'default',
						'controller' => 'staff',
						'action' => 'index',
					),

					'staff-index-wildcard' => array(
						'label' => 'Наши сотрудники',
						'route' => 'default',
						'controller' => 'staff',
						'action' => 'index',
					),

				),
			),
		),
	),

);