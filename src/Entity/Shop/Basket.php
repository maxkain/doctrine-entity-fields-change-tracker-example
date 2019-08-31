<?php

namespace App\Entity\Shop;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Shop\BasketItem;
use App\Common\TrackInterface;
use App\Common\TrackableInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Shop\BasketRepository")
 */
class Basket implements TrackableInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var BasketTrack[]|Collection
     * 
     * @ORM\OneToMany(targetEntity="App\Entity\Shop\BasketTrack", mappedBy="basket", cascade={"remove"})
     */
    private $tracks;

    /**
     * @var BasketItem[]|Collection
     * 
     * @ORM\OneToMany(targetEntity="App\Entity\Shop\BasketItem", mappedBy="basket", orphanRemoval=true, cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $basketItems;
    
    /* ... */

    
    public function __construct()
    {
        $this->basketItems = new ArrayCollection();
    }
   
    public function getId()
    {
        return $this->id;
    }

    /* ... */

    /**
     * @return string
     */
    public function getTrackClass()
    {
        return get_class($this).'Track';
    }
        
}
