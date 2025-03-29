<?php

namespace classes;

interface NoSqlDBInterface
{
    public function addEvent(array $arEvent);

    public function cleanAllEvents();

    public function getAllEvents():array;

    public function getMostAppropriateEvent(array $searchParams);
}