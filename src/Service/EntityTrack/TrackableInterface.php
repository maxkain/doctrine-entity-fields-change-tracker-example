<?php

namespace App\Service\EntityTrack;


/**
 * Tracking entity deletions, insertions and field changes
 */
interface TrackableInterface
{
    /**
     * @return TrackableInterface The Entity for tracking. Will be tracked to Entity class with 'Track' suffix
     */
    public function getTrackEntity();

}
