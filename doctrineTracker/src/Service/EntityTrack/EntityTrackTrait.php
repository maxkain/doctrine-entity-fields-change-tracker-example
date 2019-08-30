<?php

namespace App\Service\EntityTrack;

use App\Entity\User;
use App\Service\Helper;

trait EntityTrackTrait
{
    /**
     * @var int
     * 
     * @ORM\Column(type="smallint")
     */
    private $action;
    
    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true) 
     */
    private $user;
    
    /**
     * @var string 
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     */    
    private $fieldName;
    
    /**
     * @var string 
     * 
     * @ORM\Column(type="text", nullable=true)
     */    
    private $valueOld;
    
    /**
     * @var string 
     * 
     * @ORM\Column(type="text", nullable=true)
     */    
    private $valueNew;

    /** 
     * @var \DateTime 
     * 
     * @ORM\Column(type="datetime") 
     */  
    private $timestamp;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $valueOldView;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $valueNewView;
    
    
    public function getAction()
    {
        return $this->action;
    }
    
    /**
     * @return User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getValueOld()
    {
        return $this->valueOld;
    }

    public function getValueNew()
    {
        return $this->valueNew;
    }

    public function getTimestamp(): \DateTime
    {
        return $this->timestamp;
    }

    public function setAction($action)
    {
        $this->action = $action;
    }
    
    /**
     * @param User|null $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    private function toValue($value)
    {
        if (is_object($value)) {
            return $value->getId();
        } else {
            return $value;
        }
    }

    private function toValueView($value)
    {
        if (is_object($value)) {
            if (is_callable([$value, 'getValueForTrack'])) {
                return $value->getValueForTrack();
            } else {
                return (string) $value;
            }
        } else if (is_bool($value)) {
            return $value ? 'Да' : 'Нет';
        } else if ($value instanceof \DateTime) {
            return Helper::dateFormat($value, 'long');
        } else {
            return $value;
        }
    }

    public function setValueOld($value)
    {
        $this->valueOld = $this->toValue($value);
        $this->valueOldView = $this->toValueView($value);
    }

    public function setValueNew($value)
    {
        $this->valueNew = $this->toValue($value);
        $this->valueNewView = $this->toValueView($value);
    }
    
    public function getValueOldView()
    {
        return $this->valueOldView;
    }

    public function getValueNewView()
    {
        return $this->valueNewView;
    }

    public function setTimestamp(\DateTime $timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public function getFieldName()
    {
        return $this->fieldName;
    }

    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    }
     
}
