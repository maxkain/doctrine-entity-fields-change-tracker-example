<?php

namespace App\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\EntityManager;
use App\Service\EntityTrack\TrackInterface;
use App\Service\EntityTrack\TrackableInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Service\EntityTrack\TrackHandlers;

class EntityTrackListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    
    /**
     * @var EntityManager 
     */
    private $em;

    /**
     * @var TrackHandlers
     */
    private $trackHandlers;


    public function __construct(TokenStorageInterface $token, TrackHandlers  $trackHandlers)
    {
        $this->tokenStorage = $token;
        $this->trackHandlers = $trackHandlers;
    }

    private function addTrack(TrackableInterface $entity, $action, $user, $changeFieldName = null, $changeFieldValues = [])
    {
        $entityClass = $this->em->getMetadataFactory()->getMetadataFor(get_class($entity))->getName();
        $handler = $this->trackHandlers->get($entityClass);
        if ($changeFieldName && !$handler->isUpdateTrackable($changeFieldName, $changeFieldValues[1])) {
            return false;
        }

        $trackClass = $entity->getTrackClass();
        $uow = $this->em->getUnitOfWork();
        $trackMetaData = $this->em->getMetadataFactory()->getMetadataFor($trackClass);

        /** @var $track TrackInterface  */
        $track = new $trackClass();
        $track->setEntity($entity);
        $track->setAction($action);
        $track->setUser($user);
        if ($changeFieldName) {
            $track->setFieldName($changeFieldName);
            $track->setValueOld($changeFieldValues[0]);
            $track->setValueNew($changeFieldValues[1]);
        }

        if ($handler->handle($track, $entity)) {
            $uow->persist($track);
            $uow->computeChangeSet($trackMetaData, $track);
            return true;
        }

        return false;
    }
    
    private function handleEntity(TrackableInterface $entity, $action, $user)
    {
        $uow = $this->em->getUnitOfWork();
        $trackClass = $entity->getTrackClass();
        if ($action == TrackInterface::ACTION_UPDATE) {
            $changeset = $uow->getEntityChangeSet($entity);
            if (!is_array($changeset)) {
                return;
            }
            $trackMetaData = $this->em->getMetadataFactory()->getMetadataFor($trackClass);
            foreach ($changeset as $changeFieldName => $changeFieldValues) {
                $this->addTrack($entity, $action, $user, $changeFieldName, $changeFieldValues, $trackMetaData);
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
