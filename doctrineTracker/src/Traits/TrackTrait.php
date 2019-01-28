<?php

namespace App\Traits;

use App\Entity\User;


trait TrackTrait
{
    
    /**
     * @var smallint
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
     * @var string 
     * 
     * @ORM\Column(type="text", nullable=true)
     */    
    private $title;
    
    /**
     * @var string 
     * 
     * @ORM\Column(type="text", nullable=true)
     */    
    private $description;
    
    /** 
     * @var \DateTime 
     * 
     * @ORM\Column(type="datetime") 
     */  
    private $timestamp;

    private $valueOldView;

    private $valueNewView;
    
    
    public function getAction()
    {
        return $this->action;
    }
    
    /**
     * @return User[]|null
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
     * @param User[]|null $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    public function setValueOld($valueOld)
    {
        if (is_object($valueOld)) {
            $this->valueOld = $valueOld->getId();
            if (is_callable([$valueOld, 'getValueForTrack'])) {
                $this->valueOldView = $valueOld->getValueForTrack();
            } else {
                $this->valueOldView = (string) $valueOld;
            }
        } else {
            $this->valueOld = $valueOld;
            $this->valueOldView = $valueOld;
        }
    }

    public function setValueNew($valueNew)
    {
        if (is_object($valueNew)) {
            $this->valueNew = $valueNew->getId();
            if (is_callable([$valueNew, 'getValueForTrack'])) {
                $this->valueNewView = $valueNew->getValueForTrack();
            } else {
                $this->valueNewView = (string) $valueNew;
            }
        } else {
            $this->valueNew = $valueNew;
            $this->valueNewView = $valueNew;
        }
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

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setDescription($description)
    {
        $this->description = $description;
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
