<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    const TABLE_NAME = 'events';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'number', 'is_done',
    ];
}
