<?php

namespace App\Service\EntityTrack;


/**
 * Tracking entity deletions, insertions and field changes
 */
interface TrackableInterface
{
    /**
     * @return TrackableInterface
     */
    public function getTrackEntity();

}
