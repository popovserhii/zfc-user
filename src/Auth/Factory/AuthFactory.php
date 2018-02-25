<?php

namespace Popov\ZfcUser\Auth\Factory;

use Interop\Container\ContainerInterface;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Authentication\AuthenticationService;
use Popov\ZfcUser\Auth\Auth;

class AuthFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $zendDb = $container->get('Zend\Db\Adapter\Adapter');
        $tableName = 'user';
        $identityColumn = 'email';
        $credentialColumn = 'password';
        $credentialTreatment = '?';

        $authAdapter = new CredentialTreatmentAdapter(
            $zendDb,
            $tableName,
            $identityColumn,
            $credentialColumn,
            $credentialTreatment
        );

        /*$authService = new AuthenticationService(
            null,
            $authAdapter
        );*/

        $auth = new Auth($authAdapter/*, $authService*/);

        return $auth;
    }
}