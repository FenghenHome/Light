<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use SoftDeletes;
    
    public function commodity()
    {
        return $this->hasOne('App\Commodity', 'id', 'commodity_id');
    }
}
