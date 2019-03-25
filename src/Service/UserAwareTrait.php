<?php
namespace Popov\ZfcUser\Service;

use Popov\ZfcUser\Model\User as User;

/**
 * Trait to provide user aware setter and getter
 */
trait UserAwareTrait {
	/**
	 * @var User
	 */
	protected $user;

	/**
	 * Set the user object
	 *
	 * @param null|User $user
	 * @return $this
	 */
	public function setUser(?User $user) {
		$this->user = $user;

		return $this;
	}

	/**
	 * Get the user object
	 *
	 * @return null|User
	 */
	public function getUser(): ?User {
		return $this->user;
	}

}
