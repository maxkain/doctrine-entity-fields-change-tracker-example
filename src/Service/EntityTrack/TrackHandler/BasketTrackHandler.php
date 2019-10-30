<?php


namespace App\Service\EntityTrack\TrackHandler;

use App\Entity\Shop\BasketTrack;
use App\Entity\Shop\Basket;
use App\Service\EntityTrack\AbstractTrackHandler;
use App\Service\EntityTrack\TrackableInterface;
use App\Service\EntityTrack\TrackInterface;
use App\Service\EntityTrack\TrackHandler\BasketItemTrackHandler;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class BasketTrackHandler extends AbstractTrackHandler
{
    /**
     * @var BasketItemTrackHandler
     */
    private $basketItemTrackHandler;


    public function __construct(ContainerBagInterface $containerBag, BasketItemTrackHandler $basketItemTrackHandler)
    {
        $this->trackConfig = $containerBag->get('entity_track')['entity']['Basket'];
        $this->basketItemTrackHandler = $basketItemTrackHandler;
    }

    /**
     * @param BasketTrack|TrackInterface $track
     * @param Basket|TrackableInterface $entity
     * @return bool
     */
    public function handle(TrackInterface $track, TrackableInterface $entity): bool
    {
        if (!$entity->isInvoiced() || $track->getAction() == TrackInterface::ACTION_DELETE) {
            return false;
        }

        return true;
    }

    public function getBasketTrackTitle(BasketTrack $basketTrack)
    {
        if ($basketTrack->getProduct()) {
            return $this->basketItemTrackHandler->getTrackTitle($basketTrack);
        } else {
            return $this->getTrackTitle($basketTrack);
        }
    }
}
