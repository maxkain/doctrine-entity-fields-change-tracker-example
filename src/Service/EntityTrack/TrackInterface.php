<?php

namespace App\Service\EntityTrack;

interface TrackInterface
{
    const ACTION_UPDATE = 0;
    const ACTION_INSERT = 1;
    const ACTION_DELETE = 2;

    public function setAction($action);
    public function getAction();

    public function setEntity($entity);

    public function setUser($user);

    public function setFieldName($fieldName);
    public function getFieldName();

    public function setValueOld($value);
    public function getValueOld();

    public function setValueOldView($value);
    public function getValueOldView();

    public function setValueNew($value);
    public function getValueNew();

    public function setValueNewView($value);
    public function getValueNewView();
}

