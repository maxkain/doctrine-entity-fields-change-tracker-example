<?php

namespace App\Common;

/**
 * Tracking entity delations, insertions and field changes
 */
interface TrackableInterface
{
    
    /**
     * @return string Description
     */
    public function getTrackEntityShortClass();

    /**
     * @return array
     */
    public function getTrackFieldList();
    
    /**
     * @param TrackInterface
     * @return boolean Do track or not
     */
    public function onSetTrackData($track);
    
    /**
     * @param array $deletedEntities
     */
    public function onAddTrackDelete($deletedEntities);
    
}
