<?php

namespace App\Service\EntityTrack;


/**
 * Tracking entity deletions, insertions and field changes
 */
interface TrackableInterface
{
    /**
     * @return string The class of the track entity
     */
    public function getTrackClass();

}
