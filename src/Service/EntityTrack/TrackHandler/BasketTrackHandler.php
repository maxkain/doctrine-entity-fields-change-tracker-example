<?php


namespace App\Service\EntityTrack\TrackHandler;

use App\Entity\Shop\BasketTrack;
use App\Entity\Shop\Basket;
use App\Service\Delivery\DeliveryManager;
use App\Service\EntityTrack\AbstractTrackHandler;
use App\Service\EntityTrack\TrackableInterface;
use App\Service\EntityTrack\TrackInterface;
use App\Service\EntityTrack\TrackHandler\BasketItemTrackHandler;
use App\Service\Payment\PaymentManager;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class BasketTrackHandler extends AbstractTrackHandler
{
    /**
     * @var BasketItemTrackHandler
     */
    private $basketItemTrackHandler;

    /**
     * @var DeliveryManager
     */
    private $deliveryManager;

    /**
     * @var PaymentManager
     */
    private $paymentManager;


    public function __construct(ContainerBagInterface $containerBag, BasketItemTrackHandler $basketItemTrackHandler, DeliveryManager $deliveryManager, PaymentManager $paymentManager)
    {
        $this->trackConfig = $containerBag->get('entity_track')['entity']['Basket'];
        $this->basketItemTrackHandler = $basketItemTrackHandler;
        $this->deliveryManager = $deliveryManager;
        $this->paymentManager = $paymentManager;
    }

    /**
     * @param BasketTrack|TrackInterface $track
     * @param Basket|TrackableInterface $entity
     * @param array $changeset
     * @return bool
     */
    public function handle(TrackInterface $track, TrackableInterface $entity, $changeset): bool
    {
        if (!$entity->isInvoiced() || $track->getAction() == TrackInterface::ACTION_DELETE) {
            return false;
        }

        if ($track->getAction() == TrackInterface::ACTION_UPDATE) {
            if ($track->getFieldName() == 'delivery') {
                $track->setValueOldView($this->deliveryManager->codeToTitle($track->getValueOld()));
                $track->setValueNewView($this->deliveryManager->codeToTitle($track->getValueNew()));
            }
            if ($track->getFieldName() == 'payment') {
                $track->setValueOldView($this->paymentManager->codeToTitle($track->getValueOld()));
                $track->setValueNewView($this->paymentManager->codeToTitle($track->getValueNew()));
            }
            if ($track->getFieldName() == 'pickupPoint') {
                $deliveryOld = $entity->getDelivery();
                $deliveryNew = $entity->getDelivery();
                if (isset($changeset['delivery'])) {
                    $deliveryOld = $changeset['delivery'][0];
                    $deliveryNew = $changeset['delivery'][1];
                }
                $track->setValueOldView($this->deliveryManager->pickupPointToTitle($deliveryOld, $track->getValueOld()));
                $track->setValueNewView($this->deliveryManager->pickupPointToTitle($deliveryNew, $track->getValueNew()));
            }
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
