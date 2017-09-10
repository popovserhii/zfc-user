<?php
namespace Popov\ZfcUser\Model\Repository;

use Doctrine\ORM\Query\ResultSetMapping;
use	Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\EntityRepository;

class UserRoleRepository extends EntityRepository {

	protected $_table = 'users_roles';
	protected $_alias = 'ur';


	/**
	 * @param int|array $id
	 * @param string $field
	 * @return mixed
	 */
	public function findItems($id, $field = 'id')
	{
		$rsm = new ResultSetMappingBuilder($this->_em);
		$rsm->addRootEntityFromClassMetadata($this->getEntityName(), $this->_alias);

		$data = [];
		$idIn = '';

		if (is_array($id))
		{
			$idIn .= $this->getIdsIn($id);
			$data = array_merge($data, $id);
		}
		else
		{
			$idIn .= '?';
			$data[] = $id;
		}

		$query = $this->_em->createNativeQuery(
			"SELECT *
			FROM {$this->_table} {$this->_alias}
			WHERE {$this->_alias}.`$field` IN ({$idIn})",
			$rsm
		);

		$query = $this->setParametersByArray($query, $data);

		return $query->getResult();
	}

	/**
	 * @param int|array $id
	 * @param string $field
	 * @return array
	 */
	public function findItemsRoles($id, $field = 'id')
	{
		$rsm = new ResultSetMapping();

		$rsm->addEntityResult($this->getEntityName(), $this->_alias);
		$rsm->addFieldResult($this->_alias, 'id', 'id');
		$rsm->addFieldResult($this->_alias, 'userId', 'userId');
		$rsm->addFieldResult($this->_alias, 'roleId', 'roleId');
		$rsm->addScalarResult('resource', 'resource');

		$data = [];
		$idIn = '';

		if (is_array($id))
		{
			$idIn .= $this->getIdsIn($id);
			$data = array_merge($data, $id);
		}
		else
		{
			$idIn .= '?';
			$data[] = $id;
		}

		$query = $this->_em->createNativeQuery(
			"SELECT {$this->_alias}.*, r.`resource`
			FROM {$this->_table} {$this->_alias}
			INNER JOIN `roles` r ON {$this->_alias}.`roleId` = r.`id`
			WHERE {$this->_alias}.`$field` IN ({$idIn})",
			$rsm
		);

		$query = $this->setParametersByArray($query, $data);

		return $query->getResult();
	}

}