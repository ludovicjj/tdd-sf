<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvitationCodeRepository")
 * @UniqueEntity("code")
 */
class InvitationCode
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=5)
     * @Assert\Regex("/^[0-9]{5}$/")
     * @Assert\NotBlank()
     */
    private $code;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expire_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getExpireAt(): ?\DateTimeInterface
    {
        return $this->expire_at;
    }

    public function setExpireAt(\DateTimeInterface $expire_at): self
    {
        $this->expire_at = $expire_at;

        return $this;
    }
}