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
    private static $trackFieldList = [
        'count', 'price'
    ];

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
    
    /**
     * @var int 
     * 
     * @ORM\Column(type="smallint")
     */    
    private $count;
    /**
     * @var float Total price
     * 
     * @ORM\Column(type="decimal", precision=11, scale=2, nullable=true)
     */    
    private $price;

    /**
     * @var boolean; 
     */
    private $forDelete;
        
    /* ... */


    public function getId()
    {
        return $this->id;
    }
    
    /* ... */

    public function getTrackEntityShortClass()
    {
        return 'Basket';
    }
    
    public function getTrackFieldList()
    {
        return self::$trackFieldList;
    }
    
    /**
     * @param BasketTrack $track
     * @return bool
     */
    public function onSetTrackData($track)
    {
        $product = $track->getProduct();
        $productTitle = $product->getName().' (#'.$product->getId().')';
        if (!$this->getBasket()->isInvoiced() || $this->forDelete) {
            return false;
        } else if ($track->getAction() == TrackInterface::ACTION_DELETE) {
            $track->setTitle('Удален товар «'.$productTitle.'»');
        } else if ($track->getAction() == TrackInterface::ACTION_INSERT) {
            if ($track->getBasket()->getId()) {
                $track->setTitle('Добавлен товар «'.$productTitle.'»');
            } else {
                return false;
            }
        } else if ($track->getAction() == TrackInterface::ACTION_UPDATE) {
            $trackFieldName = $track->getFieldName();
            switch ($trackFieldName) {
                case 'count': $track->setTitle('Изменение количества товара «'.$productTitle.'»'); break;
                case 'price': $track->setTitle('Изменение цены товара «'.$productTitle.'»'); break;
                case 'discount': $track->setTitle('Изменение скидки товара «'.$productTitle.'»'); break;
                default: $track->setTitle('Изменение поля «'.$trackFieldName.'» товара «'.$productTitle.'»');
            }
            $track->setDescription('«'.$track->getValueOldView().'» на «'.$track->getValueNewView().'»');
        }
        
        return true;
    }
    
    /**
     * @return Basket
     */
    public function getBasket()
    {
        return $this->basket;
    }
    
    public function onAddTrackDelete($deletedEntities)
    {
        foreach ($deletedEntities as $deletedEntity) {
            if ($deletedEntity instanceof Basket && $deletedEntity->getId() == $this->basket->getId()) {
                $this->forDelete = true;
            } 
        }
        return null;
    }
    
}
