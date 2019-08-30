<?php


namespace App\Service\EntityTrack;

use App\Service\EntityTrack\TrackableInterface;
use App\Service\EntityTrack\TrackInterface;


interface TrackHandlerInterface
{

    /**
     * @param TrackInterface $track
     * @param TrackableInterface $entity
     * @return bool Persist track or not
     */
    public function handle(TrackInterface $track, TrackableInterface $entity) : bool;

    /**
     * @param $fieldName mixed
     * @param $value mixed New value of field
     * @return bool Persist track or not on field update
     */
    public function isUpdateTrackable($fieldName, $value);

}