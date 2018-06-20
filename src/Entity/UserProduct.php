<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserProductRepository")
 */
class UserProduct
{
    /**
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * @ORM\Column(type="boolean", options={"default" = 0})
     */
    private $is_downloaded;

    public function getIsDownloaded(): ?bool
    {
        return $this->is_downloaded;
    }

    public function setIsDownloaded(bool $is_downloaded): self
    {
        $this->is_downloaded = $is_downloaded;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }
    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }
}
