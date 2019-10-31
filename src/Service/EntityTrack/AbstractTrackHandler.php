<?php


namespace App\Service\EntityTrack;

use App\Service\EntityTrack\TrackInterface;
use App\Service\EntityTrack\TrackHandlerInterface;
use App\Service\EntityTrack\TrackableInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractTrackHandler implements TrackHandlerInterface
{
    /**
     * @var array
     */
    protected $trackConfig;


    public function getTrackTitle(TrackInterface $track)
    {
        if ($track->getAction() == TrackInterface::ACTION_DELETE) {
            return $this->trackConfig['delete']['title'] ?? null;
        } else if ($track->getAction() == TrackInterface::ACTION_INSERT) {
            return $this->trackConfig['new']['title'] ?? null;
        } else if ($track->getAction() == TrackInterface::ACTION_UPDATE) {
            $value = $track->getValueNew();
            $fieldConfig = $this->trackConfig['update'][$track->getFieldName()];
            if (isset($fieldConfig['values'][$value])) {
                return $fieldConfig['values'][$value]['title'];
            } else {
                return $fieldConfig['title'] ?? null;
            }
        }
    }

    public function isUpdateTrackable($fieldName, $value)
    {
        $fieldConfig = $this->trackConfig['update'][$fieldName] ?? null;

        if ($fieldConfig) {
            if (!isset($fieldConfig['value']) || in_array($value, array_keys($fieldConfig['values']))) {
                return true;
            }
        }

        return false;
    }

}
