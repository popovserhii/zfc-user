<?php
namespace Popov\ZfcUser\Model\Repository;

use Doctrine\ORM\Query\ResultSetMapping;
use	Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\EntityRepository;

class UsersCityRepository extends EntityRepository {

	protected $_table = 'users_city';
	protected $_alias = 'uc';


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
	 * @param string $where
	 * @param array $args
	 * @param array $orderBy
	 * @return array
	 */
	public function findCity($where = '', array $args = [], array $orderBy = [])
	{
		$rsm = new ResultSetMapping();

		$rsm->addScalarResult('id', 'id');
		$rsm->addScalarResult('userId', 'userId');
		$rsm->addScalarResult('city', 'city');
		$rsm->addScalarResult('company', 'company');

		$order = $this->getOrderBy($orderBy);

		if ($order != '')
		{
			$order = "ORDER BY {$order}";
		}

		$query = $this->_em->createNativeQuery(
			"SELECT `{$this->_alias}`.*, c.`city`, c.`name` AS company
			FROM {$this->_table} {$this->_alias}
			LEFT JOIN `city` c ON uc.`cityId` = c.`id`
			WHERE 1 > 0 {$where}
			{$order}",
			$rsm
		);

		if ($args)
		{
			$query = $this->setParametersByArray($query, $args);
		}

		return $query->getResult();
	}

}