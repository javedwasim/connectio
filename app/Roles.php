<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $guarded = array('id');
    protected $table = 'roles';
}
