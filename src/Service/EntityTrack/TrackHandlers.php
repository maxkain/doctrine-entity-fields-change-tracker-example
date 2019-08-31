<?php


namespace App\Service\EntityTrack;

use App\Service\EntityTrack\TrackHandlerInterface;
use App\Service\EntityTrack\TrackHandler\BasketTrackHandler;
use App\Service\EntityTrack\TrackHandler\BasketItemTrackHandler;


class TrackHandlers
{
    /**
     * @var array EntityName => handler
     */
    private $handlers;

    public function __construct(BasketTrackHandler $basketTrackHandler, BasketItemTrackHandler $basketItemTrackHandler)
    {
        $this->handlers = [
            'Basket' => $basketTrackHandler,
            'BasketItem' => $basketItemTrackHandler,
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