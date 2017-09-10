<?php
namespace Popov\ZfcUser\Authentication\Adapter\DbTable\Factory;

use Interop\Container\ContainerInterface;
//use Popov\ZfcUser\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;

class CredentialTreatmentAdapterFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $zendDb = $container->get('Zend\Db\Adapter\Adapter');
        //$zendDb = $container->get('Zend\Db\Adapter\AdapterInterface');
        $tableName = 'user';
        $identityColumn = 'email';
        $credentialColumn = 'password';
        $credentialTreatment = '?';
        /*$adapter = new CredentialTreatmentAdapter(
            $zendDb,
            $tableName,
            $identityColumn,
            $credentialColumn,
            $credentialTreatment
        );*/

        $adapter = new CredentialTreatmentAdapter(
            $zendDb,
            $tableName,
            $identityColumn,
            $credentialColumn,
            $credentialTreatment
        );

        return $adapter;
    }
}