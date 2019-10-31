<?php


namespace App\Service\EntityTrack\TrackHandler;

use App\Entity\Shop\Basket;
use App\Entity\Shop\BasketTrack;
use App\Entity\Shop\BasketItem;
use App\Service\EntityTrack\TrackableInterface;
use App\Service\EntityTrack\TrackInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use App\Service\EntityTrack\AbstractTrackHandler;

class BasketItemTrackHandler extends AbstractTrackHandler
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;


    public function __construct(EntityManagerInterface $em, ContainerBagInterface $containerBag)
    {
        $this->em = $em;
        $this->trackConfig = $containerBag->get('entity_track')['entity']['BasketItem'];
    }

    /**
     * @param BasketTrack|TrackInterface $track
     * @param BasketItem|TrackableInterface $entity
     * @param array $changeset
     * @return bool
     */
    public function handle(TrackInterface $track, TrackableInterface $entity, $changeset): bool
    {
        if (!$entity->getBasket() || !$entity->getBasket()->isInvoiced()) {
            return false;
        }

        $deletedEntities = $this->em->getUnitOfWork()->getScheduledEntityDeletions();
        /** @var TrackableInterface $deletedEntity */
        foreach ($deletedEntities as $deletedEntity) {
            if ($deletedEntity instanceof Basket && $deletedEntity->getId() == $entity->getBasket()->getId()) {
                return false;
            }
        }

        if ($track->getAction() == TrackInterface::ACTION_INSERT) {
            $track->setValueNew($entity->getCount());
        }

        return true;
    }

}
