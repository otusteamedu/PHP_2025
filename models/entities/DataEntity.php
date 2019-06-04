<?php

namespace app\models\entities;


use app\models\Models;

abstract class DataEntity extends Models
{
    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {

            $this->$property = $value;

            if(!in_array($property, $this->changes)){

                array_push($this->changes,$property);
            }
        }
    }
}