<?php

namespace App\Entity\Shop;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Shop\Basket;
use App\Common\TrackInterface;
use App\Common\TrackableInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Shop\BasketItemRepository")
 */
class BasketItem implements TrackableInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     */
    private $id;
    
    /**
     * @var Basket
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\Shop\Basket", inversedBy="basketItems")
     */
    private $basket;
        
    /* ... */

    public function getId()
    {
        return $this->id;
    }
    
    /* ... */

    /**
     * @return TrackableInterface
     */
    public function getTrackEntity()
    {
        return $this->basket;
    }
    
}
