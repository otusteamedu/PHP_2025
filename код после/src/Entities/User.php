<?php

namespace App\Entities;

class User {
    public $id;
    public $name;
    public $email;
    public $subscriptionType;
    
    public function __construct($id = null, $name, $email, $subscriptionType = 'free') {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->subscriptionType = $subscriptionType;
    }
}