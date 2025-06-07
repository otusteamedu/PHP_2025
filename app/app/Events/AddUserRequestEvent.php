<?php

namespace App\Events;


class AddUserRequestEvent extends Event
{
    public string $number;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $number)
    {
        $this->number = $number;
    }
}
