<?php
namespace Popov\ZfcUser\Service;

use Popov\Agere\Service\AbstractEntityService,
	Popov\ZfcUser\Model\User as UsersModel;

class UserCityService extends AbstractEntityService
{
	protected $_repositoryName = 'userCity';

	/**
	 * @param int|array $id
	 * @param string $field
	 * @return mixed
	 */
	public function getItems($id, $field = 'id')
	{
		/** @var \Popov\ZfcUser\Model\Repository\UsersCityRepository $repository */
		$repository = $this->getRepository($this->_repositoryName);

		return $repository->findItems($id, $field);
	}

	/**
	 * @param array $condition, example ['field' => $val, 'field2' => $val2]
	 * @param array $orderBy
	 * @return mixed
	 */
	public function getItemsCity(array $condition = [], array $orderBy = [])
	{
		/** @var \Popov\ZfcUser\Model\Repository\UsersCityRepository $repository */
		$repository = $this->getRepository($this->_repositoryName);

		$where = $repository->getWhereByArray($condition);

		return $repository->findCity($where['str'], $where['args'], $orderBy);
	}

	/**
	 * @param int $id
	 * @param string $field
	 * @return array
	 */
	public function getCityIdItemsArray($id, $field = 'id')
	{
		$result = [];
		$items = $this->getItems($id, $field);

		/** @var \Popov\ZfcUser\Model\UsersCity $item */
		foreach ($items as $item)
		{
			$result[] = $item->getCityId();
		}

		return $result;
	}

	/**
	 * @param array $data
	 * @param UsersModel $user
	 */
	public function saveData(array $data, UsersModel $user)
	{
		/** @var \Popov\ZfcUser\Model\Repository\UsersCityRepository $repository */
		//$repository = $this->getRepository($this->_repositoryName);

		$i = 0;
		//$items = $this->getItems($userItem->getId(), 'userId');
		$cities = $user->getCities();

		foreach ($cities as $city)
		{

			if (isset($data['cityId'][$i]))
			{
				//$oneItem->setCityId($data['cityId'][$i]);
				unset($data['cityId'][$i]);
				//$repository->addItem($oneItem);

				$this->getEntityManager()->persist($city);

				++ $i;
			}
			else
			{

				//$repository->addRemove($oneItem);
				$this->getEntityManager()->remove($city);
				//$ids[] = $city->getId();
			}
		}
		/*if (isset($data['cityId'])) {
			foreach ($data['cityId'] as $val) {
				$oneItem = $repository->createOneItem();
				//$oneItem->setCityId($val);
				$oneItem->setUsers($user);
				//$oneItem->setUserId($user->getId());
				$repository->addItem($oneItem);
			}
		}
		$repository->saveData();*/

		$this->getEntityManager()->flush();
	}

}