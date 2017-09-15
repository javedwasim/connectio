<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class usermeta extends Model
{
    protected $guarded = array('id');
    protected $table = 'usersmeta';
}
