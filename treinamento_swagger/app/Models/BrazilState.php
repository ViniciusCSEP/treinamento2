<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrazilState extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'state'
    ];

    public $timestamps = false;
}
