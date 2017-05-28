<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commodity extends Model
{
    use SoftDeletes;
    
    // 查看该商品归属的 region
    public function region()
    {
        return $this->belongsTo('App\Region');
    }
}
