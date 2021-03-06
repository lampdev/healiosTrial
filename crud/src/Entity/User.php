<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements EntityInterface, UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int|null
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity=Role::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     * @var Role
     */
    private $role;

    /**
     * @ORM\OneToMany(targetEntity=AccessToken::class, mappedBy="user", orphanRemoval=true)
     * @var Collection|AccessToken[]
     */
    private $accessTokens;

    public function __construct()
    {
        $this->accessTokens = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

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
     * @return $this
     */
    public function setEmail(string $email): self
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
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Role
     */
    public function getRole(): Role
    {
        return $this->role;
    }

    /**
     * @param Role $role
     * @return $this
     */
    public function setRole(Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public function getSalt(): string
    {
        return (string)getenv('SALT');
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->getEmail();
    }

    /**
     * @return $this
     */
    public function eraseCredentials(): self
    {
        return $this;
    }

    /**
     * @return Collection|AccessToken[]
     */
    public function getAccessTokens(): Collection
    {
        return $this->accessTokens;
    }

    /**
     * @param AccessToken $accessToken
     * @return $this
     */
    public function addAccessToken(AccessToken $accessToken): self
    {
        if (!$this->accessTokens->contains($accessToken)) {
            $this->accessTokens[] = $accessToken;
            $accessToken->setUser($this);
        }

        return $this;
    }

    /**
     * @param AccessToken $accessToken
     * @return $this
     */
    public function removeAccessToken(AccessToken $accessToken): self
    {
        if ($this->accessTokens->removeElement($accessToken)) {
            if ($accessToken->getUser() === $this) {
                $accessToken->setUser(null);
            }
        }

        return $this;
    }
}
