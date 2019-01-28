<?php

namespace App\Entity\Shop;

use Doctrine\ORM\Mapping as ORM;
use App\Common\TrackInterface;
use App\Traits\TrackTrait;
use App\Entity\Catalog\Product\Product;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BasketTrackRepository")
 */
class BasketTrack implements TrackInterface
{    
    use TrackTrait;
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Basket
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\Shop\Basket", inversedBy="tracks")
     */
    private $basket;

    /**
     * @var Product
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\Catalog\Product\Product")
     */
    private $product;

    
    public function __construct()
    {
        $this->timestamp = new \DateTime();  
    }
    
    public function __toString()
    {
        return $this->getTitle();
    }


    public function getId()
    {
        return $this->id;
    }

    public function getBasket(): Basket
    {
        return $this->basket;
    }

    public function setBasket(Basket $basket)
    {
        $this->basket = $basket;
    }

    /**
     * @return Product|null
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Product|null $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }
    
    public function setAdditionalEntity($entity)
    {
        if ($entity instanceof BasketItem) {
            $this->product = $entity->getProduct();
        } else if ($entity instanceof Product) {
            $this->product = $entity;
        }
    }
    
}
