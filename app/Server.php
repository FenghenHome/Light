<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Server extends Model
{
    use SoftDeletes;
    
    public function region()
    {
        return $this->belongsTo('App\Region');
    }
}
