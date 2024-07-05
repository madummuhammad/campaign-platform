<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DapilTransaction extends Model
{
  protected $guarded = [];

  function dapil()
  {
    return $this->belongsTo('App\Models\Dapil');
  }

  function city()
  {
    return $this->belongsTo('App\Models\City');
  }
}
