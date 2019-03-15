<?php

namespace App\Entity\Shop;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Shop\BasketItem;
use App\Common\TrackInterface;
use App\Common\TrackableInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Shop\BasketRepository")
 */
class Basket implements TrackableInterface
{
    private static $trackFieldList = [
        'fio', 'phone', 'email', 'zip', 'commentUser', 'commentManager', 'status', 'cancelReason', 'coupon', 'user', 'invoiced', 'payed'
    ];
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Collection|BasketTrack[]
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
    
    /**
     * @var boolean 
     * 
     * @ORM\Column(type="boolean")
     */
    private $invoiced;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $invoicedAt;
    
    /* ... */

    
    public function __construct()
    {
        $this->invoiced = false;
    }
   
    public function getId()
    {
        return $this->id;
    }

    public function isInvoiced()
    {
        return $this->invoiced;
    }

    public function setInvoiced($invoiced)
    {
        if ($invoiced && !$this->invoicedAt) {
            $this->invoicedAt = new \DateTime();
        }
        $this->invoiced = $invoiced;
    }

    /**
     * @return \DateTime
     */
    public function getInvoicedAt(): \DateTime
    {
        return $this->invoicedAt;
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
        $trackFieldName = $track->getFieldName();
        if ($track->getAction() == TrackInterface::ACTION_INSERT && $this->isInvoiced()) {
            $track->setTitle('Создание заказа');
        } else if (!$this->isInvoiced() || $track->getAction() == TrackInterface::ACTION_DELETE) {
            return false;
        } else if ($track->getAction() == TrackInterface::ACTION_UPDATE) {
            $oldValue = $track->getValueOldView();
            $newValue = $track->getValueNewView();
            $track->setDescription('«'.$oldValue.'» на «'.$newValue.'»');
            switch ($trackFieldName) {
                case 'fio': $track->setTitle('Изменение ФИО'); break;
                case 'phone': $track->setTitle('Изменение телефона'); break;
                case 'email': $track->setTitle('Изменение E-mail'); break;
                case 'zip': $track->setTitle('Изменение индекса'); break;
                case 'commentUser': $track->setTitle('Изменение комментария покупателя'); break;
                case 'commentManager': $track->setTitle('Изменение комментария менеджера'); break;
                case 'status':
                    $track->setTitle('Изменение статуса');
                    $track->setDescription('«'.self::getStatusTitle($oldValue).'» на «'.self::getStatusTitle($newValue).'»');
                    break;
                case 'cancelReason': $track->setTitle('Изменение причины отмены'); break;
                case 'coupon': $track->setTitle('Изменение купона'); break;
                case 'user': $track->setTitle('Изменение покупателя'); break;
                case 'invoiced':
                    $track->setTitle('Создание заказа');
                    $track->setDescription('');
                    break;
                case 'payed':
                    $track->setTitle('Оплата заказа');
                    $track->setDescription($newValue ? 'Да' : 'Нет');
                break;
                default: $track->setTitle('Изменение поля «'.$trackFieldName.'»');
            }
        }
        return true;
    }
    
    /**
     * @return Basket
     */
    public function getBasket()
    {
        return $this;
    }

    /**
     * @return BasketTrack[]|ArrayCollection|Collection
     */
    public function getTracks()
    {
        return $this->tracks;
    }
    
    public function onAddTrackDelete($deletedEntities)
    {
        return null;
    }
    
}
