<?php

namespace App\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\EntityManager;
use App\Common\TrackInterface;
use App\Common\TrackableInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TrackListener
{

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    
    /**
     * @var EntityManager 
     */
    private $em;
    
    
    function __construct(TokenStorageInterface $token)
    {
        $this->tokenStorage = $token;
    }

    private function addTrack(TrackableInterface $entity, $action, $user, $changeFieldName = null, $changeFieldValues = [], $trackMetaData = null)
    {
        $trackEntity = $entity->getTrackEntity();
        $trackClass = get_class($trackEntity).'Track';
        $uow = $this->em->getUnitOfWork();
        if (!$trackMetaData) {
            $trackMetaData = $this->em->getMetadataFactory()->getMetadataFor($trackClass);
        }
        /**
         * @var $track TrackInterface
         */
        $track = new $trackClass();
        $track->setEntity($entity);
        $track->setAction($action);
        $track->setUser($user);
        if ($changeFieldName) {
            $track->setFieldName($changeFieldName);
            $track->setValueOld($changeFieldValues[0]);
            $track->setValueNew($changeFieldValues[1]);
        }
        if ($action == TrackInterface::ACTION_DELETE) {
            $entity->onAddTrackDelete($uow->getScheduledEntityDeletions());
        }
        if ($entity->onSetTrackData($track)) {
            $uow->persist($track);
            $uow->computeChangeSet($trackMetaData, $track);
        }        
    }
    
    private function handleEntity(TrackableInterface $entity, $action, $user)
    {
        $uow = $this->em->getUnitOfWork();
        $trackEntity = $entity->getTrackEntity();
        if ($trackEntity === null) {
            return;
        }
        $trackClass = get_class($trackEntity).'Track';
        if ($action == TrackInterface::ACTION_UPDATE) {
            $changeset = $uow->getEntityChangeSet($entity);
            if (!is_array($changeset)) {
                return;
            }
            $trackMetaData = $this->em->getMetadataFactory()->getMetadataFor($trackClass);
            $changeFieldList = $entity->getTrackFieldList();
            foreach ($changeset as $changeFieldName => $changeFieldValues) {
                if (in_array($changeFieldName, $changeFieldList)) {
                    $this->addTrack($entity, $action, $user, $changeFieldName, $changeFieldValues, $trackMetaData);
                } 
            }
        } else {
            $this->addTrack($entity, $action, $user);            
        }
    }
    
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $this->em = $em;
        $uow = $em->getUnitOfWork();
        $token = $this->tokenStorage->getToken();
        $user = null;
        if ($token) {
            $user = $token->getUser();
            if ($user == 'anon.') {
                $user = null;
            }
        }
        
        //changed fields of entities
        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof TrackableInterface) {
                $this->handleEntity($entity, TrackInterface::ACTION_UPDATE, $user);
            }
        }
        
        //inserted entities
        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof TrackableInterface) {
                $this->handleEntity($entity, TrackInterface::ACTION_INSERT, $user);
            }
        }

        //deleted entities
        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof TrackableInterface) {
                $this->handleEntity($entity, TrackInterface::ACTION_DELETE, $user);
            }
        }
    }

}