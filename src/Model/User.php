<?php
namespace Popov\ZfcUser\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Popov\ZfcCore\Model\DomainAwareTrait;

/**
 * @ORM\Entity(repositoryClass="Popov\ZfcUser\Model\Repository\UserRepository")
 * @ORM\Table(name="user")
 */
class User {

    use DomainAwareTrait;

    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=100, nullable=false)
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(name="password", type="string", length=32, nullable=false)
     */
	private $password;

    /**
     * @var string
     * @ORM\Column(name="firstName", type="string", nullable=false)
     */
	private $firstName;

    /**
     * @var string
     * @ORM\Column(name="lastName", type="string", nullable=false)
     */
	private $lastName;

    /**
     * @var string
     * @ORM\Column(name="patronymic", type="string", nullable=false)
     */
	private $patronymic;

    /**
     * @var string
     * @ORM\Column(name="phone", type="string", length=13, nullable=false)
     */
	private $phone;

    /**
     * @var string
     * @ORM\Column(name="phoneWork", type="string", length=13, nullable=false)
     */
	private $phoneWork;

    /**
     * @var string
     * @ORM\Column(name="phoneInternal", type="string", length=13, nullable=false)
     */
	private $phoneInternal;

    /**
     * @var string
     * @ORM\Column(name="post", type="string", nullable=false)
     */
	private $post;

    /**
     * @var \DateTime
     * @ORM\Column(name="birthedAt", type="datetime", nullable=true)
     */
    private $birthedAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="employedAt", type="datetime", nullable=true)
     */
	private $employedAt;

    /**
     * @var string
     * @ORM\Column(name="photo", type="string", nullable=false)
     */
	private $photo;

    /**
     * @var string
     * @ORM\Column(name="notation", type="string", nullable=false)
     */
	private $notation;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	//private $cities;

    /**
     * Many Users have Many Roles.
     *
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Popov\ZfcRole\Model\Role", inversedBy="users", cascade={"persist"})
     * @ORM\JoinTable(name="users_roles")
     */
	private $roles;

	/**
	 * Constructor
	 */
	public function __construct() {
		#$this->cities = new ArrayCollection();
		$this->roles = new ArrayCollection();
	}

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getPatronymic()
    {
        return $this->patronymic;
    }

    /**
     * @param string $patronymic
     * @return User
     */
    public function setPatronymic($patronymic)
    {
        $this->patronymic = $patronymic;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneWork()
    {
        return $this->phoneWork;
    }

    /**
     * @param string $phoneWork
     * @return User
     */
    public function setPhoneWork($phoneWork)
    {
        $this->phoneWork = $phoneWork;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneInternal()
    {
        return $this->phoneInternal;
    }

    /**
     * @param string $phoneInternal
     * @return User
     */
    public function setPhoneInternal($phoneInternal)
    {
        $this->phoneInternal = $phoneInternal;

        return $this;
    }

    /**
     * @return string
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param string $post
     * @return User
     */
    public function setPost($post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBirthedAt()
    {
        return $this->birthedAt;
    }

    /**
     * @param \DateTime $birthedAt
     * @return User
     */
    public function setBirthedAt($birthedAt)
    {
        $this->birthedAt = $birthedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEmployedAt()
    {
        return $this->employedAt;
    }

    /**
     * @param \DateTime $employedAt
     * @return User
     */
    public function setEmployedAt($employedAt)
    {
        $this->employedAt = $employedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param string $photo
     * @return User
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return string
     */
    public function getNotation()
    {
        return $this->notation;
    }

    /**
     * @param string $notation
     * @return User
     */
    public function setNotation($notation)
    {
        $this->notation = $notation;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param ArrayCollection $roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @param Collection $roles
     */
    public function addRoles(Collection $roles)
    {
        foreach ($roles as $role) {
            $role->getUsers()->add($this);
            $this->roles->add($role);
        }
    }

    public function removeRoles(Collection $roles)
    {
        foreach ($roles as $role) {
            $role->getUsers()->clear();
            $this->roles->removeElement($role);
        }
    }
}
