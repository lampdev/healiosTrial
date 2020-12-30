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

    // eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MDkzMzk2NTQsImV4cCI6MTYwOTM0MzI1NCwicm9sZXMiOltdLCJpZCI6MSwibmFtZSI6ImFkbWluIiwiZW1haWwiOiJhZG1pbkBlbWFpbC5jb20iLCJpc0FkbWluIjp0cnVlLCJ1c2VybmFtZSI6ImFkbWluQGVtYWlsLmNvbSJ9.qEE4aEIZENNPIv14R5X_iq-VAeOvMwqRGV1liLdCujdLXjNYNJi0WL1mK9Xzv3G1Sz8BS5rL75u162mYIo-vmbHgQzNYIUJKce3Xi0EYSvBxgujk2tpyQAe7ZqsqOLJ7k_p_lkdYltVEzH2l2TUCJPw_jH-2QQZ159Fjib6JLyMLN3UEm---PGKO9Im_8Kf2nMKzNi4Sl1CM7IvJmHCdovciGHmEtprD5KuYKhjRCVpmZk1YyqvsFwFYttWSPEHObP5v4vIMHQHj7K9V-tOHIXRwoms2V5xWA3OSJHdBaQdTrKZ6wTr0FmiLnpDu-Tq9JOc_Tv64YNB1vEyED_IIh5k7-hY4f4GgIJ9rq1reBSKyI6Rg5eL8mN2a6fBxV7N9jjq5GfR8Dum0O4O-wFoIQxLGQbcDH-W67Je90DD2FfWMTBuS8u2vrIItSqBv4zCROcAfXi0FCK50yh8Qo2hvrcTlJCARZg3nkdBWHbjXZwBp_GfLmzvMl5sRk7TtXtbVvPF8FtgEdcxEm-3skZnu_5u3O85oGqsdYL5-TZuHK7ty801ZoMITdvNFTKYjttdC7zx6n_t3sFV0KCMGn810OkrsSi5v7hfRfyFJSvAnrVB3NJWCouX8Hz845nsuc5MKVMaPrFL6zqqcaybvjGGjLVb6NYqkZ_4sasJsVaQ1x38

    /**
     * @return Collection|AccessToken[]
     */
    public function getAccessTokens(): Collection
    {
        return $this->accessTokens;
    }

    public function addAccessToken(AccessToken $accessToken): self
    {
        if (!$this->accessTokens->contains($accessToken)) {
            $this->accessTokens[] = $accessToken;
            $accessToken->setUser($this);
        }

        return $this;
    }

    public function removeAccessToken(AccessToken $accessToken): self
    {
        if ($this->accessTokens->removeElement($accessToken)) {
            // set the owning side to null (unless already changed)
            if ($accessToken->getUser() === $this) {
                $accessToken->setUser(null);
            }
        }

        return $this;
    }
}
