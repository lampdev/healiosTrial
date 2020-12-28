<?php

namespace App\Entity;

use App\Repository\RefreshTokenRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RefreshTokenRepository::class)
 * @ORM\Table(name="refresh_tokens")
 */
class RefreshToken
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     * @var string
     */
    private $refresh_token;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $username;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTimeInterface
     */
    private $valid;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refresh_token;
    }

    /**
     * @param string $refresh_token
     * @return $this
     */
    public function setRefreshToken(string $refresh_token): self
    {
        $this->refresh_token = $refresh_token;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getValid(): DateTimeInterface
    {
        return $this->valid;
    }

    /**
     * @param DateTimeInterface $valid
     * @return $this
     */
    public function setValid(DateTimeInterface $valid): self
    {
        $this->valid = $valid;

        return $this;
    }
}
