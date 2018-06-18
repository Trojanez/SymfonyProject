<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $phone;

    /**
     * @ORM\Column(type="boolean", options={"default" = 0})
     */
    private $is_subscribe;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="integer", options={"default" = 0})
     */
    private $downloads;

    public function getId()
    {
        return $this->id;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getIsSubscribe()
    {
        return $this->is_subscribe;
    }

    public function setIsSubscribe(string $is_subscribe): self
    {
        $this->is_subscribe = $is_subscribe;

        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate()
    {
        $this->date = new \DateTime();

        return $this;
    }
    public function getDownloads()
    {
        return $this->downloads;
    }

    public function setDownloads($downloads): self
    {
        $this->downloads = $downloads;

        return $this;
    }


}
