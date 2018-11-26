<?php

namespace Popov\ZfcUser\Model;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Popov\ZfcCore\Model\DomainAwareTrait;
use Popov\ZfcRole\Model\Role;

/**
 * @ORM\Entity(repositoryClass="Popov\ZfcUser\Model\Repository\UserRepository")
 * @ORM\Table(name="user")
 */
class User
{
    const MNEMO = 'user';

    const TABLE = 'user';

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
     * @ORM\Column(name="email", type="string", length=101, nullable=false)
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
     * @ORM\Column(name="patronymic", type="string", nullable=true)
     */
    private $patronymic;

    /**
     * @var string
     * @ORM\Column(name="phone", type="string", length=13, nullable=true)
     */
    private $phone;

    /**
     * @var string
     * @ORM\Column(name="phoneWork", type="string", length=13, nullable=true)
     */
    private $phoneWork;

    /**
     * @var string
     * @ORM\Column(name="phoneInternal", type="string", length=13, nullable=true)
     */
    private $phoneInternal;

    /**
     * @var string
     * @ORM\Column(name="post", type="string", nullable=true)
     */
    private $post;

    /**
     * @var DateTime
     * @ORM\Column(name="birthedAt", type="datetime", nullable=true)
     */
    private $birthedAt;

    /**
     * @var DateTime
     * @ORM\Column(name="createdAt", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var DateTime
     * @ORM\Column(name="employedAt", type="datetime", nullable=true)
     */
    private $employedAt;

    /**
     * @var string
     * @ORM\Column(name="photo", type="string", nullable=true)
     */
    private $photo;

    /**
     * Can user access to inner system
     * By default set to "false" that meat you should obviously set "true" for users witch should have access to inner
     * system
     *
     * @var bool
     * @ORM\Column(name="isInner", type="integer", length=1, nullable=false)
     */
    private $isInner = 0;

    /**
     * @var string
     * @ORM\Column(name="notation", type="string", nullable=true)
     */
    private $notation;

    /**
     * Many Users have Many Roles.
     *
     * @var Role[]
     * @ORM\ManyToMany(targetEntity="Popov\ZfcRole\Model\Role", inversedBy="users", cascade={"persist"})
     * @ORM\JoinTable(name="users_roles",
     *    joinColumns={@ORM\JoinColumn(name="userId", referencedColumnName="id")},
     *    inverseJoinColumns={@ORM\JoinColumn(name="roleId", referencedColumnName="id")}
     * )
     */
    private $roles;

    /**
     * @var Pool
     * @ORM\ManyToOne(targetEntity="Stagem\Pool\Model\Pool", inversedBy="users", cascade={"persist", "remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="poolId", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     */
    #private $pool;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return User
     */
    public function setId(int $id): User
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return User
     */
    public function setFirstName(string $firstName): User
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return User
     */
    public function setLastName(string $lastName): User
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getPatronymic(): string
    {
        return $this->patronymic;
    }

    /**
     * @param string $patronymic
     * @return User
     */
    public function setPatronymic(string $patronymic): User
    {
        $this->patronymic = $patronymic;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return User
     */
    public function setPhone(string $phone): User
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneWork(): string
    {
        return $this->phoneWork;
    }

    /**
     * @param string $phoneWork
     * @return User
     */
    public function setPhoneWork(string $phoneWork): User
    {
        $this->phoneWork = $phoneWork;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneInternal(): string
    {
        return $this->phoneInternal;
    }

    /**
     * @param string $phoneInternal
     * @return User
     */
    public function setPhoneInternal(string $phoneInternal): User
    {
        $this->phoneInternal = $phoneInternal;

        return $this;
    }

    /**
     * @return string
     */
    public function getPost(): string
    {
        return $this->post;
    }

    /**
     * @param string $post
     * @return User
     */
    public function setPost(string $post): User
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getBirthedAt(): DateTime
    {
        return $this->birthedAt;
    }

    /**
     * @param DateTime $birthedAt
     * @return User
     */
    public function setBirthedAt(DateTime $birthedAt): User
    {
        $this->birthedAt = $birthedAt;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     * @return User
     */
    public function setCreatedAt(DateTime $createdAt): User
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEmployedAt(): DateTime
    {
        return $this->employedAt;
    }

    /**
     * @param DateTime $employedAt
     * @return User
     */
    public function setEmployedAt(DateTime $employedAt): User
    {
        $this->employedAt = $employedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    /**
     * @param string $photo
     * @return User
     */
    public function setPhoto(string $photo): User
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return bool
     */
    public function isInner(): bool
    {
        return $this->isInner;
    }

    /**
     * @param bool $isInner
     * @return User
     */
    public function setIsInner(bool $isInner): User
    {
        $this->isInner = $isInner;

        return $this;
    }

    /**
     * @return string
     */
    public function getNotation(): string
    {
        return $this->notation;
    }

    /**
     * @param string $notation
     * @return User
     */
    public function setNotation(string $notation): User
    {
        $this->notation = $notation;

        return $this;
    }

    /**
     * @return Role[]
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param Role[] $roles
     * @return User
     */
    public function setRoles($roles): User
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @param array|Collection $roles
     */
    public function addRoles($roles)
    {
        foreach ($roles as $role) {
            $role->getUsers()->add($this);
            $this->roles->add($role);
        }
    }

    public function removeRoles($roles)
    {
        foreach ($roles as $role) {
            $role->getUsers()->clear();
            $this->roles->removeElement($role);
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return trim($this->getFirstName() . ' ' . $this->getLastName());
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return trim($this->getName() . ' ' . $this->getPatronymic());
    }
}
