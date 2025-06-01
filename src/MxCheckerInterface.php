<?php
namespace Elisad5791\Phpapp;

interface MxCheckerInterface
{
    public function checkMxRecord(string $domain): bool;
}