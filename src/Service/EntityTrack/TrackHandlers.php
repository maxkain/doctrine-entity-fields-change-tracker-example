<?php


namespace App\Service\EntityTrack;

use App\Service\EntityTrack\TrackHandlerInterface;
use App\Service\EntityTrack\TrackHandler\BasketTrackHandler;
use App\Service\EntityTrack\TrackHandler\BasketItemTrackHandler;
use App\Entity\Shop\Basket;
use App\Entity\Shop\BasketItem;

class TrackHandlers
{
    /**
     * @var array EntityClass => handler
     */
    private $handlers;

    public function __construct(BasketTrackHandler $basketTrackHandler, BasketItemTrackHandler $basketItemTrackHandler)
    {
        $this->handlers = [
            Basket::class => $basketTrackHandler,
            BasketItem::class => $basketItemTrackHandler,
        ];
    }

    public function get($name) : ?TrackHandlerInterface
    {
        return $this->handlers[$name] ?? null;
    }

    public function getHandlers()
    {
        return $this->handlers;
    }

}