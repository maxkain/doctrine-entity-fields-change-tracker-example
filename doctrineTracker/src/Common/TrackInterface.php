<?php

namespace App\Common;

interface TrackInterface {
    
    const ACTION_UPDATE = 0;
    const ACTION_INSERT = 1;
    const ACTION_DELETE = 2;

    public function setAction($action);

    public function setEntity($entity);

    public function setUser($user);

    public function setFieldName($fieldName);
    public function setValueOld($value);
    public function setValueNew($value);
}

