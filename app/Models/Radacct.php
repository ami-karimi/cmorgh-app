<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Radacct extends Model
{
    protected $table = 'radacct';
    protected $guarded = ['id'];
    public $timestamps = false;
}
